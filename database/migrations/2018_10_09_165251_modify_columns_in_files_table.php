<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

# This migration does not make sense
class ModifyColumnsInFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = Schema::connection('api_db');

        if ($db_connection->hasColumn(
            'start_date', 'end_date', 'rejection_reason',
            'is_file_accepted', 'status')) {

            //this migration removes start_date, end_date, recommendation, rejection_reason from files table
            $db_connection->table('files', function (Blueprint $table) {
                $column_list = [
                    'start_date', 'end_date', 'rejection_reason',
                    'is_file_accepted', 'status'
                ];
                $table->dropColumn($column_list);
            });
        }

    }

}
