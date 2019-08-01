<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCompanyIdToMediaPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plans', function (Blueprint $table) {
            $table->string('company_id')->index();
        });

        DB::statement("UPDATE media_plans 
                        SET company_id = (
                            SELECT company_user.company_id 
                            FROM company_user 
                            WHERE media_plans.planner_id = company_user.user_id)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_plans', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
