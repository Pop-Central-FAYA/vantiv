<?php

namespace Vanguard\Libraries\MpsImporter;

use Carbon\Carbon;
use Vanguard\Models\MpsProfile;
use Illuminate\Support\Facades\DB;
use Vanguard\Libraries\Batch\LaravelBatch;

// $tv_profile = new \Vanguard\Libraries\MpsImporter\TvUserProfile(now(), '/var/www/diary.csv'); $tv_profile->process();

class TvUserProfile
{
    const CHUNK_BATCH = 20000;

    public function __construct($import_time, $csv_file)
    {
        $this->import_time = $import_time;
        $this->csv_file = $csv_file;
    }

    public function process()
    {
        return DB::transaction(function (){
            $file_handle = fopen($this->csv_file, "r");
            $header = fgetcsv($file_handle);
            $rows_saved = $this->storeProfiles($file_handle, $header);
            fclose($file_handle);
            return $rows_saved;
        });
    }

    private function storeProfiles($file_handle, $header) {
        $profile_columns = $this->getProfileColumns($header);
        $profile_list = [];

        $formatted_time = $this->import_time->format("Y-m-d H:i:s");
        $current_count = 0;
        while (($row_data = fgetcsv($file_handle)) !== false) {
            if (count($row_data) == 1) {
                continue;
            }

            $current_count++;
            $profile = [];

            foreach ($profile_columns as $value) {
                $normalized_key = $value[0];
                $index = $value[1];
                $value = $row_data[$index];
                switch ($normalized_key) {
                    case "age":
                        $value = $this->normalizeAge($value);
                        break;
                    case "region":
                        $value = $this->normalizeRegion($value);
                        break;
                    case "state":
                        $value = $this->normalizeState($value);
                        break;
                    case "wave":
                        $value = $this->normalizeWave($value);
                        break;
                    default:
                        break;
                }
                $profile[$normalized_key] = $value;
            }
            $profile["created_at"] = $formatted_time;
            $profile_list[] = $profile;

            echo($current_count);
            echo('.');

            if (count($profile_list) >= static::CHUNK_BATCH) {
                $this->insertProfiles($profile_list);
                $profile_list = [];
            }
        }
        $this->insertProfiles($profile_list);
        $profile_list = [];

        echo(PHP_EOL);
        return $current_count;
    }

    private function getProfileColumns($header)
    {
        $profile_fields = [
            "IOBS" => ["ext_profile_id", 0], "Wave" => "wave",
            "Exact Age" => "age", "Gender" => "gender",
            "Area-State Location" => "state", "Social Class" => "social_class",
            "Region Of Survey" => "region", "Weight" => "pop_weight"
        ];
        $fields = array_keys($profile_fields);
        foreach ($header as $index => $key) {
            if (in_array($key, $fields)) {
                $normalized_key = $profile_fields[$key];

                if (is_array($normalized_key) === false) {
                    $profile_fields[$key] = [$normalized_key, $index];
                } else {
                    $normalized_key[1] = $index;
                    $profile_fields[$key] = $normalized_key;
                }
            }
        }
        return $profile_fields;
    }

    private function insertProfiles($profile_list) {
        if (count($profile_list) > 0) {
            $mps_profile = new MpsProfile();
            $columns = array_keys($profile_list[0]);
            $laravel_batch = new LaravelBatch(app("db"));
            $laravel_batch->insert($mps_profile, $columns, $profile_list, static::CHUNK_BATCH);
        }
    }
    
    private function normalizeWave($value) 
    {
        $value = $value . " 01";
        return Carbon::createFromFormat("F 'y d", $value)->toDateString(); 
    }

    private function normalizeState($value) {
        if ($value == "CrossRiver") {
            $value = "Cross River";
        }
        return $value;
    }

    private function normalizeAge($value) {
        return (int) str_replace(" years", "", strtolower($value));
    }

    private function normalizeRegion($value) {
        switch ($value) {
            case "North Central (Abuja)":
                $value = "North Central";
                break;
            case "North-East (Jos)":
                $value = "North East";
                break;
            case "North-West":
                $value = "North West";
                break;
            case "South-East (Onitsha, Aba)":
                $value = "South East";
                break;
            case "South-South (Rivers)":
                $value = "South South";
                break;
            case "South-West (Ibadan, Benin)":
                $value = "South West";
                break;
            default:
                break;
        }
        return $value;
    }
}
