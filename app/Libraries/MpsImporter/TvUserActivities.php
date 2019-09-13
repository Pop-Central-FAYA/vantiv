<?php

namespace Vanguard\Libraries\MpsImporter;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Vanguard\Models\TvStation;
use Vanguard\Models\MpsProfileActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Vanguard\Libraries\Batch\LaravelBatch;

// $tv_profile = new \Vanguard\Libraries\MpsImporter\TvUserActivities(now(), '/tmp/tv-diarybaNkBK'); $tv_profile->process();

class TvUserActivities
{
    // const CHUNK_BATCH = 5000;
    const CHUNK_BATCH = 20000;

    public function __construct($import_time, $csv_file)
    {
        $this->import_time = $import_time;
        $this->csv_file = $csv_file;
        $this->wave = null;
    }

    public function process()
    {   
        $this->tv_station_list = TvStation::all();
        return DB::transaction(function(){
            $file_handle = fopen($this->csv_file, "r");
            $header = fgetcsv($file_handle);
            $rows_saved = $this->storeActivities($file_handle, $header);
            fclose($file_handle);
            return $rows_saved;
        });
    }

    protected function storeActivities($file_handle, $header) {
        $activity_list = [];

        $formatted_time = $this->import_time->format('Y-m-d H:i:s');
        $current_count = 0;

        Log::info("................................BEGIN PARSING OF TV DATA................................");

        while (($row_data = fgetcsv($file_handle)) !== false) {
            if (count($row_data) == 1) {
                Log::info("This row only has a count of 1, so skipping");
                continue;
            }

            $current_count++;
            $activity = [];
            $wave = $this->getWave($header, $row_data);
            $ext_profile_id = $row_data[0]; //the assumption is the ext_profile id is in the first column

            foreach ($row_data as $index => $value) {
                if ($value == 1) {
                    $key = $header[$index];
                    $station = TvStationParser::parseRgx($key);
                    if ($station->isNotEmpty()) {
                        $activity = $this->formatActivity($station);
                        if (is_array($activity)) {
                            $current_count++;

                            $activity['id'] = uniqid();
                            $activity['ext_profile_id'] = $ext_profile_id;
                            $activity['wave'] = $wave;
                            $activity['media_type'] = 'Tv';
                            $activity['created_at'] = $formatted_time;
                            $activity['updated_at'] = $formatted_time;

                            $activity_list[] = $activity;

                            echo($current_count);
                            echo('.');

                            Log::debug("{$key} = {$value}");
                            Log::debug("{$station['name']} {$station['day']} {$station['timebelt']}");
                        } else {
                            Log::warning($key);
                            Log::warning($station);
                        }
                    }
                }

                if (count($activity_list) >= static::CHUNK_BATCH) {
                    $this->insertActivities($activity_list);
                    $activity_list = [];
                }
            }
        }

        $this->insertActivities($activity_list);
        $activity_list = [];

        echo(PHP_EOL);
        Log::info("................................END PARSING OF TV DATA................................");
        return $current_count;
    }

    protected function insertActivities(&$activity_list) {
        if (count($activity_list) > 0) {
            $mps_activity = new MpsProfileActivity();
            $columns = array_keys($activity_list[0]);
            $laravel_batch = new LaravelBatch(app('db'));
            $result = $laravel_batch->insert($mps_activity, $columns, $activity_list, static::CHUNK_BATCH);
        }
    }

    protected function formatActivity($station)
    {
        $available_stations = $this->tv_station_list
            ->where('name', $station['name'])
            ->where('state', $station['state'])
            ->whereIn('city', ['', $station['city']])
            ->where('type', $station['station_type']);

        if ($available_stations->count() == 0) {
            $available_stations = $this->tv_station_list->where('name', $station['name']);
        }

        if ($available_stations->count() == 1) {
            //do the real work here
            $tv_station = $available_stations->first();
            $formatted_timebelt = $this->formatTimeBelt($station['timebelt']);
            return [
                "tv_station_id" => $tv_station->id,
                "tv_station_key" => $tv_station->key,
                "day" => $station['day'],
                "start_time" => $formatted_timebelt[0],
                "end_time" => $formatted_timebelt[1]
            ];
        }
        
        return null;
    }

    protected function formatTimeBelt($timebelt)
    {
        $items = explode('-', $timebelt);
        return [
            Str::replaceFirst('h', ':', $items[0]),
            Str::replaceFirst('h', ':', $items[1])
        ];
    }

    protected function getWave($header, $row)
    {
        if ($this->wave == null) {
            foreach ($header as $index => $field_name) {
                if ($field_name == 'Wave') {
                    $this->wave = $row[$index];
                    break;
                }
            }
        }
        return $this->wave;
    }
}
