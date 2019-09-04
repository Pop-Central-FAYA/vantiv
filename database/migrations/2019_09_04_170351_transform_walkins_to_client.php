<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransformWalkinsToClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "INSERT INTO clients ('id', 'nationality', 'name', 'created_by', 'company_id', 'street_address', 'city', 'state')
            SELECT 'id', 'nationality', 'name', 'created_by', 'company_id', 'street_address', 'city', 'state'" 
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client', function (Blueprint $table) {
            //
        });
    }
}
