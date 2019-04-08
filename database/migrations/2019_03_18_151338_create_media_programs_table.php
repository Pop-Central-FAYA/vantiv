<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_programs', function (Blueprint $table) {
            $table->string('id', 25);
            $table->string('name');
            $table->string('company_id', 25)->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->string('rate_card_id')->index();
            $table->string('program_vendor_id',25)->nullable()->index();
            $table->string('slug');
            $table->string('status', 20);
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
        Schema::dropIfExists('media_programs');
    }
}
