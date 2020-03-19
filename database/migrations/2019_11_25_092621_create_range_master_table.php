<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRangeMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('range_master', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('category_name')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->tinyInteger('seasonal_status')->default('1')->comment('1-seasonal,2-non_seasonal');
            $table->date('seasonal_from')->nullable();
            $table->date('seasonal_to')->nullable();
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
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
        Schema::dropIfExists('range_master');
    }
}
