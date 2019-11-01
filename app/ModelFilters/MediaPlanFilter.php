<?php

namespace Vanguard\ModelFilters;

use EloquentFilter\ModelFilter;
use Vanguard\Libraries\Enum\MediaPlanStatus;

class MediaPlanFilter extends ModelFilter
{

    public function status($status)
    {
        return $this->whereIn('status', $this->checkPlanStatus($status));
    }

    public function companyId($company_id)
    {
        return $this->where('company_id', $company_id);
    }

    private function checkPlanStatus($status)
    {
        switch ($status) {
            case MediaPlanStatus::PENDING : 
                return [MediaPlanStatus::PENDING, MediaPlanStatus::IN_REVIEW, MediaPlanStatus::FINALIZED];
                break;
            case MediaPlanStatus::APPROVED || $status == MediaPlanStatus::REJECTED :
                return [$status];
                break;
            default :
                return [MediaPlanStatus::PENDING,MediaPlanStatus::CONVERTED,MediaPlanStatus::FINALIZED,
                        MediaPlanStatus::APPROVED,MediaPlanStatus::REJECTED,MediaPlanStatus::IN_REVIEW];
        }
    }
}
