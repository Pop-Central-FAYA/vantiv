<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;

class CampaignStatusPercentage
{
    protected $company_ids;


    public function __construct($company_ids)
    {
        $this->company_ids = $company_ids;
    }

    public function companyIdArray()
    {
        $company_id_array = [];
        foreach ($this->company_ids as $company_id){
            $company_id_array[] = $company_id->id;
        }
        return $company_id_array;
    }

    public function baseQuery()
    {
        return \DB::table('campaignDetails')
            ->whereIn('launched_on', $this->companyIdArray());
    }


    public function getTotalCount()
    {
        return $this->baseQuery()->count();
    }

    public function getActiveCount()
    {
        return $this->baseQuery()->where('status', CampaignStatus::ACTIVE_CAMPAIGN)
                                ->count();
    }

    public function getPendingCount()
    {
        return $this->baseQuery()->where('status', CampaignStatus::PENDING)
                                ->count();
    }

    public function getFinishedCount()
    {
        return $this->baseQuery()->where('status', CampaignStatus::FINISHED)
                                ->count();
    }

    public function activePercentage()
    {
        return $this->getTotalCount() != 0 ? ($this->getActiveCount() / $this->getTotalCount()) * 100 : 0;
    }

    public function pendingPercentage()
    {
        return $this->getTotalCount() != 0 ?($this->getPendingCount() / $this->getTotalCount()) * 100 : 0;
    }

    public function finishedPercentage()
    {
        return $this->getTotalCount() != 0 ? ($this->getFinishedCount() / $this->getTotalCount()) * 100 : 0;
    }
}
