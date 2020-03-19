<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_master', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('store_name',250)->nullable();
            $table->tinyInteger('store_marketplace')->default('1')->comment('1-Magento');
            $table->tinyInteger('store_type')->default('1')->comment('1-Magento');
            $table->tinyInteger('is_active')->default('1')->comment('1-Active,0-Inactive');
            $table->tinyInteger('is_deleted')->default('0')->comment('1-Deleted,0-Not Deleted');
            $table->string('username',250)->nullable();
            $table->string('password',250)->nullable();
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('store_master');
    }
}
