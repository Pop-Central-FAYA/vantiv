<?php

use Illuminate\Database\Seeder;

class PublisherFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //delete if data exist
        DB::table('permissions')->delete();
        //permissions so far on the ssp side
        $permissions_array = [
            'update.super_admin', 'view.report', 'view.client', 'update.client', 'create.client',
            'view.brand', 'update.brand', 'create.brand',
            'view.campaign', 'submit.campaign', 'create.campaign', 'update.campaign',
            'view.mpo', 'update.mpo_status',
            'view.inventory', 'update.inventory', 'create.inventory',
            'view.rate_card', 'create.rate_card', 'update.rate_card',
            'view.discount', 'create.discount', 'update.discount',
            'create.user', 'view.user', 'update.user',
            'view.profile', 'update.profile'
        ];

        //store permissions
        foreach ($permissions_array as $permission){
            \Spatie\Permission\Models\Permission::create([
                'name' => $permission,
                'guard_name' => 'ssp'
            ]);
        }
    }
}
