<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBroadcasterPlayoutFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcaster_playout_files', function (Blueprint $table) {
            $table->string('id', 25);
            $table->char('file_hash', 64);
            $table->string('status', 20);
            $table->string('file_name');
            $table->text('tmp_path')->nullable();
            $table->text('media_path')->nullable();
            $table->integer('duration');
            $table->text('url');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();

            $table->timestamps();

            $table->primary('id');
            $table->unique('file_hash');
            $table->index('started_at');
            $table->index('downloaded_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('broadcaster_playout_files');
    }
}
