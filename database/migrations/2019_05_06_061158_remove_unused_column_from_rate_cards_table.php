<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedColumnFromRateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $column_to_drop = [];
        $array_of_column = ['duration', 'price', 'start_time', 'end_time', 'ratecard_type_id', 'start_date', 'end_date', 'ratecard_type'];
        foreach ($array_of_column as $column){
            if(Schema::hasColumn('rate_cards', $column)){
                array_push($column_to_drop, $column);
            }
        }
        Schema::table('rate_cards', function (Blueprint $table) use ($column_to_drop) {
            $table->dropColumn($column_to_drop);
            $table->boolean('is_base')->default(false);

            $table->unique(['slug', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_cards', function (Blueprint $table) {
            $table->integer('duration')->nullable();
            $table->integer('price')->nullable();
            $table->string('ratecard_type');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('ratecard_type_id', 25)->index()->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->dropColumn('is_base');
            $table->dropUnique(['slug', 'company_id']);
        });
    }
}
