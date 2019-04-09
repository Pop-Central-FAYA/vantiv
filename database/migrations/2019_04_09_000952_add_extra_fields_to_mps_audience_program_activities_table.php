<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToMpsAudienceProgramActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mps_audience_program_activities', function (Blueprint $table) {
            $table->string('external_user_id')->index();
            $table->string('state')->index();

            $table->index('mps_audience_id');
            $table->index('station');
            $table->index('day');
            $table->index('start_time');
            $table->index('end_time');
        });
    }

    protected function dropIndexesIfExist($table, $indexes) {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexesFound = $sm->listTableIndexes('mps_audience_program_activities');
        foreach($indexes as $index) {
            $index_name = "mps_audience_program_activities_" . $index . "_index";
            if (array_key_exists($index_name, $indexesFound)) {
                $table->dropIndex($index_name);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mps_audience_program_activities', function (Blueprint $table) {
            if (Schema::hasColumn('mps_audience_program_activities', 'external_user_id')) {
                $table->dropColumn('external_user_id');
            }
            if (Schema::hasColumn('mps_audience_program_activities', 'state')) {
                $table->dropColumn('state');
            }

            $indexes = ['mps_audience_id', 'station', 'day', 'start_time', 'end_time'];
            $this->dropIndexesIfExist($table, $indexes);
        });
    }
}
