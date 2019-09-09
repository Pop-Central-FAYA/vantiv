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
            "INSERT INTO clients (id, nationality, name, image_url, created_by, company_id, street_address, created_at, updated_at)
            SELECT id, nationality, company_name, image_url, user_id, company_id, location, time_modified, time_created FROM walkins where agency_id <>''" 
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    
    }
}
