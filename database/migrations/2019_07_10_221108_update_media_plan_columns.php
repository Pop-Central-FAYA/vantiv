<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMediaPlanColumns extends Migration
{
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plans', function (Blueprint $table) {
            $table->text('criteria_lsm')->nullable()->change();
            $table->text('criteria_social_class')->nullable()->change();
            $table->text('criteria_region')->nullable()->change();
            $table->text('criteria_state')->nullable()->change();
            $table->text('criteria_age_groups')->nullable()->change();
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
            $table->string('criteria_lsm')->nullable()->change();
            $table->string('criteria_social_class')->nullable()->change();
            $table->string('criteria_region')->nullable()->change();
            $table->string('criteria_state')->nullable()->change();
            $table->string('criteria_age_groups')->nullable()->change();
        });
    }
}
