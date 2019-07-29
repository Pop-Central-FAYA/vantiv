<?php

namespace Vanguard\Http\Controllers\Ssp\Compliance;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Services\Compliance\ComplianceSummary;

class ComplianceController extends Controller
{
    public function downloadSummary($campaign_id)
    {
        $publisher_id = '';
        if(\Auth::user()->company_type == CompanyTypeName::BROADCASTER){
            $publisher_id = \Auth::user()->companies->first()->id;
        }
        $compliance_summary_service = new ComplianceSummary($campaign_id, $publisher_id);
        $compliance_summary_service->downloadComplianceSummary();
    }
}
