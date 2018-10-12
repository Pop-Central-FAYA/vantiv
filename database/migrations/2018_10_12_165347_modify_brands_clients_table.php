<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyBrandsClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `brand_client` CHANGE COLUMN client_id TO media_buyer_id VARCHAR(100) 
                                                        CHANGE COLUMN brands_client TO client_id VARCHAR(100) 
                                                        ADD media_buyer VARCHAR(100)
                                                        ADD created_at VARCHAR(45) 
                                                        ADD updated_at VARCHAR(45) ");
    }

}
