<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySubmittedAndAcceptedAsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_mpos', function (Blueprint $table) {
            $table->datetime('submitted_at')->nullable()->change();
            $table->datetime('accepted_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_mpos', function (Blueprint $table) {
            $table->timestamp('submitted_at');
            $table->timestamp('accepted_at');
        });
    }
}
