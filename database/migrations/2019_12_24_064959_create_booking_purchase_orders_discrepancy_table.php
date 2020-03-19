<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingPurchaseOrdersDiscrepancyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_purchase_orders_discrepancy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_po_products_id')->comment('reference to order_products');
            $table->tinyInteger('discrepancy_type');
            $table->string('image',250)->nullable();            
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('1')->comment('1-debit,2-keepit,3-dispose,4-return');
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
        Schema::dropIfExists('booking_purchase_orders_discrepancy');
    }
}
