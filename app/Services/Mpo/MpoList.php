<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Services\Traits\MpoQueryTrait;

use Log;

class MpoList
{
    protected $company_id;
    protected $start_date;
    protected $end_date;

    use MpoQueryTrait;

    public function __construct($company_id, $start_date, $end_date, $status=null)
    {
        $this->company_id = $company_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
    }

    public function baseQuery()
    {
        return $this->mpoBaseQuery();
    }

    public function mpoList()
    {
        $query = $this->baseQuery()->when(is_array($this->company_id), function ($query) {
                                    return $query->whereIn('mpoDetails.broadcaster_id', $this->company_id)
                                                ->whereIn('campaignDetails.launched_on', $this->company_id);
                                })
                                ->when(!is_array($this->company_id), function ($query) {
                                    return $query->where('mpoDetails.broadcaster_id', $this->company_id)
                                                ->where('campaignDetails.launched_on', $this->company_id);
                                })
                                ->when($this->start_date && $this->end_date, function ($query) {
                                    return $query->whereBetween('mpoDetails.time_created', array($this->start_date, $this->end_date));
                                })
                                ->when($this->status, function($query) {
                                    if ($this->status == "pending") {
                                        $query->where('mpoDetails.is_mpo_accepted', 0);
                                    }
                                })
                                ->where('campaignDetails.status', '!=', 'on_hold')
                                ->orWhere('campaignDetails.status', '=', 'file_error')
                                ->orderBy('mpoDetails.time_created', 'DESC');
        return $query->get();
        
    }

    public function pendingMpoList()
    {
        return $this->baseQuery()->when(is_array($this->company_id), function ($query) {
            return $query->whereIn('mpoDetails.broadcaster_id', $this->company_id)
                ->whereIn('campaignDetails.launched_on', $this->company_id);
            })
            ->when(!is_array($this->company_id), function ($query) {
                return $query->where('mpoDetails.broadcaster_id', $this->company_id)
                    ->where('campaignDetails.launched_on', $this->company_id);
            })
            ->when($this->start_date && $this->end_date, function ($query) {
                return $query->whereBetween('mpoDetails.time_created', array($this->start_date, $this->end_date));
            })
            ->where('mpoDetails.is_mpo_accepted', 0)
            ->where('campaignDetails.status', '!=', 'on_hold')
            ->orWhere('campaignDetails.status', '=', 'file_error')
            ->orderBy('mpoDetails.time_created', 'DESC')
            ->get();
    }

    public function getMpoCompanyId()
    {
        return \DB::table('mpoDetails')
                    ->selectRaw("GROUP_CONCAT(DISTINCT broadcaster_id) AS company_id")
                    ->whereIn('broadcaster_id', $this->company_id)
                    ->groupBy('broadcaster_id')
                    ->get();
    }
}
