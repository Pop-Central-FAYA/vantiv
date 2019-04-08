<?php

namespace Vanguard\Services\Traits;

use Vanguard\Libraries\Enum\ProgramStatus;

trait TimeBeltTrait
{
    public function baseQuery()
    {
        return \DB::table('time_belts')
                    ->join('companies', 'companies.id', '=', 'time_belts.station_id')
                    ->leftJoin('media_programs', 'media_programs.id', '=', 'time_belts.media_program_id')
                    ->leftJoin('rate_cards', 'media_programs.rate_card_id', '=', 'rate_cards.id')
                    ->select('time_belts.*', 'companies.name AS station', 'media_programs.name AS program_name',
                        'media_programs.status', 'companies.id AS company_id', 'rate_cards.title AS rate_card');
    }
}
