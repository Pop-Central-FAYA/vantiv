<?php

namespace Vanguard\Http\Controllers\Api;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;

class DummyController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return response()->json(['data' => 'Hello Welcome to the api...']);
    }

    public function allFiles()
    {
        $file_details = [];
        $files = Utilities::switch_db('api')->select("SELECT * FROM files where is_file_accepted = 1 AND airbox_status = 1");
        foreach ($files as $file){
            $campaign_details = Utilities::switch_db('api')->select("SELECT * from campaigns where id = '$file->campaign_id'");
            $brand_name = $campaign_details[0]->brand;
            $channel = $campaign_details[0]->channel;
            $brand = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_name'");
            $channel_name = Utilities::switch_db('api')->select("SELECT channel from campaignChannels where id = '$channel'");
            $adslot_details = Utilities::switch_db('api')->select("SELECT * from adslots where id = '$file->adslot'");
            $day_part_id = $adslot_details[0]->day_parts;
            $day_parts = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id = '$day_part_id'");
            $target_audience_id = $adslot_details[0]->target_audience;
            $target_audience = Utilities::switch_db('api')->select("SELECT * from targetAudiences where id = '$target_audience_id'");
            $region_id = $adslot_details[0]->region;
            $region = Utilities::switch_db('api')->select("SELECT * from regions where id = '$region_id'");
            $rate_card_id = $adslot_details[0]->rate_card;
            $rate_card_details = Utilities::switch_db('api')->select("SELECT * from rateCards where id = '$rate_card_id'");
            $hourly_range_id = $rate_card_details[0]->hourly_range_id;
            $hourly_range = Utilities::switch_db('api')->select("SELECT * from hourlyRanges where id = '$hourly_range_id'");
            $day_id = $rate_card_details[0]->day;
            $day = Utilities::switch_db('api')->select("SELECT * from days where id = '$day_id'");
            $broad_id = $adslot_details[0]->broadcaster;
            $broadcaster_info = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broad_id'");
            $file_details[] = [
                'file_id' => $file->id,
                'user_id' => $file->user_id,
                'campaign_name' => $campaign_details[0]->name,
                'product_name' => $campaign_details[0]->product,
                'brand' => $brand[0]->name,
                'channel' => $channel_name[0]->channel,
                'start_date' => date('Y-m-d', strtotime($campaign_details[0]->start_date)),
                'end_date' => date('Y-m-d', strtotime($campaign_details[0]->stop_date)),
                'agency_id' => $campaign_details[0]->agency,
                'agency_broadcaster' => $campaign_details[0]->agency_broadcaster,
                'broadcaster_id' => $campaign_details[0]->broadcaster,
                'from_to_time' => $adslot_details[0]->from_to_time,
                'day_part' => $day_parts[0]->day_parts,
                'target_audience' => $target_audience[0]->audience,
                'region' => $region[0]->region,
                'minimum_age' => $adslot_details[0]->min_age,
                'maximum_age' => $adslot_details[0]->max_age,
                'hourly_range' => $hourly_range[0]->time_range,
                'day' => $day[0]->day,
                'broadcast_station' => $broadcaster_info[0]->brand,
                'file' => decrypt($file->file_url),
            ];
        }
        return response()->json(
            ['all_files' => $file_details]
        , 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addFile($id)
    {
        $check_file = Utilities::switch_db('api')->select("SELECT * from files where file_code = '$id'");
        if($check_file[0]->airbox_status === 1){
            return response()->json(['file_code' => $id], 200);
        }

        $url = decrypt($check_file[0]->file_ur);
        $path = '/media/RIDWAN/Files';

        $this->downloadFile($url, $path);

        $update_file = Utilities::switch_db('api')->update("UPDATE files set airbox_status = 1 WHERE file_code = '$id'");
        if($update_file){
            return response()->json(['file_code' => $id], 200);
        }else{
            return response()->json(['error' => 'Error Occured'], 500);
        }

    }

    public function downloadFile($url, $path)
    {
        $newfname = $path;
        $file = fopen ($url, 'rb');
        if ($file) {
            $newf = fopen ($newfname, 'wb');
            if ($newf) {
                while(!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
        }
    }

}
