<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = Schema::connection('api_db');

        if ($db_connection->hasTable('brands')) {
            $db_connection->rename('brands', "brand_old");
        }

        $db_connection->create('brands', function (Blueprint $table) {
            $table->string('id', 25);
            $table->string('name');
            $table->text('image_url');
            $table->string('industry_code');
            $table->string('sub_industry_code');
            $table->string('slug');
            $table->timestamps();

            $table->primary('id');
            $table->index('created_at');
            $table->index('updated_at');
        });

        if ($db_connection->hasTable('brand_old')) {
            DB::connection('api_db')->statement("
                INSERT INTO brands (id, `name`, image_url, industry_code, sub_industry_code, slug, created_at, updated_at)
                SELECT id, `name`, image_url, industry_id, sub_industry_id, md5(`name`), time_created, time_modified FROM brand_old");
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $db_connection = Schema::connection('api_db');

        $db_connection->dropIfExists('brands');

        if ($db_connection->hasTable('brand_old')) {
            $db_connection->rename('brand_old', "brands");
        }
    }
}
