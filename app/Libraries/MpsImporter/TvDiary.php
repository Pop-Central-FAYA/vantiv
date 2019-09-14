<?php

namespace Vanguard\Libraries\MpsImporter;

use Illuminate\Support\Facades\DB;
use Log;
use Vanguard\Models\MpsProfile;
use Vanguard\Models\MpsProfileActivity;

class TvDiary
{

    public function import($bucket, $key)
    {
        $mps_file = new MpsFile($bucket, $key);
        $csv_file = $mps_file->download()->getCsvFileName();
        $import_time = now();

        $results = DB::transaction(function () use ($csv_file, $import_time) {
            $start = microtime(true);

            Log::info("About to process tv user profile");
            $tv_user_profile = new TvUserProfile($import_time, $csv_file);
            $profiles_imported = $tv_user_profile->process();
            $time_elapsed_secs = microtime(true) - $start;
            Log::info("Processed {$profiles_imported} user profiles in {$time_elapsed_secs} seconds");

            Log::info('About to process tv profile activitiesx');
            $tv_user_activities = new TvUserActivities($import_time, $csv_file);
            $activities_imported = $tv_user_activities->process();
            $time_elapsed_secs = microtime(true) - $start;
            Log::info("Processed {$activities_imported} activities in {$time_elapsed_secs} seconds");
            
            MpsProfile::where('created_at', '!=', $import_time->format('Y-m-d H:i:s'))->delete();
            $time_elapsed_secs = microtime(true) - $start;
            Log::info("Deleted old MPS profiles in {$time_elapsed_secs} seconds");

            //trying the chunking method
            // MpsProfileActivity::where('created_at', '!=', $import_time->format('Y-m-d H:i:s'))->delete();
            // $deleted = 0;
            // $query = MpsProfileActivity::where('created_at', '!=', $import_time->format('Y-m-d H:i:s'));
            // do {
            //     $new_deleted = $query->take(50000)->delete();
            //     $deleted += $new_deleted;
            // } while ($new_deleted !== 0);
            // $time_elapsed_secs = microtime(true) - $start;
            Log::info("Deleted old MPS profile activities in {$time_elapsed_secs} seconds");

            return [$profiles_imported, $activities_imported];
        });

        $mps_file->cleanup();

        return $results;
    }
}