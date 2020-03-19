<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tax_identifier')->unique()->index();
            $table->tinyInteger('tax_type')->default(1)->comment('1->import_duty,2->vat');
            $table->decimal('rate',5,2)->comment('rate in %')->nullable();
            $table->bigInteger('commodity_code_id')->index()->unsigned()->comment('reference to commodity table');
            $table->bigInteger('country_id')->index()->unsigned()->comment('reference to countries table');
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->unsigned()->index()->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('commodity_code_id')->references('id')->on('commodity_codes')->onDelete('cascade');
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
        Schema::dropIfExists('tax_rates');
    }
}
