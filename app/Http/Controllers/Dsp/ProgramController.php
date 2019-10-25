<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Program\StoreRequest;
use Vanguard\Http\Requests\Program\UpdateRequest;
use Vanguard\Http\Resources\MediaPlanProgramResource;
use Vanguard\Models\MediaPlanProgram;
use Vanguard\Models\TvStation;
use Vanguard\Services\Program\StoreService;
use Vanguard\Services\Program\UpdateService;

class ProgramController extends Controller
{
    public function store(StoreRequest $request, $station_id)
    {
        TvStation::findOrFail($station_id);
        $validated = $request->validated();

        $program = (new StoreService($validated, $station_id))->run();
        return new MediaPlanProgramResource($program);
    }

    public function update(UpdateRequest $request, $station_id, $program_id)
    {
        TvStation::findOrFail($station_id);
        $program = MediaPlanProgram::findOrFail($program_id);

        $validated = $request->validated();
        (new UpdateService($program_id, $validated, $program))->run();
        return new MediaPlanProgramResource(MediaPlanProgram::find($program_id));
    } 
}
