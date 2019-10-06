<?php

use Illuminate\Database\Seeder;

use Symfony\Component\Yaml\Yaml;
use Vanguard\Models\MpsProfile;
use Vanguard\Models\MpsProfileActivity;

/**
 * This seeds test data for mps 
 */
class MpsTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::transaction(function() {
            MpsProfile::truncate();
            $profiles = $this->readSeedData("profiles.yml");
            MpsProfile::insert($profiles);

            MpsProfileActivity::truncate();
            $activities = $this->readSeedData("activities.yml");
            MpsProfileActivity::insert($activities);

        });
    }

    protected function readSeedData($file_name) 
    {
        $filepath = dirname(__FILE__) . "/" . $file_name;
        return Yaml::parseFile($filepath);
    }
}
// php artisan db:seed --class=MpsTestDataSeeder
