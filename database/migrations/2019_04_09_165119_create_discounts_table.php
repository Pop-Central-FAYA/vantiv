<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('discounts')){
            Schema::rename('discounts', 'discounts_old');
        }
        Schema::create('discounts', function (Blueprint $table) {
            $table->string('id');
            $table->string('name');
            $table->integer('percentage');
            $table->string('slug');
            $table->string('status');
            $table->string('company_id', 25)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
        if(Schema::hasTable('discounts_old')){
            Schema::rename('discounts_old', 'discounts');
        }
    }
}
