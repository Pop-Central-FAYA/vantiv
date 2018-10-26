<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToAdslotReasonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('api_db')->table('adslot_reason', function (Blueprint $table) {
            $table->string('user_id');
            $table->text('recommendation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('api_db')->table('adslot_reason', function (Blueprint $table) {
            $table->dropIfExists(['user_id', 'recommendation']);
        });
    }
}
