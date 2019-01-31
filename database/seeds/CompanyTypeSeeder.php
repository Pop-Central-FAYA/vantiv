<?php

use Illuminate\Database\Seeder;
use Vanguard\Models\CompanyType;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company_type1 = [
                'id' => uniqid(),
                'name' => 'Broadcaster'
            ];
        $company_type2 = [
                'id' => uniqid(),
                'name' => 'Agency'
            ];


        CompanyType::create($company_type1);
        CompanyType::create($company_type2);
    }
}
