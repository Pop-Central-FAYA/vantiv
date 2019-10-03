<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class DeleteAdmisAdvertisersAgentUsersBrandProductBroadcasterUserClientUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('advertisers');
        Schema::dropIfExists('agentUsers');
        Schema::dropIfExists('brand_products');
        Schema::dropIfExists('clientUsers');
        Schema::dropIfExists('broadcasterUsers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
