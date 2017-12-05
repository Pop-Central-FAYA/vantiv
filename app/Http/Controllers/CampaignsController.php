<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Vanguard\Libraries\Api;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $campaign_all = Api::getCampaignByBroadcaster();
        if($campaign_all->status === true)
        {
            $campaign = $campaign_all->data;
            return view('campaign.index')->with('campaign', $campaign);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('campaign.create1');
    }

    public function createStep2($id)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $day_parts = $obj_preloaded->data->day_parts;
        $campaign_type = $obj_preloaded->data->campaign_types;
        return view('campaign.create2')->with('day_parts', $day_parts)->with('step2', Session::get('step2'));
    }

    public function createStep3($id)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $target_audience = $obj_preloaded->data->target_audience;
        return view('campaign.create3')->with('target_audience', $target_audience)->with('step3', Session::get('step3'));
    }

    public function createStep4($id)
    {
        $time = Api::get_time();
        $obj_time = json_decode($time);
        $time_sec = $obj_time->data;
        return view('campaign.create4')->with('time_in_sec', $time_sec);
    }

    public function createStep5($id)
    {
        $getCampaign = Api::getCampaignByBroadcaster();
        $campaign = $getCampaign->data;
        $time = Api::get_time();
        $obj_time = json_decode($time);
        $time_sec = $obj_time->data;
        return view('campaign.create5')->with('campaign', $campaign)
                                        ->with('time', $time_sec);
    }

    public function createStep6($id)
    {
        $getCampaign = Api::getCampaignByBroadcaster();
        $campaign = $getCampaign->data;

        $all_adslot = Api::get_adslot();
        $adslot = json_decode($all_adslot);
        $count = count(($adslot->data));
        return view('campaign.create6')->with('counting', $count);
    }

    public function createStep7($id)
    {
        return view('campaign.create7');
    }

    public function createStep8($id)
    {
        return view('campaign.create8');
    }

    public function createStep9($id)
    {
        return view('campaign.create9');
    }

    public function postStep2(Request $request, $id)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $target_audience = $obj_preloaded->data->target_audience;

        $this->validate($request, [
            'name' => 'required',
            'brand' => 'required',
            'product' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'dayparts' => 'required_without_all',
        ]);
        session(['step2' => ((object) $request->all())]);

        return redirect()->route('campaign.create3', ['id' => $id])->with('target_audience', $target_audience);

    }

    public function postStep3(Request $request, $id)
    {
        $time = Api::get_time();
        $obj_time = json_decode($time);
        $time_sec = $obj_time->data;

        $this->validate($request, [
            'target_audience' => 'required',
            'min_age' => 'required',
            'max_age' => 'required',
            'region' => 'required_without_all'
        ]);

        session(['step3' => ((object) $request->all())]);

        return redirect()->route('campaign.create4', ['id' => $id])->with('time', $time_sec);
    }

    public function postStep4(Request $request, $id)
    {
        $getCampaign = Api::getCampaignByBroadcaster();
        $campaign = $getCampaign->data;
        $all_adslot = Api::get_adslot();
        $adslot = json_decode($all_adslot);
        $count = count(($adslot->data));
        $this->validate($request, [
            'file' => 'required',
            'time' => 'required'
        ]);
        session(['step4' => ((object) $request->all())]);
        return redirect()->route('campaign.create6', ['id' => $id])->with('counting', $count);
    }

    public function getStep7($id)
    {
        $adslot = Api::get_adslot();
        $a = json_decode($adslot);
        $b = (object)($a->data);
        $count = count(($a->data));

        $file = Session::get('step4');
        $file_obj = (object)($file->file);
        $time_obj = (object)($file->time);
        $time_data = [];
        $i = 0;
        foreach ($file_obj as $file_id) {
            $time_data[] = [
                'file' => $file_id,
                'time' => $file->time[$i++]
            ];
        }
        return view('campaign.create7')->with('adslot', $b)
                                       ->with('counting', $count)
                                        ->with('data', ((object) $time_data));
//        $first = Session::get('step2');
//        $second = Session::get('step3');
//        $third = Session::get('step4');
//        dd($third);

//
//
//        dd((object) $time_data);

    }
}
