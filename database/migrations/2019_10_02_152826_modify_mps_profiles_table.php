<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMpsProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mps_profiles', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('updated_at');

            $table->date('wave')->change();

            $table->unique(['wave', 'ext_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mps_profiles', function (Blueprint $table) {
            $table->dropUnique(['wave', 'ext_profile_id']);

            $table->string('id', 25)->nullable()->unique();
            $table->timestamp('updated_at');

            $table->string('wave')->change();

        });
    }
}
