<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModifyClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('status')->change();
            $table->string('created_by');
            $table->string('company_id');
            $table->string('street_address');
            $table->string('city');
            $table->string('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('company_id');
            $table->dropColumn('street_address');
            $table->dropColumn('city');
            $table->dropColumn('state');
        });
    }
}
