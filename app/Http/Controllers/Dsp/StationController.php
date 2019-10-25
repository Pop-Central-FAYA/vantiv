<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Station\ListRequest;
use Vanguard\Http\Requests\Station\StoreRequest;
use Vanguard\Http\Requests\Station\UpdateRequest;
use Vanguard\Http\Resources\StationResource;
use Vanguard\Models\TvStation;
use Vanguard\Services\Station\StoreService;
use Vanguard\Services\Station\UpdateService;

class StationController extends Controller
{
    public function list(ListRequest $request)
    {
        $validated = $request->validated();
        $station_list = TvStation::filter($validated)->get();
        return StationResource::collection($station_list);
    }

    public function get($id)
    {
        $station = TvStation::findOrFail($id);
        return new StationResource($station);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $station = (new StoreService($validated))->run();
        return new StationResource($station);
    }

    public function update(UpdateRequest $request, $id)
    {
        $station = TvStation::findOrFail($id);
        $validated = $request->validated();

        (new UpdateService($station->id, $validated, $station))->run();
        return new StationResource(TvStation::find($id));
    } 
}
