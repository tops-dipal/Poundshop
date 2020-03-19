<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoProductTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_product_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('po_id')->comment('from purchase order master table')->unsigned()->index()->nullable();
            $table->bigInteger('product_id')->comment('from product master table')->unsigned()->index()->nullable();
            $table->integer('quan_per_case')->nullable();
            $table->integer('total_cases')->nullable();
            $table->integer('total_quantity')->nullable();
            $table->decimal('price_per_quant', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('exp_mros', 10, 2);
            $table->foreign('po_id')->references('id')->on('purchase_order_master')->onDelete('cascade');
            //$table->foreign('product_id')->references('id')->on('supplier_contact')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_product_trans');
    }
}
