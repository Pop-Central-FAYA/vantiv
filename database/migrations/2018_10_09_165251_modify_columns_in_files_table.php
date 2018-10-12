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
        if(Schema::hasColumn('start_date', 'end_date', 'recommendation', 'rejection_reason', 'is_file_accepted', 'status')){
            DB::statement('ALTER TABLE `files` 
                                DROP COLUMN start_date,
                                DROP COLUMN end_date,
                                DROP COLUMN recommendation,
                                DROP COLUMN rejection_reason,
                                DROP COLUMN is_file_accepted,
                                DROP COLUMN status');

        }

    }

}
