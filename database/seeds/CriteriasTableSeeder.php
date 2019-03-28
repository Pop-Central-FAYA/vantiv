<?php

use Illuminate\Database\Seeder;
use Vanguard\Models\Criteria;
use Vanguard\Models\SubCriteria;

class CriteriasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $criterias = [
        	'regions' => [
        		'NW', 'NE', 'NC', 'SW', 'SE', 'SS', 'Lagos'
        	],
        	'states' => [
        		'Lagos', 'Kano', 'Kaduna', 'Kano', 'Abia', 'Anambra', 'Delta', 'Adamawa'
        	],
        	'genders' => [
        		'Male', 'Female', 'Both'
        	],
        	'living_standard_measures' => [
        		'LSM 1', 'LSM 2', 'LSM 3', 'LSM 4', 'LSM 5', 'LSM 6', 'LSM 7', 'LSM 8', 'LSM 9', 'LSM 10', 'LSM 11', 'LSM 12'
        	],
        	'social_classes' => [
        		'A', 'B', 'C', 'D', 'E', 'F'
        	],
        	'media_types' => [
        		'Tv', 'Radio', 'Both'
        	]
        ];

        foreach ($criterias as $key => $criteria) {
        	$newCriteria = Criteria::create(['name' => $key]);
        	foreach ($criteria as $value) {
        		SubCriteria::create([
        			'criteria_id' => $newCriteria->id,
        			'name' => $value
        		]);
        	}
        }
    }
}
