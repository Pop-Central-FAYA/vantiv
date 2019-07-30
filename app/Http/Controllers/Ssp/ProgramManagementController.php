<?php

namespace Vanguard\Http\Controllers\Ssp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\MediaInventoryStoreRequest;
use Vanguard\Services\Company\CompanyDetailsFromIdList;
use Vanguard\Services\Inventory\CreateInventoryService;
use Vanguard\Services\Inventory\GetProgramById;
use Vanguard\Services\Inventory\GetProgramList;
use Vanguard\Services\Inventory\UpdateInventoryService;
use Vanguard\Services\RateCard\GetRateCardForStation;
use Vanguard\Services\Traits\ListDayTrait;
use Yajra\DataTables\DataTables;

class ProgramManagementController extends Controller
{
    use CompanyIdTrait;
    use ListDayTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media_inventory_service = new GetProgramList($this->companyId());
        $media_inventories = $media_inventory_service->getActivePrograms();
        return view('broadcaster_module.program-management.index')->with('media_inventories', $media_inventories);
    }

    /**
     * format media inventory to datatable
     */
    public function formatToDataTable(DataTables $dataTables)
    {
        $media_inventory_service = new GetProgramList($this->companyId());
        $media_inventories = $media_inventory_service->activeProgramDate();
        return $dataTables->collection($media_inventories)
            ->addColumn('edit', function ($media_inventories) {
                if(\Auth::user()->hasPermissionTo('update.inventory')){
                    return '<a href="'.route('program.management.edit', ['id' => $media_inventories['id']]).'" class="weight_medium">Edit</a>';
                }
            })
            ->rawColumns(['edit' => 'edit'])->addIndexColumn()
            ->make(true);
    }

    /**
     * edit media inventory
     */
    public function edit($id)
    {
        $program_by_id_service = new GetProgramById($id);
        $program = $program_by_id_service->getProgram();
        $get_station_rate_card_service = new GetRateCardForStation(array($program->company_id));
        return view('broadcaster_module.program-management.edit')->with('program', $program_by_id_service->getProgram())
                                                                    ->with('rate_cards', $get_station_rate_card_service->getRateCardDurations())
                                                                    ->with('time_belts', $program_by_id_service->groupTimeBelt())
                                                                    ->with('days', $this->listDays());
    }

    /**
     * get media inventory details
     */
    public function details($id)
    {

    }

    /**
     * create media inventory
     */
    public function create()
    {
        $company_ids = $this->getCompanyIdsList();
        $get_station_rate_card_service = new GetRateCardForStation($company_ids);
        $companies_service = new CompanyDetailsFromIdList($company_ids);
        return view('broadcaster_module.program-management.create')->with('days', $this->listDays())
                                                                        ->with('rate_cards', $get_station_rate_card_service->getRateCardDurations())
                                                                        ->with('companies', $companies_service->getCompanyDetails());
    }

    public function store(MediaInventoryStoreRequest $request)
    {
        $create_media_inventory_service = new CreateInventoryService($request->days, $request->program_name, $request->company,
            null, $request->rate_card, $request->start_date, $request->end_date, $request->start_time, $request->end_time);
        $create_inventory = $create_media_inventory_service->createTimeBelt();
        if($create_inventory == 'success'){
            \Session::flash('success', 'Media Inventory Created Successfully');
            return redirect()->route('program.management.index');
        }else{
            \Session::flash('error', 'An error occurred while creating your media inventory');
            return redirect()->back();
        }
    }

    public function update(MediaInventoryStoreRequest $request, $program_id)
    {
        $update_media_inventory_service = new UpdateInventoryService($request->days, $request->program_name, $request->company,
            null, $request->rate_card, $request->start_date, $request->end_date, $request->start_time, $request->end_time,
            $program_id);
        $update_media_inventory = $update_media_inventory_service->updateInventory();
        if($update_media_inventory == 'success'){
            \Session::flash('success', 'Media Inventory Updated Successfully');
            return redirect()->route('program.management.index');
        }else{
            \Session::flash('error', 'An error occurred while updating your media inventory');
            return redirect()->back();
        }
    }

    public function fetRateCard()
    {
        $company_id = request()->company_id;
        $rate_card_service = new GetRateCardForStation($company_id);
        return ['rate_cards' => $rate_card_service->getRateCardDurations()];
    }

}
