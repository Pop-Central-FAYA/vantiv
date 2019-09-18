<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransformOldBrandToNewBrand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "UPDATE brands SET client_id=(SELECT client_id FROM brand_client WHERE brands.id = brand_client.brand_id AND brand_client.media_buyer = 'Agency'), created_by=(SELECT created_by FROM brand_client WHERE brands.id = brand_client.brand_id AND brand_client.media_buyer = 'Agency')" 
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
