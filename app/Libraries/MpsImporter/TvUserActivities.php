<?php

namespace Vanguard\Libraries\MpsImporter;

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
        return DB::transaction(function() {
            $file_handle = fopen($this->csv_file, "r");
            $header = fgetcsv($file_handle);
            $rows_saved = $this->storeActivities($file_handle, $header);
            fclose($file_handle);
            return $rows_saved;
        });
    }

    /**
     * Get all the tv stations from the backend and create a mapping of them
     * for constant time access (This will save a ton of time when generating the activities)
     */
    private function getTvStationMap()
    {
        return TvStation::all()->groupBy(function($item) {
            return $this->generateTvStationKey($item);
        });
    }

    private function generateTvStationKey($item) {
        return "{$item['name']}-{$item['state']}-{$item['city']}";
    }

    private function storeActivities($file_handle, $header) {
        $activity_list = [];
        $current_count = 0;

        Log::info("................................BEGIN PARSING OF TV DATA ACTIVITIES................................");

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
                    $parsed_info = TvStationParser::parse($key);
                    
                    if ($parsed_info === null) {
                        continue;
                    }

                    $activity = $this->getModelAttributes($parsed_info, $ext_profile_id, $wave);
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
        $this->batchInsert($activity_list);
        $activity_list = [];

        Log::info("................................END PARSING OF TV DATA ACTIVITIES................................");
        return $current_count;
    }

    /**
     * Get an format the wave as month and year
     * Wave is the month the data was collected
     */
    private function getWave($header, $row)
    {
        if ($this->wave === null) {
            foreach ($header as $index => $field_name) {
                if ($field_name == 'Wave') {
                    $this->wave = $row[$index];
                    break;
                }
            }
        }
        return $this->wave;
    }

    private function getModelAttributes($parsed_info, $ext_profile_id, $wave)
    {
        $key = $this->generateTvStationKey($parsed_info);
        $tv_station = $this->tv_station_map->get($key);

        if ($tv_station === null) {
            return null;
        }
        return [
            "ext_profile_id" => $ext_profile_id,
            "wave" => $wave,
            "tv_station_key" => $tv_station["key"],
            "day" => $parsed_info["day"],
            "broadcast_type" => $parsed_info["broadcast_type"],
            "start_time" => $this->formatTimeBelt($parsed_info["start_time"]),
            "end_time" => $this->formatTimeBelt($parsed_info["end_time"]),
            "media_type" => "Tv",
            "created_at" => $this->formatted_time
        ];
    }

    private function formatTimeBelt($timebelt)
    {
        return Str::replaceFirst("h", ":", $timebelt);
    }

    /**
     * We are using LaravelBatch because using default laravel insertion seems to 
     * be inefficient and causes memory leakage
     * @todo switch to using normal eloquent batch insertion
     */
    private function batchInsert(&$activity_list) {
        if (count($activity_list) > 0) {
            $mps_activity = new MpsProfileActivity();
            $columns = array_keys($activity_list[0]);
            $laravel_batch = new LaravelBatch(app('db'));
            $laravel_batch->insert($mps_activity, $columns, $activity_list, static::CHUNK_BATCH);
        }
    }
}
