<?php

use Illuminate\Database\Seeder;

class UpdateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * To update the role table
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')
        ->where('guard_name', 'dsp')
        ->update(['guard_name' => 'web']);
    }
}
