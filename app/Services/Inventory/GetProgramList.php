<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Libraries\Enum\ProgramStatus;
use Vanguard\Models\MediaProgram;

class GetProgramList
{
    protected $station_id;

    public function __construct($station_id)
    {
        $this->station_id = $station_id;
    }

    public function getActivePrograms()
    {
        return \DB::table('media_programs')
                    ->join('companies', 'companies.id', '=', 'media_programs.company_id')
                    ->join('rate_cards', 'rate_cards.id', '=', 'media_programs.rate_card_id')
                    ->when(is_array($this->station_id), function ($query) {
                        return $query->whereIn('media_programs.company_id', $this->station_id);
                    })
                    ->when(!is_array($this->station_id), function ($query) {
                        return $query->where('media_programs.company_id', $this->station_id);
                    })
                    ->select('media_programs.id', 'media_programs.name', 'rate_cards.title AS rate_card', 'companies.name AS station')
                    ->where('media_programs.status', ProgramStatus::ACTIVE)
                    ->get();
    }

    public function activeProgramDate()
    {
        $media_programs = [];
        foreach ($this->getActivePrograms() as $program){
            $media_programs[] = [
                'id' => $program->id,
                'program' => $program->name,
                'revenue' => 0,
                'rate_card' => $program->rate_card,
                'station' => $program->station
            ];
        }
        return $media_programs;
    }
}
