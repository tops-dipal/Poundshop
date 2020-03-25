<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplensUpdateTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replens_update_trans', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('replen_id')->comment('from replens')->unsigned()->index();
            $table->bigInteger('user_id')->comment('from users')->unsigned()->index();
            $table->tinyInteger('priority')->default('12')->comment('2-Emergency,4-Priority1,6-Priority2,8-Priority3,10-Priority4,12-Priority5');
            $table->integer('qty')->nullable();
            $table->tinyInteger('prev_priority')->default('12')->comment('2-Emergency,4-Priority1,6-Priority2,8-Priority3,10-Priority4,12-Priority5');
            $table->integer('prev_qty')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replens_update_trans');
    }
}
