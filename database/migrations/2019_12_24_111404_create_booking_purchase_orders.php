<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingPurchaseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('booking_id')->index();
            $table->unsignedBigInteger('po_id')->index();
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');       
            $table->foreign('po_id')->references('id')->on('purchase_order_master')->onDelete('cascade');       
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
        Schema::dropIfExists('booking_purchase_orders');
    }
}
