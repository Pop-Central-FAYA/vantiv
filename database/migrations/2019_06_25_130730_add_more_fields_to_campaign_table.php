<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('product')->nullable();
            $table->date('start_date')->nullable();
            $table->date('stop_date')->nullable();
            $table->string('channel')->reference('id')->on('campaignChannel')->nullable();
            $table->string('walkin_id')->reference('id')->on('walkIns')->nullable();
            $table->string('brand_id')->reference('id')->on('brands')->nullable();
            $table->string('target_audience')->reference('id')->on('targetAudiences')->nullable();
            $table->text('age_groups')->nullable();
            $table->text('regions')->nullable();
            $table->double('budget')->default(0);
            $table->integer('ad_slots')->default(0);
            $table->string('created_by')->reference('id')->on('users')->nullable();
            $table->string('belongs_to')->reference('id')->on('companies')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'belongs_to', 'name','product','start_date','stop_date',
                                'channel', 'walkin_id', 'brand_id', 'target_audience', 'age_groups', 
                                'regions', 'budget', 'ad_slots']);
        });
    }
}
