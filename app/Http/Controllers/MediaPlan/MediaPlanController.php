<?php

namespace Vanguard\Http\Controllers\MediaPlan;

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

class MediaPlanController extends Controller
{
    use ListDayTrait;
    use DefaultMaterialLength;
    use CompanyIdTrait;

    public function index($value='')
    {
        //Broadcaster Dashboard module
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        if ($broadcaster_id) {
            //redirect user to the new landing page of the broadcaster.
            return view('broadcaster_module.landing_page');

        } else if ($agency_id) {
            $media_plan_service = new GetMediaPlans();
            //count pending media plans
            $count_pending_media_plans = $media_plan_service->pendingPlans();

            //count approved media plans
            $count_approved_media_plans = $media_plan_service->approvedPlans();

            //count declined media plans
            $count_declined_media_plans = $media_plan_service->declinedPlans();

            return view('agency.mediaPlan.dashboard')
                    ->with([
                        'count_pending_media_plans' => $count_pending_media_plans,
                        'count_approved_media_plans' => $count_approved_media_plans,
                        'count_declined_media_plans' => $count_declined_media_plans
                    ]);
        }   
    }
    
    public function customisPlan()
    {
        return view('agency.mediaPlan.custom_plan');
        
    }

    public function getSuggestionsByPlanId($id='')
    {
        return "got here";
    }

    public function dashboardMediaPlans(Request $request)
    {
        $media_plan_service = new GetMediaPlans();
        return $media_plan_service->run();
    }

    public function criteriaForm(Request $request)
    {
        $criterias = Criteria::with(['subCriterias'])->groupBy('name')->get();
        // return criterias array with the frontend view, in order to populate criteria inputs
        return view('agency.mediaPlan.create_plan')->with('criterias', $criterias);
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
            "day_parts" => collect(GetSuggestedPlans::DAYPARTS)->keys()->sort()->toArray(),
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

        $summary_service = new SummarizePlan($mediaPlan);

        $summaryData =  $summary_service->run();

        return view('agency.mediaPlan.summary')->with('summary', $summaryData)
                ->with('media_plan', $mediaPlan);
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
        Session::flash('success', 'Media plan successfully approved');
        return redirect()->route('agency.media_plan.summary',['id'=>$mediaPlan->id]);
    }

    public function declinePlan($media_plan_id)
    {
        $mediaPlan = MediaPlan::findorfail($media_plan_id);
        $mediaPlan->status = 'Declined';
        $mediaPlan->save();
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
    
    public function CreatePlan($id)
    {
        $media_plan = MediaPlan::find($id);
        if(!$media_plan){
            return redirect()->route("agency.media_plan.criteria_form");
        }
        $get_suggestion_with_ratings = new GetSuggestionListWithProgramRating($id);
        $plans = $get_suggestion_with_ratings->getMediaPlanSuggestionWithProgram();
        $clients = new AllClient(\Auth::user()->companies->first()->id);
        $clients = $clients->getAllClients();
        if(count($plans) == 0 ){
            return redirect()->route("agency.media_plan.criteria_form");
        }
        $suggestions = $this->groupSuggestions($plans);
        $suggestionsByStation = $this->groupSuggestionsByStation($plans);
        $dates = $this->dates($media_plan->start_date, $media_plan->end_date);
        $labeldates = $this->labeldates($media_plan->start_date, $media_plan->end_date);
        $days = $this->days($media_plan->start_date, $media_plan->end_date);
        $fayaFound = array(
            'total_tv' => $this->countByMediaType($suggestions, 'Tv'),
            'total_radio' => $this->countByMediaType($suggestions, 'Radio'),
            'programs_stations' => $plans,
            'stations' => $suggestionsByStation,
            'total_audiences' => $this->totalAudienceFound($suggestions),
            'dates' => $dates,
            'labeldates' => $labeldates,
            'days' => $days,
        );
        $client_brand = '';
        if($media_plan->client_id != ''){
            $client_brand = new ClientBrand($media_plan->client_id);
            $client_brand = $client_brand->run();
        }
        return view('agency.mediaPlan.complete_plan')->with('fayaFound', $fayaFound)
                                                            ->with('clients', $clients)
                                                            ->with('days', $this->listDays())
                                                            ->with('default_material_length', $this->getDefaultMaterialLength())
                                                            ->with('plan_id', $id)
                                                            ->with('media_plan', $media_plan)
                                                            ->with('brands', $client_brand);
    
    }

    public function days($start, $end)
    {
       date_default_timezone_set('UTC');
       $diff = strtotime($end) - strtotime($start);
       $daysBetween = floor($diff/(60*60*24));
            $formattedDates = array();
            for ($i = 0; $i <= $daysBetween; $i++) {
                $tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
                $formattedDates[] = date('l', strtotime($tmpDate));
            }    
            return $formattedDates;
    }

    public function labeldates($start, $end)
     {
        date_default_timezone_set('UTC');

        $diff = strtotime($end) - strtotime($start);

        $daysBetween = floor($diff/(60*60*24));

        $formattedDates = array();
        for ($i = 0; $i <= $daysBetween; $i++) {
            $tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
            $formattedDates[] = date('F d', strtotime($tmpDate));
        }
        return $formattedDates;
    }

    public function dates($start, $end) {
        date_default_timezone_set('UTC');

        $diff = strtotime($end) - strtotime($start);

        $daysBetween = floor($diff/(60*60*24));

        $formattedDates = array();
        for ($i = 0; $i <= $daysBetween; $i++) {
            $tmpDate = date('Y-m-d', strtotime($start . " + $i days"));
            $formattedDates[] = date('Y-m-d', strtotime($tmpDate));
        }
        return $formattedDates;
    }




    public function CompletePlan(Request $request)
    {
        try{
            Utilities::switch_db('api')->transaction(function () use($request) {

                $programs_id = json_decode($request->get('data'));
                $programs_id = collect($programs_id);
                $programs_id = $this->groupById($programs_id);
                $client_name = $request->get('client_name');
                $product_name = $request->get('product_name');
                $brand_id = $request->get('brand_id');
                $plan_id = $request->get('plan_id');

                foreach($programs_id as $key => $value) {
                    DB::table('media_plan_suggestions')
                        ->where('id', $key)
                        ->update(['material_length' => $value]);
                }
                DB::table('media_plans')
                    ->where('id', $plan_id)
                    ->update(['client_id' => $client_name, 'product_name' => $product_name, 'brand_id' => $brand_id]);
            });
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->json(['status'=>'error', 'message'=> "The current operation failed" ]);
        }
        return response()->json(['msg'=>"Good to go", "status" => "success"]);
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
                'redirect_url' => $suggestions->id
            ];
        } else {
            Session::flash('error', 'No results came back for your criteria');
            return [
                'status'=>"error",
                'message'=> "No results came back for your criteria"
            ];
        }
        // if ($suggestions->isNotEmpty()) {
        //     // store planning criteria and suggestions
        //     $storeSuggestionsService = new StorePlanningSuggestions($request, $suggestions);
        //     $newMediaPlan = $storeSuggestionsService->storePlanningSuggestions();
        //     return [
        //         'status'=>"success",
        //         'message'=> "Ratings successfully generated, going to next page",
        //         'redirect_url' => $newMediaPlan->id
        //     ];
        // }else{
        //     Session::flash('error', 'No results came back for your criteria');
        //     return [
        //         'status'=>"error",
        //         'message'=> "No results came back for your criteria"
        //     ];
        // }
    }

}
