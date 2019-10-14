<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMpsProfileActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        //because enums are custom types and cause an error when performing actions on tables with it
        Schema::table('mps_profile_activities', function (Blueprint $table) {
            if (Schema::hasColumn('mps_profile_activities', 'media_type')) {
                $table->dropColumn('media_type');
            }
        });

        Schema::table('mps_profile_activities', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('updated_at');
            $table->dropColumn('tv_station_id');

            $table->date('wave')->change()->index();
            $table->string('broadcast_type', 15)->index();
        });
    }
    
    /** 
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mps_profile_activities', function (Blueprint $table) {
            $table->dropIndex(['wave']);

            $table->string('id', 25)->nullable()->unique();
            $table->timestamp('updated_at');
            $table->enum('media_type', ['Radio', 'Tv']);
            $table->string('tv_station_id', 25)->index();

            $table->string('wave')->change();

            $table->dropColumn('broadcast_type');
        });
    }
}
