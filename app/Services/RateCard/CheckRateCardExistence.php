<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Libraries\Enum\ProgramStatus;
use Vanguard\Models\Ratecard\Ratecard;

class CheckRateCardExistence
{
    protected $name;
    protected $company_id;

    public function __construct($name, $company_id)
    {
        $this->name = $name;
        $this->company_id = $company_id;
    }

    public function checkRateCardExistence()
    {
        return Ratecard::where([
            ['slug', str_slug($this->name)],
            ['company_id', $this->company_id],
            ['status', ProgramStatus::ACTIVE]
        ])->first();
    }
}
