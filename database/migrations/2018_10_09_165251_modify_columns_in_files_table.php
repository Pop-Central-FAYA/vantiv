<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnsInFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //this migration removes start_date, end_date, recommendation, rejection_reason from files table
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'recommendation', 'rejection_reason', 'is_file_accepted', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file', function (Blueprint $table) {
            //
        });
    }
}
