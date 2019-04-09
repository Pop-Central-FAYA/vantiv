<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompanyTypeSeeder::class);
        $this->call(RateCardPriority::class);
        $this->call(CriteriasTableSeeder::class);
        $this->call(MpsTableSeeder::class);
        $this->call(StatePopulationsTableSeeder::class);
    }
}
