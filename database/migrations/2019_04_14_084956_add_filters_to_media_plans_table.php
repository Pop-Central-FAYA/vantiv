<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFiltersToMediaPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plans', function (Blueprint $table) {
            $table->string('filters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_plans', function (Blueprint $table) {
            if (Schema::hasColumn('media_plans', 'filters')) {
                $table->dropColumn('filters');
            }
        });
    }
}
