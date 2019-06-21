<?php

use Illuminate\Database\Seeder;

class AgencyFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //delete if data exist
         //permissions so far on the ssp side
         $permissions_array = [
             'create.media_plan', 'update.media_plan', 'submit.media_plan', 'view.media_plan', 
             'view.invoice', 'view.wallet', 'create.wallet', 'create.asset', 'view.asset', 'update.asset',
             'delete.asset'
         ];
 
         //store permissions
         foreach ($permissions_array as $permission){
             \Spatie\Permission\Models\Permission::create([
                 'name' => $permission,
             ]);
         }
    }
}
