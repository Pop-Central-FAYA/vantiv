<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class ChangeFilesTableToSelectedAdslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('files')){
            Schema::rename('files', 'selected_adslots');
        }
    }

    public function down()
    {
        if(Schema::hasTable('selected_adslots')){
            Schema::rename('selected_adslots', 'files');
        }
    }

}
