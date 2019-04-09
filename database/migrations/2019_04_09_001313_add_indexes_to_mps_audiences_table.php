<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToMpsAudiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mps_audiences', function (Blueprint $table) {
            $table->index('external_user_id');
            $table->index('age');
            $table->index('gender');
            $table->index('region');
            $table->index('lsm');
            $table->index('state');
            $table->index('social_class');
        });
    }

    protected function dropIndexesIfExist($table, $indexes) {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexesFound = $sm->listTableIndexes('mps_audiences');
        foreach($indexes as $index) {
            $index_name = "mps_audiences_" . $index . "_index";
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
        Schema::table('mps_audiences', function (Blueprint $table) {
            $indexes = ['external_user_id', 'age', 'gender', 'region', 'lsm', 'state', 'social_class'];
            $this->dropIndexesIfExist($table, $indexes);
        });
    }
}
