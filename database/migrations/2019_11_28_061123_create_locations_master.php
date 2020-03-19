<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations_master', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('warehouse_id')->nullable();
            $table->integer('site_id')->nullable();
            $table->string('aisle',5)->nullable();
            $table->string('rack',5)->nullable();
            $table->string('floor',5)->nullable();
            $table->string('box',5)->nullable();
            $table->integer('sort_order')->nullable();
            $table->tinyInteger('type_of_location')->default('1');
            $table->tinyInteger('case_pack')->default('0')->comment('0 - no (default), 1 - Yes');
            $table->smallInteger('length')->nullable()->comment('in cm');
            $table->smallInteger('width')->nullable()->comment('in cm');
            $table->smallInteger('height')->nullable()->comment('in cm');
            $table->decimal('cbm', 10, 2)->nullable();
            $table->decimal('storable_weight', 10, 2)->nullable();
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations_master');
    }
}
