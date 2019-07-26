<?php

namespace Vanguard\Http\Controllers\Broadcaster;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\Inventory\GetMediaInventory;
use Yajra\DataTables\DataTables;

class TimeBeltManagementController extends Controller
{
    use CompanyIdTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('broadcaster_module.time-belt-management.index');
    }

    /**
     * format data to datatable
     */
    public function formatToDatatable(DataTables $dataTables)
    {
        $get_time_belt_service = new GetMediaInventory($this->companyId());
        $time_belts = $get_time_belt_service->getMediaInventory();
        return $dataTables->collection($time_belts)
            ->addColumn('details', function ($time_belts) {
                return '<a href="'.route('time.belt.management.details', ['id' => $time_belts['id']]).'" class="weight_medium">Details</a>';
            })
            ->rawColumns(['details' => 'details'])->addIndexColumn()
            ->make(true);
    }

    /**
     * details
     */
    public function details($time_belt_id)
    {

    }

}
