<?php

namespace Vanguard\Http\Controllers\Agency;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Pbmedia\LaravelFFMpeg\FFMpeg;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Session;



class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agency_id = Session::get('agency_id');
        $invoice = Utilities::switch_db('api')->select("SELECT * from invoices WHERE agency_id = '$agency_id'");
        $file = Utilities::switch_db('api')->select("SELECT * from files WHERE agency_id = '$agency_id'");
        $mpo = Utilities::switch_db('api')->select("SELECT * from mpos where agency_id = '$agency_id'");
        return view('agency.campaigns.all_campaign')->with('invoice', $invoice)->with('files', $file)->with('mpo', $mpo);
    }

    public function getData(DataTables $datatables, Request $request)
    {
        $campaign = [];
        $j = 1;
        $agency_id = \Session::get('agency_id');
        $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE agency = '$agency_id' AND adslots > 0 ORDER BY time_created asc");
        foreach ($all_campaign as $cam)
        {
            $brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE id = '$cam->brand'");
            $pay = Utilities::switch_db('api')->select("SELECT amount from payments WHERE campaign_id = '$cam->id'");
            $campaign[] = [
                'id' => $j,
                'camp_id' => $cam->id,
                'name' => $cam->name,
                'brand' => $brand[0]->name,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'amount' => '&#8358;'.number_format($pay[0]->amount, 2),
            ];
            $j++;
        }
        return $datatables->collection($campaign)
            ->addColumn('mpo', function ($campaign) {
                return '<button data-toggle="modal" data-target=".mpoModal' . $campaign['camp_id']. '" class="btn btn-success btn-xs" > View Details </button>';
            })
            ->addColumn('invoice', function($campaign){
                return '<button data-toggle="modal" data-target=".invoiceModal' . $campaign['camp_id']. '" class="btn btn-success btn-xs" > View Details </button>    ';
            })
            ->rawColumns(['mpo' => 'mpo', 'invoice' => 'invoice'])->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allClient()
    {
        $cl = [];
        $agency_id = \Session::get('agency_id');
        $all_clients = Utilities::switch_db('api')->select("SELECT * from walkIns WHERE agency_id = '$agency_id'");
        foreach ($all_clients as $all)
        {
            $clients = \DB::select("SELECT * from users where id= '$all->user_id'");
            $cl[] = $clients;

        }
        $page = \Input::get('page', 1);
        $perPage = 15;
        $offSet = ($page * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($cl, $offSet, $perPage, true);
        $data =  new LengthAwarePaginator($itemsForCurrentPage, count($cl), $perPage, $page);
        $data->setPath('all-clients');
        return view('agency.campaigns.client.index')->with('client', $data);
    }

    public function getStep1($id)
    {
        $industry = Utilities::switch_db('api')->select("SELECT id, `name` from sectors");
        $chanel = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        $walkins = Utilities::switch_db('api')->select("SELECT id from walkIns where user_id='$id'");
        $walkins_id = $walkins[0]->id;
        $brands = Utilities::switch_db('api')->select("SELECT * from brands WHERE walkin_id = '$walkins_id'");
        $day_parts = Utilities::switch_db('api')->select("SELECT * from dayParts");
        $region = Utilities::switch_db('api')->select("SELECT * from regions");
        $target = Utilities::switch_db('api')->select("SELECT * from targetAudiences");
        return view('agency.campaigns.create1')->with('industry', $industry)
            ->with('chanel', $chanel)
            ->with('brands', $brands)
            ->with('region', $region)
            ->with('day_part', $day_parts)
            ->with('target', $target)
            ->with('step1', \Session::get('step1'))
            ->with('id', $id);
    }

    public function postStep1(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'brand' => 'required',
            'product' => 'required',
            'channel' => 'required',
            'min_age' => 'required',
            'max_age' => 'required',
            'target_audience' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'industry' => 'required',
            'dayparts' => 'required_without_all',
            'region' => 'required',
        ]);

        if(strtotime($request->end_date) < strtotime($request->start_date)){
            return redirect()->back()->with('error', 'Start Date cannot be greater than End Date');
        }
        $step1_req = ((object) $request->all());
        session(['step1' => $step1_req]);

        return redirect()->route('agency_campaign.step2', ['id' => $id])->with('step_1', Session::get('step1'))
            ->with('id', $id);

    }

    public function getStep2($id)
    {
        $step1 = Session::get('step1');
        if(!$step1){
            return back()->with('error', 'Data lost, please go back and select your filter criteria');
        }
        $day_parts = "'". implode("','" ,$step1->dayparts) . "'";
        $region = "'". implode("','", $step1->region) ."'";
        $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience = '$step1->target_audience' AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 group by broadcaster");

        $ads_broad = [];
        foreach ($adslots as $adslot)
        {
            $broad = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id = '$adslot->broadcaster'");
            $ads_broad[] = [
                'broadcaster' => $adslot->broadcaster,
                'count_adslot' => $adslot->all_slots,
                'boradcaster_brand' => $broad[0]->brand,
            ];
        }

        return view('agency.campaigns.create2')->with('adslots', $ads_broad)
            ->with('id', $id);
    }

    public function getStep3($id, $broadcaster)
    {
        return view('agency.campaigns.create3')->with('id', $id)
            ->with('broadcaster', $broadcaster);
    }

    public function postStep3(Request $request, $id, $broadcaster)
    {

        $this->validate($request, [
            'uploads' => 'required|max:20000',
            'time' => 'required'
        ]);

        if(((int)$request->f_du) > ((int)$request->time)){
            return redirect()->back()->with('error','Your video file duration cannot be more than the time slot you picked');
        }

        if ($request->file('uploads')) {
            $filesUploaded = $request->uploads;
            $extension = $filesUploaded->getClientOriginalExtension();
            if($extension == 'mp4' || $extension == 'wma' || $extension == 'ogg' || $extension == 'mkv'){

                $destinationPath = 'uploads';
                $filesUploaded->move($destinationPath,$filesUploaded->getClientOriginalName());
                $file_gan_gan = 'uploads/'.$filesUploaded->getClientOriginalName();

                $time = $request->time;
                $inser_upload = \DB::table('uploads')->insert([
                    'user_id' => $id,
                    'time' => $time,
                    'uploads' => $file_gan_gan
                ]);

                if($inser_upload){
                    return redirect()->route('agency_campaign.step4', ['id' => $id, 'broadcaster' => $broadcaster]);
                }else{
                    return back()->with('error','Could not complete upload process');
                }
            }

        }
    }

    public function getStep4($id, $broadcaster)
    {
        $step1 = Session::get('step1');
        $day_parts = "'". implode("','" ,$step1->dayparts) . "'";
        $region = "'". implode("','", $step1->region) ."'";
        $adslots = Utilities::switch_db('api')->select("SELECT * FROM adslots where broadcaster = '$broadcaster' AND min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience = '$step1->target_audience' AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 GROUP BY rate_card");
        $adslot_array = [];
        $in_b = [];
        foreach ($adslots as $ads)
        {
            $rate = Utilities::switch_db('api')->select("SELECT * from rateCards WHERE id = '$ads->rate_card'");
            $adss = Utilities::switch_db('api')->select("SELECT * from adslots WHERE rate_card = '$ads->rate_card' AND is_available = 0");
            $day_id = $rate[0]->day;
            $hourly = $rate[0]->hourly_range_id;
            $day = Utilities::switch_db('api')->select("SELECT * from days WHERE id = '$day_id'");
            $hourly = Utilities::switch_db('api')->select("SELECT * from hourlyRanges where id ='$hourly' ");

            $adslot_array[] = [
                'rate_id' => $rate[0]->id,
                'hourly_range' => (object)[
                    'id' => $hourly[0]->id,
                    'time_range' => $hourly[0]->time_range
                ],
                'day' => (object)[
                    'id' => $day[0]->id,
                    'day' => $day[0]->day,
                ],
                'adslots' => $adss
            ];
        }
        $file = \DB::select("SELECT * from uploads where user_id = '$id'");
        $cart = \DB::select("SELECT * from carts WHERE user_id = '$id'");
        return view('agency.campaigns.create4')->with('id', $id)
            ->with('broadcaster', $broadcaster)
            ->with('rate', $adslot_array)
            ->with('file_upload', $file)
            ->with('cart', $cart);
    }

    public function postCart(Request $request, $id, $broadcaster)
    {
        $this->validate($request, [
            'price' => 'required',
            'file' => 'required',
            'time' => 'required',
            'rate_id' => 'required',
            'adslot_id' => 'required|unique:carts'
        ]);
        $price = $request->price;
        $file = $request->file;
        $time = $request->time;
        $rate_id = $request->rate_id;
        $hourly_range = $request->range;
        $agency = Session::get('agency_id');
        $adslot_id = $request->adslot_id;
        $ip = \Request::ip();
        $insert = \DB::insert("INSERT INTO carts (user_id, price, ip_address, file, from_to_time, `time`, rate_id, adslot_id, agency_id, broadcaster_slot) VALUES ('$id','$price','$ip','$file','$hourly_range','$time','$rate_id', '$adslot_id','$agency','$broadcaster')");
        if($insert){
            return "success";
        }else{
            return "failure";
        }
    }

    public function checkout($id, $broadcaster)
    {
        $first = Session::get('step1');
        $day_parts = "'". implode("','" ,$first->dayparts) . "'";
        $region = "'". implode("','", $first->region) ."'";
        $brands = Utilities::switch_db('api')->select("SELECT name from brands where id = '$first->brand'");
        $day_partss = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id IN ($day_parts) ");
        $targets = Utilities::switch_db('api')->select("SELECT audience from targetAudiences where id = '$first->target_audience'");
        $regions = Utilities::switch_db('api')->select("SELECT region from regions where id IN ($region) ");
        $calc = \DB::select("SELECT SUM(price) as total_price FROM carts WHERE user_id = '$id'");
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$id'");
        return view('agency.campaigns.checkout')->with('first_session', $first)
            ->with('calc', $calc)
            ->with('day_part', $day_partss)
            ->with('region', $regions)
            ->with('target', $targets)
            ->with('query', $query)
            ->with('brand', $brands)
            ->with('id', $id)
            ->with('broadcaster', $broadcaster);
    }

    public function removeCart($id)
    {
        $ads = $id;
        $del = \DB::select("DELETE FROM carts WHERE adslot_id = '$ads'");
        return redirect()->back()->with('success', trans('Item deleted from cart successfully'));
    }

    Public function postCampaign(Request $request, $id, $broadcaster)
    {
        $first = Session::get('step1');
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$id'");
        $ads = [];
        foreach ($query as $q)
        {
            $ads[] = $q->adslot_id;
        }
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$id'");
        $request->all();
        $new_q = [];
        $pay = [];
        $camp = [];
        $invoice = [];
        $mpo = [];
        $i = 0;
        $adssss = "'". implode("','" ,$ads) . "'";
        $campaign_id = uniqid();
        $pay_id = uniqid();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns where user_id = '$id'");
        $now = strtotime(Carbon::now('Africa/Lagos'));
        $camp[] = [
            'id' => $campaign_id,
            'user_id' => $id,
            'channel' => $first->channel,
            'brand' => $first->brand,
            'start_date' => date('Y-m-d', strtotime($first->start_date)),
            'stop_date' => date('Y-m-d', strtotime($first->end_date)),
            'name' => $first->name,
            'product' => $first->product,
            'day_parts' => "'". implode("','" ,$first->dayparts) . "'",
            'target_audience' => $first->target_audience,
            'region' => "'". implode("','" ,$first->region) . "'",
            'min_age' => (integer)$first->min_age,
            'max_age' => (integer)$first->max_age,
            'industry' => $first->industry,
            'adslots' => count($query),
            'walkins_id' => $walkin_id[0]->id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'adslots_id' => "'". implode("','" ,$ads) . "'",
            'adslots' => count($query),
            'agency' => Session::get('agency_id'),
            'agency_broadcaster' => $broadcaster,
        ];

        $save_campaign = Utilities::switch_db('api')->table('campaigns')->insert($camp);

        if($save_campaign){
            $camp_id = Utilities::switch_db('api')->select("SELECT id from campaigns WHERE id='$campaign_id'");
            foreach($query as $q)
            {
//            $adslot = Utilities::switch_db('api')->select("SELECT id from adslots where id='$q->rate_id'");
                $new_q[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'file_name' => $q->file,
                    'file_url' => $q->file,
                    'adslot' => $q->adslot_id,
                    'user_id' => $id,
                    'file_code' => uniqid(),
                    'time_created' => date('Y-m-d H:i:s', $now),
                    'time_modified' => date('Y-m-d H:i:s', $now),
                    'agency_id' => Session::get('agency_id'),
                    'agency_broadcaster' => $broadcaster
                ];
            }

            $pay[] = [
                'id' => $pay_id,
                'campaign_id' => $camp_id[0]->id,
                'payment_method' => $request->payment,
                'amount' => (integer) $request->total,
                'time_created' => $now,
                'time_modified' => $now,
                'broadcaster' => Session::get('broadcaster_id'),
                'walkins_id' => $walkin_id[0]->id,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
                'agency_id' => Session::get('agency_id'),
                'agency_broadcaster' => $broadcaster
            ];

            $save_payment = Utilities::switch_db('api')->table('payments')->insert($pay);

            $save_file = Utilities::switch_db('api')->table('files')->insert($new_q);

            if($save_payment && $save_file){
                $payment_id = Utilities::switch_db('api')->select("SELECT id from payments WHERE id='$pay_id'");

                $invoice[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'user_id' => $id,
                    'payment_id' => $payment_id[0]->id,
                    'invoice_number' => rand(10000, 10000000),
                    'actual_amount_paid' => (integer) $request->total,
                    'refunded_amount' => 0,
                    'walkins_id' => $walkin_id[0]->id,
                    'agency_id' => Session::get('agency_id'),
                    'agency_broadcaster' => $broadcaster,
                ];

                $mpo[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'discount' => 0,
                    'agency_id' => Session::get('agency_id'),
                    'agency_broadcaster' => $broadcaster
                ];

                $save_invoice = Utilities::switch_db('api')->table('invoices')->insert($invoice);

                $save_mpo = Utilities::switch_db('api')->table('mpos')->insert($mpo);

                if($save_invoice && $save_mpo){
                    $update_adslots = Utilities::switch_db('api')->select("UPDATE adslots SET is_available = 1 WHERE id IN ($adssss)");
                    $del_cart = \DB::select("DELETE FROM carts WHERE user_id = '$id'");
                    $del_uplaods = \DB::select("DELETE FROM uploads WHERE user_id = '$id'");
                    return redirect()->route('agency.campaign.all')->with('success', 'campaign created successfully');
                    Session::forget('step1');
                }
            }

        }else{
            return redirect()->back()->with('error', 'Could not create this campaign');
        }

    }


}