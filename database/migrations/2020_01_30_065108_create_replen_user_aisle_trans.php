<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplenUserAisleTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replen_user_aisle_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('warehouse_id')->comment('from warehouse_master')->unsigned()->index();
            $table->bigInteger('user_id')->comment('from users')->unsigned()->index();
            $table->string('aisle');
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('1')->comment('1-Active,0-Inactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replen_user_aisle_trans');
    }
}
