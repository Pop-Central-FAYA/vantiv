<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemoveComplianceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = Schema::connection('api_db');
        if($db_connection->hasTable('compliances')){
            $db_connection->drop('compliances');
        }
    }

}
