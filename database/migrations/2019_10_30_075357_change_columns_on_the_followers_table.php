<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsOnTheFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('followers')){
            Schema::table('followers', function (Blueprint $table) {
                $table->string('follower_id', 25)->index()->change();
                $table->string('followable_id', 25)->index()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('followers')){
            Schema::table('followers', function (Blueprint $table) {
                $table->bigInteger('follower_id')->change();
                $table->bigInteger('followable_id')->change();
            });
        }
    }
}
