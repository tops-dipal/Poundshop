<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_log', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('store_id');
            $table->tinyInteger('store_type')->default('1')->comment('1-Magento');
            $table->string('cron_name',250)->nullable();
            $table->string('cron_type',250)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cron_log');
    }
}
