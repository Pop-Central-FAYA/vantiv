<?php

namespace Vanguard\Http\Controllers\Dsp\MediaPlan;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Models\Criteria;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanProgram;
use Vanguard\Models\MediaPlanProgramRating;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Services\Inventory\StoreMediaPlanProgram;
use Vanguard\Services\MediaPlan\GetSuggestionListWithProgramRating;
use Vanguard\Services\MediaPlan\StoreMediaPlanVolumeDiscount;
use Vanguard\Services\MediaPlan\ValidateCriteriaForm;
use Vanguard\Services\MediaPlan\SuggestPlan;
use Vanguard\Services\MediaPlan\StorePlanningSuggestions;
use Illuminate\Support\Facades\DB;
use Vanguard\Services\MediaPlan\GetMediaPlans;
use Vanguard\Services\MediaPlan\SummarizePlan;
use Vanguard\Services\MediaPlan\GetSuggestedPlans;
use Vanguard\Services\MediaPlan\ExportPlan;
use Vanguard\Services\Client\AllClient;
use Session;
use Maatwebsite;
use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Exports\MediaPlanExport;
use Vanguard\Services\Traits\DefaultMaterialLength;
use Vanguard\Services\Traits\ListDayTrait;
use Vanguard\Libraries\Utilities;
use Log;
use Vanguard\Services\MediaPlan\StoreCampaign;
use Vanguard\Services\MediaPlan\StoreMpo;
use Carbon\Carbon;
use Vanguard\Services\MediaPlan\GetSuggestionListByDuration;
use Vanguard\Libraries\Enum\MediaPlanStatus;
use Vanguard\Libraries\DayPartList;
use Vanguard\Services\MediaPlan\GetTargetAudience;
use Vanguard\Services\CampaignChannels\GetChannelByName;
use Vanguard\Services\User\GetUserList;
use Vanguard\Mail\MailForApproval;
use Vanguard\Mail\ApprovalNotification;
use Illuminate\Support\Facades\Auth;

class MediaPlanController extends Controller
{
    use ListDayTrait;
    use DefaultMaterialLength;
    use CompanyIdTrait;

    public function index(Request $request)
    {
        $company_id = $this->companyId();
        $media_plan_service = new GetMediaPlans($request->status, $company_id);
        $plans = $media_plan_service->run();
        return view('agency.mediaPlan.index')->with('plans', $plans);
    }

    public function customisPlan()
    {
        return view('agency.mediaPlan.custom_plan');
    }

    public function getSuggestionsByPlanId($id='')
    {
        return "got here";
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
        // return criterias array with the frontend view, in order to populate criteria inputs
        return view('agency.mediaPlan.create_plan')->with('criterias', $new_criterias)
                                                    ->with('redirect_urls', ['submit_form' => route('agency.media_plan.submit.criterias')]);
    }

    public function suggestPlan(Request $request)
    {
        // validate criteria form request
        $validateCriteriaFormService = new ValidateCriteriaForm($request->all());
        $validation = $validateCriteriaFormService->validateCriteria();

        if ($validation->fails()) {
            // var_dump($validation->errors()); return;
            return back()->withErrors($validation)->withInput();
        }
        // Fetch mps audiences, programs, stations, time duration, based on criteria
        $suggestPlanService = new SuggestPlan($request);
          $suggestions = $suggestPlanService->suggestPlan();
        if ($suggestions->isNotEmpty()) {
            // store planning criteria and suggestions
            $storeSuggestionsService = new StorePlanningSuggestions($request, $suggestions);
            $newMediaPlan = $storeSuggestionsService->storePlanningSuggestions();
            return redirect()->action(
                'MediaPlan\MediaPlanController@getSuggestPlanById', ['id' => $newMediaPlan->id]
            );
        }else{
            Session::flash('success', 'No results came back for your criteria');
            return redirect()->action(
                'MediaPlan\MediaPlanController@criteriaForm'
            );
        }
    }

    /**
     * This is the page that returns the suggested plan. (When this page loads it should also load up the filter values)
     * @todo This is kinda inefficient right now because the states list is being regenerated each time, but it will do
     * @todo Fix tis and the one below (getSuggestPlansByIdAndFilters)
     */
    public function getSuggestPlanById($id)
    {
        // Get the filter values
        $savedFilters = json_decode(MediaPlan::findOrFail($id)->filters, true);
        if (!$savedFilters) {
            $savedFilters = array();
        }
        $suggestedPlansService = new GetSuggestedPlans($id, $savedFilters);
        $plans = $suggestedPlansService->get();
        // if (count($plans) == 0) {
        //     //Render an empty page and do not redirect
        //  // return redirect()->route("agency.media_plan.criteria_form");
        // }
        // also get the filter values list to use to render with the filter dropdownss
        //dd($plans);
        return view('agency.mediaPlan.display_suggestions')
            ->with('mediaPlanId', $id)
            ->with('mediaPlanStatus', MediaPlan::findOrFail($id)->status)
            ->with('fayaFound', $plans)
            ->with('filterValues', $this->getFilterFieldValues($id))
            ->with('selectedFilters', $savedFilters);
    }

    public function getSuggestPlanByIdVue($id)
    {
        // Get the filter values
        $savedFilters = json_decode(MediaPlan::findOrFail($id)->filters, true);
        if (!$savedFilters) {
            $savedFilters = array();
        }
        $suggestedPlansService = new GetSuggestedPlans($id, $savedFilters);
        $plans = $suggestedPlansService->get();
        // $plans['selected'] = $plans['selected'];
        // dd($plans['selected']);
        // if (count($plans) == 0) {
        //     //Render an empty page and do not redirect
        //  // return redirect()->route("agency.media_plan.criteria_form");
        // }
        // also get the filter values list to use to render with the filter dropdownss
        //dd($plans);
        return view('agency.mediaPlan.display_suggestions_vue')
            ->with('mediaPlanId', $id)
            ->with('mediaPlanStatus', MediaPlan::findOrFail($id)->status)
            ->with('fayaFound', $plans)
            ->with('filterValues', $this->getFilterFieldValues($id))
            ->with('selectedFilters', $savedFilters);
    }

    /**
     * Get the ratings per filter (Not quite sure how to complete this)
     * So, save the filters the user selected, then the page will be reloaded
     * @todo add proper validation
     */
    public function setPlanSuggestionFilters(Request $request)
    {
        try {
            $media_plan_id = $request->get('mediaPlanId');
            $expected_fields = array('states', 'day_parts', 'days', 'station_type');
            $filters = array();
            foreach($expected_fields as $field) {
                $value = $request->input($field);
                if ($value && $value != 'all') {
                    $filters[$field] = $value;
                }
            }
            $media_plan = MediaPlan::findOrFail($media_plan_id);
            $media_plan->filters = json_encode($filters);
            $media_plan->save();
            return response()->json(array(
                'status' => 'success',
                'message' => 'Filters successfully saved',
                'redirect_url' => $media_plan_id
            ));
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(array(
                'status' => 'error',
                'message' => 'Unknown error occurred'
            ));
        }
    }
    /**
     * Return the values that should be used to populate the filter fields
     * i.e dayparts, and states (that were part of what was found)
     * @todo not the biggest fan of how this is done, but it should work for now
     */
    protected function getFilterFieldValues($mediaPlanId) {
        $state_list = array();
        $saved_state_list = MediaPlan::find($mediaPlanId)->state_list;
        if (strlen($saved_state_list) > 0) {
            $state_list = json_decode($saved_state_list, true);
        }
        return array(
            "day_parts" => collect(DayPartList::DAYPARTS)->keys()->sort()->toArray(),
            "state_list" => $state_list,
            "days" => array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"),
            "station_type" => array("Network", "Regional", "Satellite")
        );
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

    /**
     * This is a function to filter suggestions and reload the page with the new filtered suggestions.
     * The filters are:
     * 1. day parts --> i.e Morning, Afternoon, Night etc
     * 2. days --> i.e Monday, Tuesday etc
     * 3. states --> i.e Abuja, Kaduna etc
     * If there are no filters, then the full media plan suggestions get returned (which is the default)
     */
    public function summary($media_plan_id)
    {
        $mediaPlan = MediaPlan::with(['client'])->findorfail($media_plan_id);
        $selectedSuggestions = $mediaPlan->suggestions->where('status', 1)->where('material_length', '!=', null);

        if (count($selectedSuggestions) === 0) {
            // redirect to review suggestions page for user to select suggestions
            return redirect()->route('agency.media_plan.create', ['id'=> $mediaPlan->id]);
        }
        $routes= array(
            "back" => route('agency.media_plan.create', ['id'=>$media_plan_id]),
            "approve" => route('agency.media_plan.approve', ['id'=>$media_plan_id]),
            "decline" => route('agency.media_plan.decline', ['id'=>$media_plan_id]),
            "export" => route('agency.media_plan.export', ['id'=>$media_plan_id]),
            "approval" => route('agency.media_plan.get_approval')  
        );


        $summary_service = new SummarizePlan($mediaPlan);
        $summaryData =  $summary_service->run();
        $user_list_service = new GetUserList([$this->companyId()]);
        $user_list = $user_list_service->getUserData();
        
        return view('agency.mediaPlan.summary')->with('summary', $summaryData)
                ->with('media_plan', $mediaPlan)->with('users', $user_list)->with('routes', $routes);
    }

    public function exportPlan($media_plan_id)
    {
        $mediaPlan = MediaPlan::with(['client', 'brand'])->findorfail($media_plan_id);
        $selectedSuggestions = $mediaPlan->suggestions->where('status', 1)->where('material_length', '!=', null);

        if (count($selectedSuggestions) === 0) {
            // redirect to review suggestions page for user to select suggestions
            return redirect()->route('agency.media_plan.create', ['id'=> $mediaPlan->id]);
        }

        $plan_start_date = $mediaPlan->start_date;
        $plan_end_date = $mediaPlan->end_date;

        $summary_service = new SummarizePlan($mediaPlan);
        $media_plan_summary =  $summary_service->run();

        $export_service = new ExportPlan($mediaPlan);
        $media_plan_grouped_data = $export_service->run();

        $monthly_weeks_table_header = json_encode($export_service->monthly_weeks_campaign_duration($plan_start_date, $plan_end_date));

        return Excel::download(new MediaPlanExport($media_plan_summary, $media_plan_grouped_data, $monthly_weeks_table_header, $mediaPlan), 'mediaplan.xlsx');
    }

    public function approvePlan($media_plan_id)
    {
        $mediaPlan = MediaPlan::findorfail($media_plan_id);
        $mediaPlan->status = 'Approved';
        $mediaPlan->save();
        $this->sendRequestResponse($media_plan_id, "Approved");
        Session::flash('success', 'Media plan successfully approved');
        return redirect()->route('agency.media_plan.summary',['id'=>$mediaPlan->id]);
    }

    public function declinePlan($media_plan_id)
    {
        $mediaPlan = MediaPlan::findorfail($media_plan_id);
        $mediaPlan->status = 'Declined';
        $mediaPlan->save();
        $this->sendRequestResponse($media_plan_id, "Rejected");
        Session::flash('success', 'Media plan has been declined');
        return redirect()->route('agency.media_plan.summary',['id'=>$mediaPlan->id]);
        
    }

    public function totalAudienceFound($collection)
    {
        return $collection->sum('audience');
    }


    public function SelectPlanPost(Request $request)
    {

        $programs_id = json_decode($request->get('data'));
         $media_plan_id = $request->get('mediaplan');
            $value = "";
            try{
                Utilities::switch_db('api')->transaction(function () use($programs_id, $media_plan_id, $value) {

                    DB::table('media_plan_suggestions')
                    ->where('media_plan_id', $media_plan_id)
                    ->whereNotIn('id', $programs_id)
                    ->update(['status' => 0, 'material_length' => $value]);
                    DB::table('media_plan_suggestions')
                    ->whereIn('id', $programs_id)
                    ->update(['status' => 1]);

                });
            }catch (\Exception $exception){
                return response()->json(['status'=>'failed', 'message'=> "The current operation failed" ]);
            }
        return response()->json(['status'=>'success', 'message'=> "Plan Selected successfully" ]);


    }

    public function createPlan($id)
    {
        $media_plan = MediaPlan::find($id);
        if(!$media_plan){
            return redirect()->route("agency.media_plan.criteria_form");
        }
        $get_suggestion_with_ratings = new GetSuggestionListWithProgramRating($id);
        $plans = $get_suggestion_with_ratings->getMediaPlanSuggestionWithProgram();
        $clients = new AllClient($this->companyId());
        $clients = $clients->getAllClients();
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
        $client_brands = [];
        foreach ($clients as $client) {
            $brands = new ClientBrand($client->id);
            $client_brands[$client->id] = $brands->run();
        }
        $redirect_urls = [
            'back_action' => route('agency.media_plan.customize', ['id'=>$id]),
            'next_action' => route('agency.media_plan.summary', ['id'=>$id]),
            'save_action' => route('agency.media_plan.submit.finish_plan')
        ];

        return view('agency.mediaPlan.complete_plan')->with('fayaFound', $fayaFound)
                                                    ->with('clients', $clients)
                                                    ->with('client_brands', $client_brands)
                                                    ->with('days', $this->listDays())
                                                    ->with('default_material_length', $this->getDefaultMaterialLength())
                                                    ->with('plan_id', $id)
                                                    ->with('media_plan', $media_plan)
                                                    ->with('redirect_urls', $redirect_urls);

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

                // update media plan client, brand and product name
                MediaPlan::where('id', $request->plan_id)->update([
                    'client_id' => $request->client_id,
                    'brand_id' => $request->brand_id,
                    'product_name' => $request->product_name
                ]);
            });
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->json(['status'=>'error', 'message'=> "The current operation failed."]);
        }
        return response()->json(['message'=>"Media plan updated", "status" => "success"]);
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

                    $material_lengths[$duration][] = [
                        'id' => $suggestion['id'],
                        'material_length' => $duration,
                        'unit_rate' => (INT)$unit_rate,
                        'volume_disc' => (INT)$suggestion['volume_discount'],
                        'date' => $date,
                        'day' => $suggestion['day'],
                        'slot' => (INT)$exposure,
                        'exposure' => (INT)$exposure,
                        'net_total' => $net_total
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

    public function generateRatingsPost(Request $request)
    {
        // validate criteria form request
        $validateCriteriaFormService = new ValidateCriteriaForm($request->all());
        $validation = $validateCriteriaFormService->validateCriteria();

        if ($validation->fails()) {
            Session::flash('error', 'Please make sure the required parameters are filled out');
            return ['status'=>"error", 'message'=> "Please make sure the required parameters are filled out" ];
        }
        // Fetch mps audiences, programs, stations, time duration, based on criteria
        $suggestPlanService = new SuggestPlan($request);
        $suggestions = $suggestPlanService->suggestPlan();
        if ($suggestions) {
            return [
                'status'=>"success",
                'message'=> "Ratings successfully generated, going to next page",
                'redirect_url' => route('agency.media_plan.customize',['id'=>$suggestions->id])
            ];
        } else {
            Session::flash('error', 'No results came back for your criteria');
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
            "link" => route('agency.media_plan.decline', ['id'=>$media_plan_id]),
            "subject" => "Your Media Plan has been ". $status

        );
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
        $mediaPlan = MediaPlan::findorfail($media_plan_id);
        $client_name = "";
        $clients = new AllClient($this->companyId());
        $clients = $clients->getAllClients();
        foreach($clients as $client){
            if($mediaPlan->client_id = $client->id)
            {
                $client_name= $client->company_name;
            }
        } 
        return $client_name;
    }

    public function postRequestApproval(Request $request)
    {
      $mediaPlan = MediaPlan::with(['client'])->findorfail($request->media_plan_id);
      $mediaPlan->status = 'In Review';
      $mediaPlan->save();
      $lo= $this->requestApproval($request->media_plan_id, $request->user_id);
       $mediaPlanData = MediaPlan::with(['client'])->findorfail($request->media_plan_id);
       return response()->json([
        'status' => 'success',
        'data' =>  $mediaPlanData
        ]);
    }
}
