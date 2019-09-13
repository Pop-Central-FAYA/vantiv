<?php

namespace Vanguard\Libraries\MpsImporter;

use Illuminate\Support\Facades\DB;
use Log;

class TvDiary
{

    public function import($bucket, $key)
    {
        $mps_file = new MpsFile($bucket, $key);
        $csv_file = $mps_file->download()->getCsvFileName();
        $import_time = now();

        $results = DB::transaction(function () use ($csv_file, $import_time) {
            Log::info("About to process tv user profile");
            $tv_user_profile = new TvUserProfile($import_time, $csv_file);
            $profiles_imported = $tv_user_profile->process();
            Log::info("Processed {$profiles_imported} user profiles");

            Log::info('About to process tv profile activitiesx');
            $tv_user_activities = new TvUserActivities($import_time, $csv_file);
            $activities_imported = $tv_user_activities->process();
            Log::info("Processed {$activities_imported} activities");

            //delete old data (for both profiles and activities)
            return [$profiles_imported, $activities_imported];
        });

        $mps_file->cleanup();

        return $results;
    }
}