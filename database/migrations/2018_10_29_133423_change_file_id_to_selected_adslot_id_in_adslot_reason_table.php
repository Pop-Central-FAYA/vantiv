<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFileIdToSelectedAdslotIdInAdslotReasonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adslot_reason', function($table)
        {
            $table->renameColumn('file_id', 'selected_adslot_id');
        });
    }

    public function down()
    {
        Schema::table('adslot_reason', function($table)
        {
            $table->renameColumn('selected_adslot_id', 'file_id');
        });
    }

}
