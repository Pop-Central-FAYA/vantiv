<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Services\Traits\MpoQueryTrait;

class MpoDetails
{
    protected $mpo_id;
    protected $company_id;

    use MpoQueryTrait;

    public function __construct($mpo_id, $company_id)
    {
        $this->mpo_id = $mpo_id;
        $this->company_id = $company_id;
    }

    public function baseQuery()
    {
        return $this->mpoBaseQuery();
    }

    public function getMpoDetails()
    {
        return $this->baseQuery()->where([
            ['mpoDetails.broadcaster_id', $this->company_id],
            ['campaignDetails.launched_on', $this->company_id]
            ])
            ->where('mpoDetails.mpo_id', $this->mpo_id)
            ->where('campaignDetails.status', 'pending')
            ->orWhere('campaignDetails.status', 'file_error')
            ->get();
    }
}
