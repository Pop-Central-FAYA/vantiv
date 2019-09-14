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
    const CHUNK_BATCH = 20000;

    public function __construct($import_time, $csv_file)
    {
        $this->formatted_time = $import_time->format('Y-m-d H:i:s');
        $this->csv_file = $csv_file;
        $this->wave = null;
    }

    public function process()
    {   
        $this->tv_station_map = $this->getTvStationMap();
        return DB::transaction(function(){
            $file_handle = fopen($this->csv_file, "r");
            $header = fgetcsv($file_handle);
            $rows_saved = $this->storeActivities($file_handle, $header);
            fclose($file_handle);
            return $rows_saved;
        });
    }

    protected function getTvStationMap()
    {
        $tv_stations = TvStation::all();
        $station_and_state = $tv_stations->groupBy(function($item) {
            return "{$item->name}{$item->state}{$item->city}";
        });
        $station_only = $tv_stations->groupBy(function($item) {
            return $item->name;
        });
        return array_merge($station_only->toArray(), $station_and_state->toArray());
    }

    protected function storeActivities($file_handle, $header) {
        $activity_list = [];
        $current_count = 0;

        Log::info("................................BEGIN PARSING OF TV DATA................................");

        while (($row_data = fgetcsv($file_handle)) !== false) {
            if (count($row_data) == 1) {
                Log::debug("This row only has a count of 1, so skipping");
                continue;
            }

            $wave = $this->getWave($header, $row_data);
            $ext_profile_id = $row_data[0]; //the assumption is the ext_profile id is in the first column

            foreach ($row_data as $index => $value) {
                if ($value == 1) {
                    $key = $header[$index];
                    $parsed_info = TvStationParser::parseRgx($key);
                    if ($parsed_info->isNotEmpty()) {
                        $activity = $this->processColumn($parsed_info, $ext_profile_id, $wave);
                        if ($activity) {
                            $activity_list[] = $activity;
                            $current_count++;
                            if (count($activity_list) >= static::CHUNK_BATCH) {
                                $this->batchInsert($activity_list);
                                $activity_list = [];
                            }
                            echo("{$current_count} ");
                        } else {
                            Log::warning("Could not parse: {$key}");
                        } 
                    } 
                }
            }
        }
        $this->batchInsert($activity_list);
        $activity_list = [];

        Log::info("................................END PARSING OF TV DATA................................");
        return $current_count;
    }

    protected function processColumn($parsed_info, $ext_profile_id, $wave)
    {
        $tv_station = $this->getTvStation($parsed_info);
        if ($tv_station) {
            $formatted_timebelt = $this->formatTimeBelt($parsed_info['timebelt']);
            return [
                "id" => uniqid(),
                'ext_profile_id' => $ext_profile_id,
                "tv_station_id" => $tv_station['id'],
                "tv_station_key" => $tv_station['key'],
                "day" => $parsed_info['day'],
                "start_time" => $formatted_timebelt[0],
                "end_time" => $formatted_timebelt[1],
                'wave' => $wave,
                'media_type' => 'Tv',
                'created_at' => $this->formatted_time,
                'updated_at' => $this->formatted_time
            ];
        } 
        return null;
    }

    protected function batchInsert(&$activity_list) {
        if (count($activity_list) > 0) {
            $mps_activity = new MpsProfileActivity();
            $columns = array_keys($activity_list[0]);
            $laravel_batch = new LaravelBatch(app('db'));
            $result = $laravel_batch->insert($mps_activity, $columns, $activity_list, static::CHUNK_BATCH);
        }
    }

    protected function getTvStation($station)
    {
        $key = "{$station['name']}{$station['state']}{$station['city']}";
        $tv_station = Arr::get($this->tv_station_map, $key, null);
        if ($tv_station) {
            return $tv_station[0];
        }
        //try with just the name as a fallback
        $tv_station = Arr::get($this->tv_station_map, $station['name'], null);
        if ($tv_station) {
            return $tv_station[0];
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
