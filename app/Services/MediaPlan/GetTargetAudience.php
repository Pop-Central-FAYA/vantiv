<?php

namespace Vanguard\Services\MediaPlan;

use Vanguard\Libraries\Utilities;
use Vanguard\Models\TargetAudience;

class GetTargetAudience
{
    protected $audience;

    public function __construct($audience)
    {
        $this->audience = $audience;
    }

    public function getAudienceByName()
    {
        return TargetAudience::whereIn('audience', $this->audience)->get();
    }
}
