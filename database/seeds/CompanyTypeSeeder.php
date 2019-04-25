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
            'name' => 'broadcaster'
        ];
        $company_type2 = [
            'id' => uniqid(),
            'name' => 'agency'
        ];
        $company_types = CompanyType::all()->toArray();
        if(count($company_types) != 0){
            foreach ($company_types as $company_type)
            {
                //check if the company type exist, if not create it
                if($company_type['name'] == $company_type1['name']){
                    return;
                }else {
                     CompanyType::create($company_type1);
                }

                if($company_type['name'] == $company_type2['name']){
                    return;
                }else{
                    CompanyType::create($company_type2);
                }
            }
        }
    }
}
