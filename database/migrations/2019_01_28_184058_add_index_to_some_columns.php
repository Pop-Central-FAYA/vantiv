<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToSomeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('id', 25)->change();

            $table->primary('id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('rejection_reasons', function (Blueprint $table) {
            $table->string('rejection_reason_category_id', 25)->change();

            $table->index(['created_at']);
            $table->index(['updated_at']);
        });

        Schema::table('preselected_adslots', function (Blueprint $table) {
            $table->string('user_id', 25)->change();
            $table->string('broadcaster_id', 25)->change();
            $table->string('adslot_id', 25)->change();
            $table->string('agency_id', 25)->nullable()->change();
            $table->string('filePosition_id', 25)->nullable()->change();

            $table->index('user_id');
            $table->index('broadcaster_id');
            $table->index('adslot_id');
            $table->index('agency_id');
            $table->index('filePosition_id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('uploads', function (Blueprint $table) {
            $table->string('user_id', 25)->change();

            $table->index('user_id');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('id')->change();

            $table->dropPrimary(['id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('rejection_reasons', function (Blueprint $table) {
            $table->string('rejection_reason_category_id')->change();

            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('preselected_adslots', function (Blueprint $table) {
            $table->string('user_id')->change();
            $table->string('broadcaster_id')->change();
            $table->string('adslot_id')->change();
            $table->string('agency_id')->nullable()->change();
            $table->string('filePosition_id')->nullable()->change();

            $table->dropIndex(['user_id']);
            $table->dropIndex(['broadcaster_id']);
            $table->dropIndex(['adslot_id']);
            $table->dropIndex(['agency_id']);
            $table->dropIndex(['filePosition_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('uploads', function (Blueprint $table) {
            $table->string('user_id')->change();

            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
    }
}
