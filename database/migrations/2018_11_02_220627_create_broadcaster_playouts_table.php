<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBroadcasterPlayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcaster_playouts', function (Blueprint $table) {
            $table->string('id', 25);
            $table->string('mpo_detail_id', 25);
            $table->string('broadcaster_id', 25);
            $table->string('broadcaster_playout_file_id', 25);
            $table->string('selected_adslot_id', 25);
            $table->date('air_date');
            $table->char('air_between', 15);
            $table->string('status', 10);
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('played_at')->nullable();
            $table->string('placed_in')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->index('mpo_detail_id');
            $table->index('broadcaster_id');
            $table->index('broadcaster_playout_file_id');
            $table->index('selected_adslot_id');
            $table->index('air_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('broadcaster_playouts');
    }
}