<?php

namespace Vanguard\Libraries\MpsImporter;

use Illuminate\Support\Facades\DB;

class TvDiary
{

    public function import($s3_file_location)
    {
        $mps_file = new MpsFile($s3_file_location);
        $csv_file = $mps_file->download()->getCsvFileName();
        $import_time = now();

        DB::transaction(function () use ($csv_file, $import_time) {
            // $tv_user_profile = new TvUserProfile($import_time, $csv_file);
            // $profiles_imported = $tv_user_profile->process();

            $tv_user_activities = new TvUserActivities($import_time, $csv_file);
            $activities_imported = $tv_user_activities->process();
        });
    }
}