<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportDutyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_duty', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('commodity_code_id')->unsigned()->comment('Foreign key of commodity codes')->index();
            $table->double('rate',8,2);
            $table->bigInteger('country_id')->comment('from countries table')->unsigned()->index();
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
        Schema::dropIfExists('import_duty');
    }
}
