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
            $table->string('id', 25)->primary()->change();

            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('rejection_reasons', function (Blueprint $table) {
            $table->string('rejection_reason_category_id', 25)->change();

            $table->index('rejection_reason_category_id', 'rejection_reasons_one');
            $table->index('created_at', 'rejection_reasons_two');
            $table->index('updated_at', 'rejection_reasons_three');
        });

        Schema::table('preselected_adslots', function (Blueprint $table) {
            $table->string('user_id', 25)->index()->change();
            $table->string('broadcaster_id', 25)->index()->change();
            $table->string('adslot_id', 25)->index()->change();
            $table->string('agency_id', 25)->index()->nullable()->change();
            $table->string('filePosition_id', 25)->index()->nullable()->change();

            $table->index('created_at');
            $table->index('updated_at');

        });

        Schema::table('uploads', function (Blueprint $table) {
            $table->string('user_id', 25)->index()->change();

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
