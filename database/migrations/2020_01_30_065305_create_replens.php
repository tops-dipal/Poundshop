<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replens', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('warehouse_id')->comment('from warehouse_master')->unsigned()->index();
            $table->bigInteger('product_id')->comment('from products')->unsigned()->index();
            $table->bigInteger('default_location')->comment('from locations_master')->unsigned()->index();
            $table->tinyInteger('priority')->default('12')->comment('2-Emergency,4-Priority1,6-Priority2,8-Priority3,10-Priority4,12-Priority5');
            $table->integer('replan_qty')->nullable();
            $table->tinyInteger('is_manual_update')->default('0')->comment('1-Yes,0-No');
            $table->tinyInteger('cron_replan_priority')->default('12')->comment('2-Emergency,4-Priority1,6-Priority2,8-Priority3,10-Priority4,12-Priority5');
            $table->integer('cron_replan_qty')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('0')->comment('1-Active,0-Inactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replens');
    }
}
