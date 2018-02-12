<?php

namespace Vanguard\Http\Controllers\Advertiser;

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
        $all_mpo = [];
        $advertiser_id = Session::get('advertiser_id');
        $invoice = Utilities::switch_db('api')->select("SELECT * from invoices WHERE agency_id = '$advertiser_id'");
        $file = Utilities::switch_db('api')->select("SELECT * from files WHERE agency_id = '$advertiser_id'");
        $mpos = Utilities::switch_db('api')->select("SELECT * from mpos where agency_id = '$advertiser_id'");
        foreach ($mpos as $mpo){
            $campaign = Utilities::switch_db('api')->select("SELECT * FROM campaigns where id = '$mpo->campaign_id'");
            $campaign_id = $campaign[0]->id;
            $camp_id = $campaign[0]->adslots_id;
            $total = Utilities::switch_db('api')->select("SELECT amount from payments where campaign_id = '$mpo->campaign_id'");
            $brand_id = $campaign[0]->brand;
            $brand = Utilities::switch_db('api')->select("SELECT `name` from brands where id = '$brand_id'");
            $adslots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE id IN ($camp_id)");

            if($adslots){
                $slot = $adslots;
            }else{
                $slot = $adslots;
            }
            $all_mpo[] = [
                'id' => $mpo->id,
                'campaign_id' => $campaign_id,
                'campaign_name' => $campaign[0]->name,
                'brand' => $brand[0]->name,
                'adslot' => $slot,
                'discount' => $mpo->discount,
                'total' => $total[0]->amount,
            ];
        }

        return view('advertisers.campaigns.all_campaign')->with('invoices', $invoice)->with('files', $file)->with('mpos', $all_mpo);
    }

    public function getData(DataTables $datatables, Request $request)
    {
        $campaign = [];
        $j = 1;
        $agency_id = \Session::get('advertiser_id');
        $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE agency = '$agency_id' AND adslots > 0 ORDER BY time_created desc");
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
            ->addColumn('invoices', function($campaign){
                return '<button data-toggle="modal" data-target=".invoiceModal' . $campaign['camp_id']. '" class="btn btn-success btn-xs" > View Details </button>    ';
            })
            ->rawColumns(['mpo' => 'mpo', 'invoices' => 'invoices'])->addIndexColumn()
            ->make(true);
    }

    public function getStep1($id)
    {
        $industry = Utilities::switch_db('api')->select("SELECT id, `name` from sectors");
        $chanel = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        $brands = Utilities::switch_db('api')->select("SELECT * from brands WHERE broadcaster_agency = '$id'");
        $day_parts = Utilities::switch_db('api')->select("SELECT * from dayParts");
        $region = Utilities::switch_db('api')->select("SELECT * from regions");
        $target = Utilities::switch_db('api')->select("SELECT * from targetAudiences");
        return view('advertisers.campaigns.create1')->with('industry', $industry)
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

        $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$id'");
        $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$id'");

        if(strtotime($request->end_date) < strtotime($request->start_date)){
            return redirect()->back()->with('error', 'Start Date cannot be greater than End Date');
        }
        $step1_req = ((object) $request->all());
        session(['step1' => $step1_req]);

        return redirect()->route('advertiser_campaign.step2', ['id' => $id])->with('step_1', Session::get('step1'))
            ->with('id', $id);

    }

    public function getStep2($id)
    {
        $step1 = Session::get('step1');
        if(!$step1){
            return back()->with('error', 'Data lost, please go back and select your filter criteria');
        }
        $day_parts = implode("','" ,$step1->dayparts);
        $region = implode("','", $step1->region);
        $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience = '$step1->target_audience' AND day_parts IN ('$day_parts') AND region IN ('$region') AND is_available = 0 group by broadcaster");

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

        return view('advertisers.campaigns.create2')->with('adslots', $ads_broad)
            ->with('id', $id);
    }

    public function getStep3($id, $broadcaster)
    {
        return view('advertisers.campaigns.create3')->with('id', $id)
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
                $uploads = \DB::select("SELECT * from uploads where user_id = '$id' AND time = '$time'");
                if(count($uploads) === 1){
                    return back()->with('error', 'You cannot upload twice for this time slot');
                }
                $insert_upload = \DB::table('uploads')->insert([
                    'user_id' => $id,
                    'time' => $time,
                    'uploads' => $file_gan_gan
                ]);

                if($insert_upload){
                    return redirect()->route('advertiser_campaign.step3_1', ['id' => $id, 'broadcaster' => $broadcaster]);
                }else{
                    return back()->with('error','Could not complete upload process');
                }
            }

        }
    }

    public function getStep3_1($id, $broadcaster)
    {
        return view('advertisers.campaigns.create3_1')->with('id', $id)
            ->with('broadcaster', $broadcaster);
    }

    public function postStep3_1(Request $request, $id, $broadcaster)
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
                $uploads = \DB::select("SELECT * from uploads where user_id = '$id' AND time = '$time'");
                if(count($uploads) === 1){
                    return back()->with('error', 'You cannot upload twice for this time slot');
                }
                $insert_upload = \DB::table('uploads')->insert([
                    'user_id' => $id,
                    'time' => $time,
                    'uploads' => $file_gan_gan
                ]);

                if($insert_upload){
                    return redirect()->route('advertiser_campaign.step3_2', ['id' => $id, 'broadcaster' => $broadcaster]);
                }else{
                    return back()->with('error','Could not complete upload process');
                }
            }

        }
    }

    public function getStep3_2($id, $broadcaster)
    {
        return view('advertisers.campaigns.create3_2')->with('id', $id)
            ->with('broadcaster', $broadcaster);
    }

    public function postStep3_2(Request $request, $id, $broadcaster)
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
                $uploads = \DB::select("SELECT * from uploads where user_id = '$id' AND time = '$time'");
                if(count($uploads) === 1){
                    return back()->with('error', 'You cannot upload twice for this time slot');
                }
                $insert_upload = \DB::table('uploads')->insert([
                    'user_id' => $id,
                    'time' => $time,
                    'uploads' => $file_gan_gan
                ]);

                if($insert_upload){
                    return redirect()->route('advertiser_campaign.step3_3', ['id' => $id, 'broadcaster' => $broadcaster]);
                }else{
                    return back()->with('error','Could not complete upload process');
                }
            }

        }
    }

    public function getStep3_3($id, $broadcaster)
    {
        return view('advertisers.campaigns.create3_3')->with('id', $id)
            ->with('broadcaster', $broadcaster);
    }

    public function postStep3_3(Request $request, $id, $broadcaster)
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
                $uploads = \DB::select("SELECT * from uploads where user_id = '$id' AND time = '$time'");
                if(count($uploads) === 1){
                    return back()->with('error', 'You cannot upload twice for this time slot');
                }
                $insert_upload = \DB::table('uploads')->insert([
                    'user_id' => $id,
                    'time' => $time,
                    'uploads' => $file_gan_gan
                ]);

                if($insert_upload){
                    return redirect()->route('advertiser_campaign.review_uploads', ['id' => $id, 'broadcaster' => $broadcaster]);
                }else{
                    return back()->with('error','Could not complete upload process');
                }
            }

        }
    }

    public function reviewUploads($id, $broadcaster)
    {
        $uploads = \DB::select("SELECT * from uploads where user_id = '$id'");
        return view('advertisers.campaigns.review')->with('uploads', $uploads)->with('id', $id)->with('broadcaster', $broadcaster);
    }

    public function getStep4($id, $broadcaster)
    {
        $rate_card = [];
        $step1 = Session::get('step1');
        if(!$step1){
            return back()->with('error', 'Data lost, please go back and select your filter criteria');
        }
        $day_parts = implode("','" ,$step1->dayparts);
        $region = implode("','", $step1->region);
        $adslots_count = Utilities::switch_db('api')->select("SELECT * FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience = '$step1->target_audience' AND day_parts IN ('$day_parts') AND region IN ('$region') AND is_available = 0 AND broadcaster = '$broadcaster'");
        $result = count($adslots_count);
        $ratecards = Utilities::switch_db('api')->select("SELECT * from rateCards WHERE id IN (SELECT rate_card FROM adslots where min_age >= $step1->min_age 
                                                            AND max_age <= $step1->max_age 
                                                            AND target_audience = '$step1->target_audience' 
                                                            AND day_parts IN ('$day_parts') AND region IN ('$region') 
                                                            AND is_available = 0 AND broadcaster = '$broadcaster')");

        foreach ($ratecards as $ratecard){
            $day = Utilities::switch_db('api')->select("SELECT * from days where id = '$ratecard->day'");
            $hourly_range = Utilities::switch_db('api')->select("SELECT * from hourlyRanges where id = '$ratecard->hourly_range_id'");
            $adslots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE rate_card = '$ratecard->id' AND is_available = 0");
            $price = Utilities::switch_db('api')->select("SELECT * from adslotPrices WHERE adslot_id IN (SELECT id from adslots WHERE rate_card = '$ratecard->id')");
            $rate_card[] = [
                'id' => $ratecard->id,
                'hourly_range' => $hourly_range[0]->time_range,
                'day' => $day[0]->day,
                'adslot' => $adslots,
                'price' => $price,
            ];
        }

        $time = [15, 30, 45, 60];

        $data = \DB::select("SELECT * from uploads WHERE user_id = '$id'");
        $cart = \DB::select("SELECT * from carts WHERE user_id = '$id'");
        return view('advertisers.campaigns.create4')->with('ratecards', $rate_card)->with('result', $result)->with('cart', $cart)->with('datas', $data)->with('times', $time)->with('id', $id)->with('broadcaster', $broadcaster);
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
        $day_parts = implode("','" ,$first->dayparts);
        $region = implode("','", $first->region);
        $brands = Utilities::switch_db('api')->select("SELECT name from brands where id = '$first->brand'");
        $day_partss = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id IN ('$day_parts') ");
        $targets = Utilities::switch_db('api')->select("SELECT audience from targetAudiences where id = '$first->target_audience'");
        $regions = Utilities::switch_db('api')->select("SELECT region from regions where id IN ('$region') ");
        $calc = \DB::select("SELECT SUM(price) as total_price FROM carts WHERE user_id = '$id'");
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$id'");
        return view('advertisers.campaigns.checkout')->with('first_session', $first)
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

        $user_id = $id;

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

        //NB: The advertiser is assumed to be its own agency so, the agency_id and agency_broadcaster is same as the advertiser_id and the advertiser_broadcaster.

        $adssss = implode(',' ,$ads);
        $campaign_id = uniqid();
        $pay_id = uniqid();
        $advertiser_id = Utilities::switch_db('api')->select("SELECT user_id from advertisers where id = '$id'");
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
            'region' => implode(',' ,$first->region),
            'min_age' => (integer)$first->min_age,
            'max_age' => (integer)$first->max_age,
            'industry' => $first->industry,
            'adslots' => count($query),
            'walkins_id' => $advertiser_id[0]->user_id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'adslots_id' => "'". implode("','" ,$ads) . "'",
            'adslots' => count($query),
            'agency' => Session::get('advertiser_id'),
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
                    'file_name' => encrypt($q->file),
                    'file_url' => encrypt($q->file),
                    'adslot' => $q->adslot_id,
                    'user_id' => $id,
                    'file_code' => mt_rand(100000, 10000000).uniqid(),
                    'time_created' => date('Y-m-d H:i:s', $now),
                    'time_modified' => date('Y-m-d H:i:s', $now),
                    'agency_id' => Session::get('advertiser_id'),
                    'agency_broadcaster' => $broadcaster,
                    'time_picked' => $q->time,
                ];
            }

            $pay[] = [
                'id' => $pay_id,
                'campaign_id' => $camp_id[0]->id,
                'payment_method' => $request->payment,
                'amount' => (integer) $request->total,
                'time_created' => $now,
                'time_modified' => $now,
                'walkins_id' => $advertiser_id[0]->user_id,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
                'agency_id' => Session::get('advertiser_id'),
                'agency_broadcaster' => $broadcaster,
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
                    'walkins_id' => $advertiser_id[0]->user_id,
                    'agency_id' => Session::get('advertiser_id'),
                    'agency_broadcaster' => $broadcaster,

                ];

                $mpo[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'discount' => 0,
                    'agency_id' => Session::get('advertiser_id'),
                    'agency_broadcaster' => $broadcaster
                ];

                $save_invoice = Utilities::switch_db('api')->table('invoices')->insert($invoice);

                $save_mpo = Utilities::switch_db('api')->table('mpos')->insert($mpo);

                if($save_invoice && $save_mpo){
                    foreach ($query as $q){
                        $get_slots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE id = '$q->adslot_id'");
                        $id = $get_slots[0]->id;
                        $time_difference = $get_slots[0]->time_difference;
                        $time_used = $get_slots[0]->time_used;
                        $time = $q->time;
                        $new_time_used = $time_used + $time;
                        if($time_difference === $new_time_used){
                            $slot_status = 1;
                        }else{
                            $slot_status = 0;
                        }
                        $update_slot = Utilities::switch_db('api')->update("UPDATE adslots SET time_used = '$new_time_used', is_available = '$slot_status' WHERE id = '$id'");
                    }

                    $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$user_id'");
                    $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$user_id'");
                    Session::forget('step1');

                    return redirect()->route('advertiser.campaign.all')->with('success', 'campaign created successfully');

                }
            }

        }else{
            return redirect()->back()->with('error', 'Could not create this campaign');
        }

    }


}