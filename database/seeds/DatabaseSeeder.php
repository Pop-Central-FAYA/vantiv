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
<<<<<<< HEAD
        $this->call(CompanyTypeSeeder::class);
        $this->call(RateCardPriority::class);
=======
        $this->call([
        	CompanyTypeSeeder::class,
            CriteriasTableSeeder::class,
            MpsTableSeeder::class
        ]);
>>>>>>> 926088447955b181da7fe62ccc3fd9bb2ee6c192
    }
}
