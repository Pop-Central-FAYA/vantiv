<?php

namespace Vanguard\Http\Controllers\Ssp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Models\Publisher;
use Vanguard\Services\Traits\ListDayTrait;
use Vanguard\Services\Traits\YearTrait;
use Vanguard\Services\Reports\Publisher\TopRevenueByMediaType;
use Vanguard\Services\Reports\Publisher\ClientsAndBrandsByMediaType;
use Vanguard\Services\Reports\Publisher\TopRevenueByClient;
use Vanguard\Services\Reports\Publisher\CampaignsByMediaType;
use Vanguard\Services\Reports\Publisher\MposByMediaType;
use Vanguard\Services\Reports\Publisher\Month\StationRevenue;
use Vanguard\Services\Reports\Publisher\Month\ActiveCampaigns;
use Vanguard\Services\Reports\Publisher\Month\SpotsSold;
use Vanguard\Services\Reports\Publisher\Month\TimeBeltRevenue;
use Vanguard\Http\Requests\Publisher\DashboardReportRequest;
use Vanguard\Http\Requests\Publisher\DashboardInventoryReportRequest;

class DashboardController extends Controller
{
    use CompanyIdTrait;
    use YearTrait;
    use ListDayTrait;
    public function index()
    {
        /**
         * If it is a broadcaster, there are some various places the user should be redirected to
         * 1. If the User has the scheduler role (he/she should be redirected to the inventory management screen)
         * 2 If the User has the admin, super admin or media_buyer role (he/she should be redirected to the campaign management page)
         * NB This might probably need to change when we finally implement the feature/permission base ALC
         */
        if (\Auth::user()->hasAnyPermission(['view.report', 'view.campaign'])) {
            $route = 'broadcaster.inventory_management';
        } else {
            $route = 'broadcaster.campaign_management';
        }
        return redirect()->route($route);
    }

    /**
     * @todo Generate year from database (like how many years active)
     */
    public function campaignManagementDashbaord()
    {
        $current_year = date('Y');

        $company_id_list = $this->getCompanyIdsList();
        $company_details = $this->getCompaniesDetails($company_id_list);

        //Get the station_ids for the initial query
        $publishers = Publisher::allowed($company_id_list)->get();
        $grouped_publishers = $publishers->groupBy('type');

        $media_type_list = $grouped_publishers->keys()->sort()->values()->reverse(); //this sorting is so tv comes first, but the order should be in the db
        $initial_media_type = $media_type_list->first();

        //get the full station list as an array
        $station_list = array();
        foreach ($grouped_publishers as $key => $value) {
            $station_list[$key] = $company_details->whereIn("id", $value->pluck("company_id"));
        }

        $monthly_filters = array('year' => $current_year, 'station_id' => $station_list[$initial_media_type]->pluck('id'));
        $data = [
            //other variables used to render different things
            'year_list' => $this->getYearFrom2018(),
            'current_year' => $current_year,
            'stations' => $station_list,
            'media_type_list' => $media_type_list,
            'media_type' => $initial_media_type,
            'company_ids' => $company_id_list,
            //monthly reports limits the reports by media type and the companies associated with it
            'monthly_reports' => $this->getMonthlyReport($company_id_list, $monthly_filters, 'station_revenue'),

            // these reports below use the full company_id list because this is the summary across all types
            'top_media_type_revenue' => (new TopRevenueByMediaType($company_id_list))->run(),
            'clients_and_brands' => (new ClientsAndBrandsByMediaType($company_id_list))->run(),
            'top_revenue_by_client' => (new TopRevenueByClient($company_id_list))->run(),
            'campaigns' => (new CampaignsByMediaType($company_id_list))->run(),
            'mpos' => (new MposByMediaType($company_id_list))->run(),
        ];
        return view('broadcaster_module.dashboard.campaign_management.dashboard')->with($data);

    }

    protected function getMonthlyReport($company_id_list, $filters, $report_type) {
        switch ($report_type) {
            case 'station_revenue':
                $service = new StationRevenue($company_id_list);
                break;
            case 'active_campaigns':
                $service = new ActiveCampaigns($company_id_list);
                break;
            case 'spots_sold':
                $service = new SpotsSold($company_id_list);
                break;
            case 'timebelt_revenue':
                $service = new TimeBeltRevenue($company_id_list);
                break;
            default:
                //default is station revenue
                $service = new StationRevenue($company_id_list);
                break;
        }
        $res = $service->setFilters($filters)->run();
        $res['report_type'] = $report_type;
        return $res;
    }

    /**
     * Requests to filter reports come through this method
     */
    protected function getFilteredPublisherReports(DashboardReportRequest $request) {
        return $this->executeMonthlyReportRequest($request);
    }

    public function inventoryManagementDashboard()
    {
        $company_id_list = $this->getCompanyIdsList();
        $company_details = $this->getCompaniesDetails($company_id_list);

        //Get the station_ids for the initial query
        $publishers = Publisher::allowed($company_id_list)->get();
        $grouped_publishers = $publishers->groupBy('type');

        $media_type_list = $grouped_publishers->keys()->sort()->values()->reverse(); //this sorting is so tv comes first, but the order should be in the db
        $initial_media_type = $media_type_list->first();

        //get the full station list as an array
        $station_list = array();
        foreach ($grouped_publishers as $key => $value) {
            $station_list[$key] = $company_details->whereIn("id", $value->pluck("company_id"));
        }

        $monthly_filters = array('station_id' => $station_list[$initial_media_type]->pluck('id'));
        $data = [
            'days' => $this->listDays(),
            'day_parts' => array("Late Night", "Overnight", "Breakfast", "Late Breakfast", "Afternoon", "Primetime"),
            'timebelt_revenue' => $this->getMonthlyReport($company_id_list, $monthly_filters, 'timebelt_revenue'),
            'stations' => $station_list,
            'media_type_list' => $media_type_list,
            'media_type' => $initial_media_type,
        ];
        return view('broadcaster_module.dashboard.inventory_management.dashboard')->with($data);

    }

    /**
     * Requests to filter reports come through this method
     */
    protected function getFilteredInventoryReports(DashboardInventoryReportRequest $request) {
        return $this->executeMonthlyReportRequest($request);
    }

    protected function executeMonthlyReportRequest($request) {
        $validated = $request->validated();
        $company_id_list = $this->getCompanyIdsList();

        /**
         * if a specific station was not requested (make sure to limit the query by stations belonging to the current media type being requested)
         */
        if (!isset($validated['station_id'])) {
            $validated['station_id'] = Publisher::ofType($validated['media_type'])->get()->pluck('company_id');
        }
        $response = array(
            'status' => 'success',
            'data' => $this->getMonthlyReport($company_id_list, $validated, $validated['report_type'])
        );
        return response()->json($response);
    }

}
