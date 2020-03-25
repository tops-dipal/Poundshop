<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlotMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slot_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time("from");
            $table->time("to");
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');            
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('slot_master');
    }
}
