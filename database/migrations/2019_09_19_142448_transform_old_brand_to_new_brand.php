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
            "UPDATE brands AS b JOIN brand_client AS bc ON b.id = bc.brand_id SET b.client_id = bc.client_id, b.created_by = bc.created_by WHERE bc.media_buyer = 'Agency'" 
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
