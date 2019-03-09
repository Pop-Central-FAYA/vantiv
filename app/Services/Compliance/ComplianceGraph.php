<?php

namespace Vanguard\Services\Compliance;

use Vanguard\Libraries\Enum\BroadcasterPlayoutStatus;

class ComplianceGraph
{
    protected $campaign_id;
    protected $start_date;
    protected $end_date;
    protected $publisher_id;

    public function __construct($campaign_id, $start_date, $end_date, $publisher_id)
    {
        $this->campaign_id = $campaign_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->publisher_id = $publisher_id;
    }

    public function complianceDateQuery()
    {
        return \DB::table('broadcaster_playouts')
                    ->join('selected_adslots', 'selected_adslots.id', '=', 'broadcaster_playouts.selected_adslot_id')
                    ->select('broadcaster_playouts.played_at')
                    ->where([
                            ['selected_adslots.campaign_id', $this->campaign_id],
                            ['broadcaster_playouts.status', BroadcasterPlayoutStatus::PLAYED]
                        ])
                    ->whereBetween('broadcaster_playouts.played_at', [$this->start_date, $this->end_date])
                    ->groupBy(\DB::raw("DATE_FORMAT(broadcaster_playouts.played_at, '%Y-%m-%d')"))
                    ->get();
    }

    public function getComplianceDates()
    {
        $dates = [];
        foreach ($this->complianceDateQuery() as $date){
            $dates[] = [date('Y-m-d', strtotime($date->played_at))];
        }
        return $dates;
    }

    public function queryComplianceWithDateResult()
    {
        $compliance_data = [];
        foreach ($this->complianceDateQuery() as $date){
            $date = date('Y-m-d', strtotime($date->played_at));
            $compliance_data[] =  \DB::table('broadcaster_playouts')
                                        ->join('selected_adslots', 'selected_adslots.id', '=','broadcaster_playouts.selected_adslot_id')
                                        ->join('companies', 'companies.id', '=', 'broadcaster_playouts.broadcaster_id')
                                        ->join('channel_company', 'channel_company.company_id', '=', 'companies.id')
                                        ->join('campaignChannels', 'campaignChannels.id', '=', 'channel_company.channel_id')
                                        ->selectRaw("IF(selected_adslots.adslot_amount IS NOT NULL, sum(selected_adslots.adslot_amount), 0) as amount,
                                                                broadcaster_playouts.broadcaster_id, selected_adslots.campaign_id, 
                                                                DATE_FORMAT(broadcaster_playouts.played_at, '%Y-%m-%d') AS time,
                                                                companies.name, campaignChannels.channel AS stack")
                                        ->whereIn('companies.id', $this->publisher_id)
                                        ->where([
                                            ['selected_adslots.campaign_id', $this->campaign_id],
                                            ['broadcaster_playouts.status', BroadcasterPlayoutStatus::PLAYED]
                                            ])
                                        ->whereRaw("DATE_FORMAT(broadcaster_playouts.played_at, '%Y-%m-%d') = '$date'")
                                        ->groupBy('broadcaster_playouts.broadcaster_id')
                                        ->get();
        }
        return $compliance_data;
    }

    /**
     * This method reformat the multidimensional array into a single array irrespective
     * of the steps in that array.
     */
    public function convertMultidimensionalArray()
    {
        //this returns the multidimensional arrays into a single array of objects
        $flattened_compliance_data = array_flatten($this->queryComplianceWithDateResult());
        //convert it back to array
        return json_decode(json_encode($flattened_compliance_data), true);
    }

    /**
     * regrouping the new result by publishers
     */
    public function groupByPublishers()
    {
        $compliance_array = [];
        foreach($this->convertMultidimensionalArray() as $key=>$value){
            if(!array_key_exists($value['broadcaster_id'],$compliance_array)){
                $compliance_array[$value['broadcaster_id']] = $value;
                unset($compliance_array[$value['broadcaster_id']]['amount']);
                $compliance_array[$value['broadcaster_id']]['amount'] =array();
                foreach ($this->complianceDateQuery() as $date){
                    $date = date('Y-m-d', strtotime($date->played_at));
                    if($date == $value['time']){
                        array_push($compliance_array[$value['broadcaster_id']]['amount'],(integer)$value['amount']);
                    }else{
                        array_push($compliance_array[$value['broadcaster_id']]['amount'],0);
                    }
                }

            }else{
                foreach ($this->complianceDateQuery() as $key => $date){
                    $date = date('Y-m-d', strtotime($date->played_at));
                    if($date == $value['time']){
                        $compliance_array[$value['broadcaster_id']]['amount'][$key] = (integer)$value['amount'];
                    }
                }
            }
        }
        return $compliance_array;
    }

    public function formatDataForGraphCompatibility()
    {
        $compliance_data = [];
        foreach ($this->groupByPublishers() as $compliance){
            if($compliance['stack'] === 'TV'){
                $color = $this->barColors()->random();
            }else{
                $color = $this->barColors()->random();
            }
            $compliance_data[] = [
                'color' => $color,
                'name' => $compliance['name'],
                'data' => $compliance['amount'],
                'stack' => $compliance['stack']
            ];
        }
        return $compliance_data;
    }

    public function barColors()
    {
        return collect([
                    '#4572A7',
                    '#AA4643',
                    '#89A54E',
                    '#80699B',
                    '#3D96AE',
                    '#DB843D',
                    '#92A8CD',
                    '#A47D7C',
                    '#B5CA92'
                ]);
    }
}
