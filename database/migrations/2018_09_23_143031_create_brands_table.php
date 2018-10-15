<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("RENAME TABLE `brands` TO `brand_old`");

        Schema::create('brands', function (Blueprint $table) {
            $table->string('id');
            $table->string('name');
            $table->text('image_url');
            $table->string('industry_code');
            $table->string('sub_industry_code');
            $table->string('slug');
            $table->timestamps();
        });

        DB::statement("INSERT INTO brands (id, `name`, image_url, industry_code, sub_industry_code, slug, created_at, updated_at) SELECT id, `name`, image_url, industry_id, sub_industry_id, md5(`name`), time_created, time_modified FROM brand_old");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brands');
    }
}