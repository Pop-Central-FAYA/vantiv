<?php

use Illuminate\Database\Seeder;
use Vanguard\Models\MpsAudience;
use Vanguard\Models\MpsAudienceProgramActivity;

class MpsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mps_audience = [
        	['external_user_id' => '100', 'age' => '20', 'gender' => 'Male', 'region' => 'SW', 'lsm' => 'LSM 1', 'state' => 'Lagos', 'social_class' => 'A'],
            ['external_user_id' => '101', 'age' => '10', 'gender' => 'Female', 'region' => 'NW', 'lsm' => 'LSM 2', 'state' => 'Kano', 'social_class' => 'A'],
            ['external_user_id' => '102', 'age' => '35', 'gender' => 'Male', 'region' => 'SE', 'lsm' => 'LSM 3', 'state' => 'Lagos', 'social_class' => 'B'],
            ['external_user_id' => '103', 'age' => '40', 'gender' => 'Female', 'region' => 'SE', 'lsm' => 'LSM 4', 'state' => 'Lagos', 'social_class' => 'C'],
            ['external_user_id' => '104', 'age' => '25', 'gender' => 'Male', 'region' => 'SW', 'lsm' => 'LSM 1', 'state' => 'Lagos', 'social_class' => 'D']
        ];

        $mps_programs = [
            ['media_type' => 'Tv', 'station' => 'NTA 10', 'program' => 'Super story', 'day' => 'Tuesday', 'start_time' => '20:00', 'end_time' => '21:30'],
            ['media_type' => 'Tv', 'station' => 'NTA 10', 'program' => 'Super story', 'day' => 'Thursday', 'start_time' => '20:00', 'end_time' => '21:30'],
            ['media_type' => 'Tv', 'station' => 'Silverbird', 'program' => 'Superman', 'day' => 'Wednesday', 'start_time' => '08:00', 'end_time' => '8:30'],
            ['media_type' => 'Radio', 'station' => 'Brilla FM', 'program' => 'Beyound', 'day' => 'Monday', 'start_time' => '20:00', 'end_time' => '21:30'],
            ['media_type' => 'Radio', 'station' => 'Cool FM', 'program' => 'Above', 'day' => 'Tuesday', 'start_time' => '20:00', 'end_time' => '21:30']
        ];

        foreach ($mps_audience as $audience) {
        	$audience = MpsAudience::create([
                'external_user_id' => $audience['external_user_id'],
                'age' => $audience['age'],
                'gender' => $audience['gender'],
                'region' => $audience['region'],
                'lsm' => $audience['lsm'],
                'state' => $audience['state'],
                'social_class' => $audience['social_class']
            ]);

        	foreach ($mps_programs as $programActivity) {
        		MpsAudienceProgramActivity::create([
        			'mps_audience_id' => $audience->id,
        			'media_type' => $programActivity['media_type'],
                    'station' => $programActivity['station'],
                    'program' => $programActivity['program'],
                    'day' => $programActivity['day'],
                    'start_time' => $programActivity['start_time'],
                    'end_time' => $programActivity['end_time']
        		]);
        	}
        }
    }
}
