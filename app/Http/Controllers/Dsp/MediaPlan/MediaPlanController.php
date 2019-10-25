<?php

namespace Vanguard\Http\Controllers\Dsp\MediaPlan;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Models\Criteria;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanProgramRating;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Services\Inventory\StoreMediaPlanProgram;
use Vanguard\Services\MediaPlan\GetSuggestionListWithProgramRating;
use Vanguard\Services\MediaPlan\StoreMediaPlanVolumeDiscount;
use Vanguard\Services\MediaPlan\GetMediaPlans;
use Vanguard\Services\MediaPlan\SummarizePlan;
use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Exports\MediaPlanExport;
use Vanguard\Services\Traits\DefaultMaterialLength;
use Vanguard\Services\Traits\ListDayTrait;
use Vanguard\Libraries\Utilities;
use Log;
use Vanguard\Services\MediaPlan\StoreCampaign;
use Vanguard\Services\MediaPlan\StoreMpo;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Vanguard\Http\Requests\AssignFollowerRequest;
use Vanguard\Http\Requests\MediaPlan\CreateStationRatingRequest;
use Vanguard\Services\MediaPlan\GetSuggestionListByDuration;
use Vanguard\Libraries\Enum\MediaPlanStatus;
use Vanguard\Libraries\DayPartList;
use Vanguard\Services\MediaPlan\GetTargetAudience;
use Vanguard\Services\CampaignChannels\GetChannelByName;
use Vanguard\Services\User\GetUserList;
use Vanguard\Mail\MailForApproval;
use Vanguard\Mail\ApprovalNotification;
use Vanguard\Http\Requests\MediaPlan\StorePlanRequest;
use Vanguard\Http\Requests\MediaPlan\StorePlanSuggestionsRequest;
use Vanguard\Http\Resources\MediaPlanSuggestionCollection;
use Vanguard\Libraries\TimeBelt;
use Vanguard\Services\MediaPlan\StoreMediaPlanService;
use Vanguard\Services\MediaPlan\StoreMediaPlanSuggestionService;
use Vanguard\Models\Client;
use Vanguard\User;
use Vanguard\Services\Ratings\StoreMediaPlanDeliverables;
use Vanguard\Http\Resources\UserCollection;
use Vanguard\Http\Resources\MediaPlanResource;
use Vanguard\Http\Requests\MediaPlan\ClonePlanRequest;
use Vanguard\Services\MediaPlan\CloneMediaPlanService;
use Vanguard\Http\Resources\MediaPlanCollection;
use Vanguard\Services\MediaPlan\DeleteMediaPlanService;
use Vanguard\Libraries\ActivityLog\LogActivity;

class MediaPlanController extends Controller
{
    use ListDayTrait;
    use DefaultMaterialLength;
    use CompanyIdTrait;

    /**
     * Load up the page that displays the timebelts to be chosen etc.
     * The page will be empty.
     * @todo Make this a bit cleaner, move generation of selected timebelts to a service/resource
     * @todo Add the appropriate permissions
     */
    public function stationDetails(Request $request, $id)
    {
        $media_plan = MediaPlan::findOrFail($id);

        $routes = [
            'back_action' => route('agency.media_plans', [], false),
            'next_action' => route('agency.media_plan.create', ['id' => $media_plan->id], false),
            'save_action' => route('agency.media_plan.select_suggestions', ['id' => $media_plan->id], false),
            'new_ratings_action' => route('reach.get', ['id' => $media_plan->id], false),
            'timebelt_ratings' => route('reach.get-timebelts', ['plan_id' => $media_plan->id], false)
        ];
        $selected = new MediaPlanSuggestionCollection($media_plan->suggestions->where('status', 1));

        return view('agency.mediaPlan.display_suggestions')
            ->with('routes', $routes)
            ->with('filter_values', $this->getTimebeltFilters($media_plan))
            ->with('media_plan', $media_plan)
            ->with('selected', $selected)
            ->with('stations', [])
            ->with('total_graph', [])
            ->with('days', ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]);
    }

    /**
     * Return the values that should be used to populate the filter fields
     * i.e dayparts, and states (that were part of what was found)
     * @todo not the biggest fan of how this is done, but it should work for now
     */
    protected function getTimebeltFilters($media_plan) {
        //Get the states associated with the plan criteria
        //If no state is present, then grab the full list of states from the sub criterias table
        $state_list = json_decode($media_plan->criteria_state, true);
        if (!$state_list || count($state_list) == 0) {
            $state_criteria_model = Criteria::with('subCriterias')->where('name', 'states')->first();
            $state_list = $state_criteria_model->subCriterias->sortBy('name')->pluck('name');
        }
        $state_list = collect($state_list)->map(function($item) {
            return ["text" => $item, "value" => $item];
        })->prepend(["text" => "All", "value" => "all"]);

        //Setup the dayparts filter
        $day_parts = collect(DayPartList::DAYPARTS)->keys()->sort()->values()->map(function($item) {
            return ["text" => $item, "value" => $item];
        })->prepend(["text" => "All", "value" => "all"]);

        return [
            "day_part" => $day_parts->toArray(),
            "state" => $state_list->toArray(),
            "day" => [
                ["text" => "All", "value" => "all"],
                ["text" => "Monday", "value" => "Mon"],
                ["text" => "Tuesday", "value" => "Tue"],
                ["text" => "Wednesday", "value" => "Wed"],
                ["text" => "Thursday", "value" => "Thu"],
                ["text" => "Friday", "value" => "Fri"],
                ["text" => "Saturday", "value" => "Sat"],
                ["text" => "Sunday", "value" => "Sun"]
            ],
            "station_type" => [
                // ["text" => "All", "value" => "all"],
                ["text" => "Network", "value" => "Network"],
                ["text" => "Regional", "value" => "Regional"],
                ["text" => "Satellite", "value" => "International"]
            ]
        ];
    }

    /*
     * *************************** API METHODS *****************************
     */
    /**
     * Choose suggestions based on information from the frontend
     */
    public function storeSuggestions(StorePlanSuggestionsRequest $request, $id)
    {
        $validated = $request->validated();
        $media_plan = MediaPlan::findOrFail($id);

        $store_suggestions_service = new StoreMediaPlanSuggestionService($validated['data'], $media_plan);
        $data = $store_suggestions_service->run();

        $logactivity = new LogActivity($media_plan, "Stored Suggestions");
        $log = $logactivity->log();
        return new MediaPlanSuggestionCollection($data);
    }

    /**
     * ****************** BELOW ARE THE OLD METHODS *****************
     */
    public function index(Request $request)
    {
        $company_id = $this->companyId();
        $media_plan_service = new GetMediaPlans($request->status, $company_id);
        $plans = $media_plan_service->run();
        $clients = Client::with('brands')->filter(['company_id' => $this->companyId()])->get();
        return view('agency.mediaPlan.index')->with('plans', new MediaPlanCollection($plans))
                                ->with('clients', $clients);
    }

    public function criteriaForm(Request $request)
    {
        $criterias = Criteria::with(['subCriterias'])->get();
        $new_criterias = [];
        foreach ($criterias as $key => $criteria) {
            $sub_criterias = [];
            foreach ($criteria->subCriterias as $key => $value) {
                $sub_criterias[$key] = $value->name;
            }
            $new_criterias[$criteria->name]['criterias'] = $sub_criterias;
            $new_criterias[$criteria->name]['all'] = 'All';
        }
        $clients = Client::with('brands')->filter(['company_id' => $this->companyId()])->get();
        // return criterias array with the frontend view, in order to populate criteria inputs
        return view('agency.mediaPlan.create_plan')->with('criterias', $new_criterias)
                                                ->with('clients', $clients)
                                                ->with('redirect_urls', ['submit_form' => route('agency.media_plan.submit.criterias')]);
    }

    public function groupSuggestions($query)
    {
        $query = $query->groupBy(function ($item, $key) {
            return $item->station.'_'.$item->program.'_'.$item->start_time.'_'.$item->end_time;
        });
        $query = $query->map(function($item, $key) {
                        $count = count($item);
                        $item = $item->first();
                        $item->audience = $count;
                        return $item;
                    });
        $query = $query->flatten();
        $query = $query->sortByDesc('audience');
        return $query;
    }

    public function groupSuggestionsByStation($query)
    {
        return $query->groupBy('station');
    }

    public function countByMediaType($collection, $media_type='')
    {
        return $collection->where('media_type', $media_type)->sum('audience');
    }

    public function summary($media_plan_id)
    {
        $media_plan = MediaPlan::with(['client', 'brand', 'company'])->findorfail($media_plan_id);
        $selected_suggestions = $media_plan->suggestions->where('status', 1)->where('material_length', '!=', null);

        if (count($selected_suggestions) === 0) {
            // redirect to review suggestions page for user to select suggestions
            return redirect()->route('agency.media_plan.create', ['id'=> $media_plan->id]);
        }

        $media_plan_summary_result = (new SummarizePlan(new MediaPlanResource($media_plan)))->run();

        /*
        * This way of get users that have a particular permission is not the most efficient way to do this,
        * moving forward we will have to review this.
        */
        $users = User::where('status', 'Active')->permission(['approve.media_plan', 'decline.media_plan'])->get();
        $company_id =$this->companyId();
        $filtered_users = $users->filter(function ($item) use ($company_id){
            if($item->companies->first()->id == $company_id){
                return $item;
            }
        })->values();
        $users = new UserCollection($filtered_users);

        return view('agency.mediaPlan.summary')->with('formatted_plan', $media_plan_summary_result)
                ->with('users', $users);
    }

    public function exportPlan($media_plan_id)
    {
        $mediaPlan = MediaPlan::with(['client', 'brand', 'company'])->findorfail($media_plan_id);
        $selectedSuggestions = $mediaPlan->suggestions->where('status', 1)->where('material_length', '!=', null);

        if (count($selectedSuggestions) === 0) {
            // redirect to review suggestions page for user to select suggestions
            return redirect()->route('agency.media_plan.create', ['id'=> $mediaPlan->id]);
        }

        $export_name = str_slug($mediaPlan->campaign_name).'.xlsx';
        $formated_media_plan = (new SummarizePlan($mediaPlan))->run();

        $logactivity = new LogActivity($mediaPlan, "Export Media Plan");
        $log = $logactivity->log();

        return Excel::download(new MediaPlanExport($formated_media_plan), $export_name);
    }

    public function changeMediaPlanStatus(Request $request)
    {
        $media_plan_id = $request->media_plan_id;
        $action = $request->action;
        $mediaPlan = MediaPlan::findorfail($media_plan_id);
        $mediaPlan->status = $action;
        $mediaPlan->save();
        $this->sendRequestResponse($media_plan_id, $action);
        $mediaPlan = MediaPlan::with(['client'])->findorfail($request->media_plan_id);
        $selectedSuggestions = $mediaPlan->suggestions->where('status', 1)->where('material_length', '!=', null);

        $logactivity = new LogActivity($mediaPlan, "Media Plan". $action);
        $log = $logactivity->log();
        return response()->json([
            'status' => 'success',
            'data' =>  new MediaPlanResource($mediaPlan)
            ]);
    }

    public function totalAudienceFound($collection)
    {
        return $collection->sum('audience');
    }

    public function createPlan($id)
    {
        $media_plan = MediaPlan::find($id);
        if(!$media_plan){
            return redirect()->route("agency.media_plan.criteria_form");
        }
        $get_suggestion_with_ratings = new GetSuggestionListWithProgramRating($id);
        $plans = $get_suggestion_with_ratings->getMediaPlanSuggestionWithProgram();
        if(count($plans) == 0 ){
            return redirect()->route("agency.media_plan.criteria_form");
        }
        $suggestions = $this->groupSuggestions($plans);
        $suggestionsByStation = $this->groupSuggestionsByStation($plans);
        $dates = $this->dates($media_plan->start_date, $media_plan->end_date);
        $label_dates = $this->labelDates($media_plan->start_date, $media_plan->end_date);
        $days = $this->days($media_plan->start_date, $media_plan->end_date);

        $fayaFound = array(
            'total_tv' => $this->countByMediaType($suggestions, 'Tv'),
            'total_radio' => $this->countByMediaType($suggestions, 'Radio'),
            'programs_stations' => $this->initializePlansWithExposures($plans, $dates, $this->getDefaultMaterialLength()),
            'stations' => $suggestionsByStation,
            'total_audiences' => $this->totalAudienceFound($suggestions),
            'dates' => $dates,
            'labeldates' => $label_dates,
            'days' => $days,
        );
        $clients = Client::with('brands')->filter(['company_id' => $this->companyId()])->get();
        $logactivity = new LogActivity($media_plan , "customise Media Plan");
        $log = $logactivity->log();
        return view('agency.mediaPlan.complete_plan')->with('fayaFound', $fayaFound)
                                                    ->with('clients', $clients)
                                                    ->with('days', $this->listDays())
                                                    ->with('default_material_length', $this->getDefaultMaterialLength())
                                                    ->with('plan_id', $id)
                                                    ->with('media_plan', new MediaPlanResource($media_plan));
    }

    public function initializePlansWithExposures($suggestions, $dates, $durations)
    {
        foreach ($suggestions as $key => $suggestion) {
            $suggestion->exposures = [];
            foreach ($durations as $duration) {
                $suggestion->exposures[$duration] = $this->setExposuresByDuration($dates, $duration, $suggestion);
            }
        }
        return $suggestions;
    }

    public function setExposuresByDuration($dates, $duration, $suggestion)
    {
        $dates_arr = [];
        $total_exposures = 0;
        $net_total = 0;
        foreach ($dates as $date) {
            $dates_arr[$date] = 0;
            if ($suggestion->material_length != '') {
                $material_lengths = json_decode($suggestion->material_length, true);
                if (array_key_exists($duration, $material_lengths)) {
                    foreach ($material_lengths[$duration] as $exposure) {
                        if ($exposure['date'] == $date) {
                            $dates_arr[$date] = $exposure['slot'];
                            $total_exposures += (INT) $exposure['slot'];
                        }
                    }
                }
            }
        }
        return ['dates' => $dates_arr, 'total_exposures' => $total_exposures, 'net_total' => $net_total];
    }

    public function days($start, $end)
    {
       $diff = strtotime($end) - strtotime($start);
       $daysBetween = floor($diff/(60*60*24));
            $formattedDates = array();
            for ($i = 0; $i <= $daysBetween; $i++) {
                $tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
                $formattedDates[] = date('l', strtotime($tmpDate));
            }
            return $formattedDates;
    }

    public function labelDates($start, $end)
     {
        $diff = strtotime($end) - strtotime($start);

        $daysBetween = floor($diff/(60*60*24));

        $formattedDates = array();
        for ($i = 0; $i <= $daysBetween; $i++) {
            $tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
            $formattedDates[] = date('M d', strtotime($tmpDate));
        }
        return $formattedDates;
    }

    public function dates($start, $end) {
        $diff = strtotime($end) - strtotime($start);

        $daysBetween = floor($diff/(60*60*24));

        $formattedDates = array();
        for ($i = 0; $i <= $daysBetween; $i++) {
            $tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
            $formattedDates[] = date('Y-m-d', strtotime($tmpDate));
        }
        return $formattedDates;
    }

    public function completePlan(Request $request)
    {
        try{
            \DB::transaction(function () use($request) {
                // store new program details
                if (count($request->new_programs) > 0) {
                    foreach ($request->new_programs as $program) {
                        $store_media_plan_program_service = new StoreMediaPlanProgram($program['days'], $program['program_name'],$program['station'],
                                                $program['start_time'], $program['end_time'],$program['unit_rate'], $program['duration']);
                        $store_media_plan_program = $store_media_plan_program_service->storeMediaPlanProgram();
                    }
                }

                // store new volume discounts
                if (count($request->new_volume_discounts) > 0) {
                    foreach ($request->new_volume_discounts as $detail) {
                        $store_volume_discount_service = new StoreMediaPlanVolumeDiscount($detail['discount'], $detail['station'], $this->companyId());
                        $store_volume_discount = $store_volume_discount_service->storeMediaPlanDiscount();
                    }
                }

                // update media plan suggestions material lengths
                if (count($request->programs_stations) > 0) {
                    foreach ($request->programs_stations as $program_station) {
                        $material_lengths = $this->computeMaterialLengthsForSuggestion($program_station);
                        MediaPlanSuggestion::where('id', $program_station['id'])->update(['material_length' => json_encode($material_lengths)]);
                    }
                }

                //store the media plan deliverables
                //@todo need to make this much better than it is currently
                $media_plan = MediaPlan::findOrFail($request->plan_id);
                $deliverables_service = new StoreMediaPlanDeliverables($media_plan);
                $deliverables_service->run();
                $logactivity = new LogActivity($media_plan, "Create Media Plan");
                $log = $logactivity->log();
            });
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->json(['status'=>'error', 'message'=> "The current operation failed."]);
        }
       
        return response()->json([
            'message'=>"Media plan updated",
            "status" => "success",
            "media_plan" => MediaPlan::find($request->plan_id)->toArray() //this is a stop gap, we need to use resources
        ]);
    }

    public function computeMaterialLengthsForSuggestion($suggestion)
    {
        $material_lengths = [];
        foreach ($suggestion['exposures'] as $duration => $exposures) {
            foreach ($exposures['dates'] as $date => $exposure) {
                if ((INT)$exposure > 0 && $suggestion['program'] != "Unknown Program") {
                    $unit_rate = 0;
                    if ($suggestion['duration_lists'] != "[null]" && $suggestion['rate_lists'] != "[null]") {
                        if (gettype($suggestion['duration_lists']) == 'string') {
                            $duration_lists = json_decode($suggestion['duration_lists']);
                            $rate_lists = json_decode($suggestion['rate_lists']);
                        } else {
                            $duration_lists = $suggestion['duration_lists'];
                            $rate_lists = $suggestion['rate_lists'];
                        }
                        foreach ($duration_lists as $key => $value) {
                            if ($duration == $value) {
                                $unit_rate = $rate_lists[$key];
                            }
                        }
                    }

                    $gross_total = (INT)$unit_rate * (INT)$exposure;
                    $deducted_value = ((INT)$suggestion['volume_discount']/100) * $gross_total;
                    $net_total = $gross_total - $deducted_value;
                    $grp = 100 * (double)$suggestion['rating'] * $exposure;

                    $material_lengths[$duration][] = [
                        'id' => $suggestion['id'],
                        'material_length' => $duration,
                        'unit_rate' => (INT)$unit_rate,
                        'volume_disc' => (INT)$suggestion['volume_discount'],
                        'date' => $date,
                        'day' => $suggestion['day'],
                        'slot' => (INT)$exposure,
                        'exposure' => (INT)$exposure,
                        'net_total' => $net_total,
                        'grp' => $grp
                    ];
                }
            }
        }
        return $material_lengths;
    }

    public function groupById($query)
    {
        $result = $query->groupBy(['id', 'material_length']);
        return $result;
    }

    public function groupByDuration($query)
    {
        return $query->groupBy('material_length');
    }

    public function storePrograms(Request $request)
    {
        $store_media_plan_program_service = new StoreMediaPlanProgram($request->days, $request->program_name,$request->station,
                                                $request->start_time, $request->end_time,$request->unit_rate, $request->duration);
        $store_media_plan_program = $store_media_plan_program_service->storeMediaPlanProgram();
        $rating = MediaPlanProgramRating::where([
            ['station', $request->station],
            ['program_name', $request->program_name]
        ])->get();
        if($store_media_plan_program){
            return ['programs' => $store_media_plan_program['media_programs'], 'ratings' => $rating];
        }else{
            return 'error';
        }
    }

    public function storeVolumeDiscount(Request $request)
    {
        $store_volume_discount_service = new StoreMediaPlanVolumeDiscount($request->discount, $request->station, $this->companyId());
        $store_volume_discount = $store_volume_discount_service->storeMediaPlanDiscount();
        if($store_volume_discount){
            return ['data' => $store_volume_discount];
        }else{
            return 'error';
        }
    }

    /**
    * This method will create a new media plan if there are ratings for it.
    * Note that this method replaces the generateRatingsPost method, so get rid of that one
    * @todo use a proper media plan resource with redirect links etc here
    */
    public function createNewMediaPlan(StorePlanRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $company_id = $this->companyId();
        $create_plan_service = new StoreMediaPlanService($validated, $company_id, $user->id);
        $media_plan = $create_plan_service->run();
        $logactivity = new LogActivity($media_plan, "Create new media plan");
        $log = $logactivity->log();
        if ($media_plan) {
            return [
                'status'=>"success",
                'message'=> "Ratings successfully generated, going to next page",
                'redirect_url' => route('agency.media_plan.customize',['id'=>$media_plan->id], false)
            ];
        } else {
            return [
                'status'=>"error",
                'message'=> "No results came back for your criteria"
            ];
        }
    }

    public function getPlanChannelId($planMediaType)
    {
        $channel_service = new GetChannelByName($planMediaType);
        $channel = $channel_service->getCampaignChannelsByName();
        return json_encode([$channel->id]);
    }

    public function getPlanGenderId($planGender)
    {
        $audience_service = new GetTargetAudience($planGender);
        $audiences = $audience_service->getAudienceByName();
        $audienceId = [];
        foreach ($audiences as $audience) {
            $audienceId[] = $audience->id;
        }
        return json_encode($audienceId);
    }

    public function convertPlanToCampaign($media_plan_id)
    {
        $media_plan = MediaPlan::findorfail($media_plan_id);

        if ($media_plan->status != MediaPlanStatus::APPROVED) {
            return response()->json([
                'status' => 'error',
                'data' => 'You can only convert and approved media plan to campaign'
            ]);
        }

        $campaign_id = '';

        try {
            \DB::transaction(function () use ($media_plan, &$campaign_id) {
                $suggestion_service = new GetSuggestionListByDuration($media_plan);
                $suggestions = $suggestion_service->run();
                // create a campaign instance
                $campaign_reference = Utilities::generateReference();
                $now = strtotime(Carbon::now('Africa/Lagos'));
                $channel = $this->getPlanChannelId($media_plan->media_type);
                $target_audience = $this->getPlanGenderId(json_decode($media_plan->gender));
                $created_by = \Auth::user()->id;
                $belongs_to = \Auth::user()->companies->first()->id;
                $budget = $suggestion_service->getTotalBudgetPerPlan($suggestions);
                $ad_slots = $suggestion_service->getTotalAdSlotPerPlan($suggestions);
                $store_campaign_service = new StoreCampaign($now, $campaign_reference, $channel, $target_audience, $created_by, $belongs_to, $media_plan, $budget, $ad_slots);
                $campaign = $store_campaign_service->run();
                $campaign_id = $campaign->id;

                // create MPO for each station in the Media plan
                // get the media plan program details
                $store_mpo_service = new StoreMpo($campaign_id, $media_plan, $suggestions);
                $mpo = $store_mpo_service->run();

                // update media plan field to "is_converted_to_mpo"
                $media_plan->status = MediaPlanStatus::CONVERTED;
                $media_plan->save();
                $logactivity = new LogActivity($media_plan, "Convert media Plan to campaign");
                $log = $logactivity->log();
            });
        } catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'data' => 'Something went wrong, media plan cannot be convert to MPO.'.$ex->getMessage()
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => 'Media Plan successfully converted to MPO',
            'campaign_id' => $campaign_id
        ]);
    }

    public function sendRequestResponse($media_plan_id, $status)
    {

       $mediaPlan = MediaPlan::findorfail($media_plan_id);
       $user_mail_content_array = array(
            "sender_name" => \Auth::user()->firstname.  " ". \Auth::user()->lastname,
            "action" => $status,
            "client" =>  $this->getClientName($media_plan_id),
            "receiver_name" => $this->getPlannerDetails($mediaPlan->planner_id)['name'],
            "link" => route('agency.media_plan.summary', ['id'=>$media_plan_id]),
            "subject" => "Your Media Plan has been ". $status

        );
        $logactivity = new LogActivity($mediaPlan, "Convert media Plan to campaign");
        $log = $logactivity->log();
        $send_mail = \Mail::to($this->getPlannerDetails($mediaPlan->planner_id)['email'])->send(new ApprovalNotification($user_mail_content_array));

    }

    public function requestApproval($media_plan_id, $user_id)
    {

          $user_mail_content_array = array(
            "sender_name" => \Auth::user()->firstname.  " ". \Auth::user()->lastname,
            "client" => $this->getClientName($media_plan_id),
            "receiver_name" => $this->getPlannerDetails($user_id)['name'],
            "link" => $media_plan_id,
            "subject" => "Request For Approval"

          );
          $mediaPlan = MediaPlan::findorfail($media_plan_id);
          $logactivity = new LogActivity($mediaPlan, "Request For Approval");
          $log = $logactivity->log();
            $send_mail = \Mail::to($this->getPlannerDetails($user_id)['email'])->send(new MailForApproval($user_mail_content_array));
    }

    function getPlannerDetails($planner_id){
        $planner="";
        $user_list_service = new GetUserList([$this->companyId()]);
        $user_list = $user_list_service->getUserData();
        foreach($user_list as $user){
            if($planner_id == $user['id'])
            {
                $planner= $user;
            }
        }
        return $planner;
    }

    public function getClientName($media_plan_id){
        $media_plan = MediaPlan::findOrFail($media_plan_id);
        $client = Client::findOrFail($media_plan->client_id);
        return $client->name;
    }

    public function postRequestApproval(Request $request)
    {
        $mediaPlan = MediaPlan::with(['client'])->findorfail($request->media_plan_id);
        $mediaPlan->status = MediaPlanStatus::IN_REVIEW;
        $mediaPlan->save();
        $lo= $this->requestApproval($request->media_plan_id, $request->user_id);
        $mediaPlanData = MediaPlan::with(['client'])->findorfail($request->media_plan_id);
        return response()->json([
            'status' => 'success',
            'data' =>  new MediaPlanResource($mediaPlanData)
        ]);
    }

    public function deletePlan($id)
    {
        $media_plan = MediaPlan::findorfail($id);
        if ($media_plan->status == MediaPlanStatus::PENDING || $media_plan->status == MediaPlanStatus::IN_REVIEW) {
            $this->authorize('delete', $media_plan);
            $delete_plan = (new DeleteMediaPlanService($media_plan))->run();
            return response()->json(array('code' =>  204), 204); 
        }
        $logactivity = new LogActivity($media_plan, "Delete Media plan");
        $log = $logactivity->log();
        return response()->json(array('code' =>  400), 400); 
    }

    public function clonePlan(ClonePlanRequest $request, $media_plan_id)
    {
        $media_plan = MediaPlan::findorfail($media_plan_id);
        $validated = $request->validated();
        $user = auth()->user();
        $company_id = $this->companyId();
        $cloned_plan = (new CloneMediaPlanService($media_plan, $validated, $company_id, $user->id))->run();
        $logactivity = new LogActivity($media_plan, "Clone Media plan");
        $log = $logactivity->log();
        return new MediaPlanResource($cloned_plan);
    }

    public function assignFollower(AssignFollowerRequest $request, $id)
    {
        $mediaPlan = MediaPlan::findOrFail($id);
        $this->authorize('assignFollower', $mediaPlan);

        $validated = $request->validated();
        array_push($validated['user_id'], $request->user()->id);
        $users = User::whereIn('id', $validated['user_id'])->get();
        $mediaPlan->addManyFollowers($users);
    }
}
