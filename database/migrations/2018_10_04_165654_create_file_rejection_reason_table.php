<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileRejectionReasonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //renaming the file_rejection_reason table to adslot_rejection
        if(Schema::hasTable('file_rejection_reason')){
            Schema::rename('file_rejection_reason', 'adslot_reason');
        }else{
            Schema::create('adslot_reason', function (Blueprint $table) {
                $table->increments('id');
                $table->string('file_id');
                $table->integer('rejection_reason_id');
                $table->timestamps();
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
        Schema::dropIfExists('adslot_reason');
    }
}
