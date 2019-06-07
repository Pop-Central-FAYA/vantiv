<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('file_name');
            $table->string('client_id')->reference('id')->on('walkIns');
            $table->string('brand_id')->reference('id')->on('brands');
            $table->enum('media_type', ['Tv', 'Radio', 'All']);
            $table->string('asset_url');
            $table->string('regulatory_cert_url');
            $table->integer('duration');
            $table->string('company_id')->reference('id')->on('companies');
            $table->string('created_by')->reference('id')->on('users');
            $table->string('updated_by')->reference('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_assets');
    }
}
