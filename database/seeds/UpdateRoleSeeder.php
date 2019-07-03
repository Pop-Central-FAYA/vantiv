<?php

use Illuminate\Database\Seeder;

class UpdateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
