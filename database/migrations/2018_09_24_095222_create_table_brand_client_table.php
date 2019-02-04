<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBrandClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_client', function (Blueprint $table) {
            $table->increments('id');
            $table->string('brand_id');
            $table->string('media_buyer');
            $table->string('media_buyer_id');
            $table->string('client_id');
            $table->timestamps();
        });

        DB::statement(
            "INSERT INTO brand_client (`brand_id`, `client_id`, `media_buyer_id`, `media_buyer`, `created_at`, `updated_at`)
            SELECT id, walkin_id AS client_id, broadcaster_agency AS media_buyer_id, 'Agency', time_created, time_modified FROM brand_old"
        );

        DB::statement(
            "UPDATE brand_client SET media_buyer = 'Broadcaster'
            WHERE media_buyer_id IN
            (
                SELECT id FROM broadcasters
            )"
        );

        DB::statement(
            "UPDATE brand_client SET media_buyer = 'Agency'
            WHERE media_buyer_id IN
            (
                SELECT id FROM agents
            )"
        );
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_client');
    }
}

;
