<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('slot_id')->unsigned()->index()->nullable();
            $table->integer('supplier_id',false)->index();
            $table->unsignedInteger('warehouse_id')->index();
            $table->string('booking_ref_id',25)->nullable();
            $table->text('comment')->nullable();
            $table->decimal('completed',5,2)->default(0)->comment('in %');
            $table->tinyInteger('status')->nullable()->comment('1=>Confirmed,2=>Not Arrived,3=>Arrived,4=>Receiving,5=>Completed');
            $table->tinyInteger('is_confirmed')->default(0)->comment('1=>confirmed,0=>not confirmed');
            $table->dateTime('arrived_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->string('delivery_notes_picture',255)->nullable();
            $table->string('delivery_note_number',25)->nullable();
            $table->smallInteger('num_of_pallets')->default(0);
            $table->smallInteger('total_qty_received')->default(0);
            $table->smallInteger('total_value_received')->default(0);
            $table->smallInteger('total_variants')->default(0);
            $table->smallInteger('total_new_products')->default(0);
            $table->smallInteger('total_damage_trade_qty')->default(0);
            $table->smallInteger('total_short_qty')->default(0);
            $table->smallInteger('total_over_qty')->default(0);
            $table->smallInteger('total_diff_po_note')->default(0);
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();
            
            $table->foreign('supplier_id')->references('id')->on('supplier_master')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouse_master')->onDelete('cascade'); 
            $table->foreign('slot_id')->references('id')->on('slots')->onDelete('cascade');
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
        Schema::dropIfExists('bookings');
    }
}
