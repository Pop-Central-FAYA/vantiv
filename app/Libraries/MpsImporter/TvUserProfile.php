<?php

namespace Vanguard\Libraries\MpsImporter;

use Vanguard\Models\MpsProfile;
use Illuminate\Support\Facades\DB;
use Vanguard\Libraries\Batch\LaravelBatch;

// $tv_profile = new \Vanguard\Libraries\MpsImporter\TvUserProfile(now(), '/tmp/tv-diarybaNkBK'); $tv_profile->process();

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

    protected function storeProfiles($file_handle, $header) {
        $profile_columns = $this->getProfileColumns($header);
        $profile_list = [];

        $formatted_time = $this->import_time->format('Y-m-d H:i:s');
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
                    case 'age':
                        $value = $this->normalizeAge($value);
                        break;
                    case 'region':
                        $value = $this->normalizeRegion($value);
                    case 'state':
                        $value = $this->normalizeState($value);
                    default:
                        break;
                }
                $profile[$normalized_key] = $value;
            }
            $profile['id'] = uniqid();
            $profile['created_at'] = $formatted_time;
            $profile['updated_at'] = $formatted_time;

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

    protected function insertProfiles($profile_list) {
        if (count($profile_list) > 0) {
            $mps_profile = new MpsProfile();
            $columns = array_keys($profile_list[0]);
            $laravel_batch = new LaravelBatch(app('db'));
            $result = $laravel_batch->insert($mps_profile, $columns, $profile_list, static::CHUNK_BATCH);
        }
    }
    
    protected function normalizeState($value) {
        if ($value == 'CrossRiver') {
            $value = 'Cross River';
        }
        return $value;
    }

    protected function normalizeAge($value) {
        return (int) str_replace(' years', '', strtolower($value));
    }

    protected function normalizeRegion($value) {
        switch ($value) {
            case 'North Central (Abuja)':
                $value = "North Central";
                break;
            case 'North-East (Jos)':
                $value = "North East";
                break;
            case 'North-West':
                $value = "North West";
                break;
            case 'South-East (Onitsha, Aba)':
                $value = "South East";
                break;
            case 'South-South (Rivers)':
                $value = "South South";
                break;
            case 'South-West (Ibadan, Benin)':
                $value = "South West";
                break;
            default:
                break;
        }
        return $value;
    }

    protected function getProfileColumns($header)
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
                $profile_fields[$key] = [$normalized_key, $index];
            }
        }
        return $profile_fields;
    }
    
}
