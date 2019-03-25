<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Vanguard\Models\LivingStandardMeasure;

class LivingStandardMeasuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('living_standard_measures')->insert([
            ['name' => 'LSM 1'],
            ['name' => 'LSM 2'],
            ['name' => 'LSM 3'],
            ['name' => 'LSM 4'],
            ['name' => 'LSM 5'],
            ['name' => 'LSM 6'],
            ['name' => 'LSM 7'],
            ['name' => 'LSM 8'],
            ['name' => 'LSM 9'],
            ['name' => 'LSM 10'],
            ['name' => 'LSM 11'],
            ['name' => 'LSM 12']
        ]);
    }
}
