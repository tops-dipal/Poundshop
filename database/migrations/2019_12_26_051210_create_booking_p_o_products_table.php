<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingPOProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_po_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('booking_id')->unsigned()->index()->nullable();
            $table->bigInteger('product_id')->unsigned()->index()->nullable();
            $table->bigInteger('product_parent_id')->unsigned()->index()->nullable();
            $table->integer('delivery_note_qty')->default(0)->nullable();
            $table->integer('qty_received')->default(0)->nullable();
            $table->integer('difference')->default(0)->nullable();
            $table->smallInteger('is_best_before_date')->commnet('0 - No, 1 - Yes')->default(0);
            $table->smallInteger('is_inner_outer_case')->commnet('0 - No, 1 - Yes')->default(0);
            $table->smallInteger('is_variant')->commnet('0 - No, 1 - Yes')->default(0);
            $table->date('best_before_date')->nullable();
            $table->string('location')->nullable();
            $table->text('comments')->nullable();
            $table->bigInteger('scan_by_user_id')->unsigned()->index()->nullable();
            $table->dateTime('scan_date')->nullable();
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();
            
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');       
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');       
            $table->foreign('scan_by_user_id')->references('id')->on('users')->onDelete('cascade');            
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
        Schema::dropIfExists('booking_po_products');
    }
}
