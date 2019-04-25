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
        		'North-West', 'North-East (Jos)', 'North Central (Abuja)', 'South-West (Ibadan, Benin)', 'South-East (Onitsha, Aba)', 'South-South (Rivers)', 'Lagos'
        	],
        	'states' => [
        		"Abia","Abuja","Adamawa","AkwaIbom","Anambra","Bauchi","Bayelsa","Benue","Borno","CrossRiver","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe", "Zamfara"
        	],
        	'genders' => [
        		'Male', 'Female'
        	],
        	'living_standard_measures' => [
        		'LSM 1 (E)', 'LSM 2 (E)', 'LSM 3 (D)', 'LSM 4 (D)', 'LSM 5 (D)', 'LSM 6 (C2)', 'LSM 7 (C2)', 'LSM 8 (C2)', 'LSM 9 (C1)', 'LSM 10 (C1)', 'LSM 11 (B)', 'LSM 12 (A)'
        	],
        	'social_classes' => [
        		'A', 'B', 'C', 'D', 'E'
        	],
        	'media_types' => [
        		'Tv', 'Radio', 'All'
        	]
        ];

		# Delete the criterias to recreate them
		Criteria::truncate();
		SubCriteria::truncate();

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
