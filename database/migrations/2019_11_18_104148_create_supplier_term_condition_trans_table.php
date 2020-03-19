<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierTermConditionTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_term_condition_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id')->comment('from supplier master table')->unsigned()->index()->nullable();
            $table->tinyInteger('type')->default('1')->comment('1-from poundshop,2-from supplier');
            $table->longText('terms')->nullable();
            $table->timestamps();            
            $table->foreign('supplier_id')->references('id')->on('supplier_master')->onDelete('cascade');            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_term_condition_trans');
    }
}
