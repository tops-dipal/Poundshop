<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_pallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('booking_id')->index();
            $table->tinyInteger('pallet_type')->nullable()->comment('1=>receive pallet,2=>return pallet');
            $table->integer('pallet_id')->index();
            $table->integer('num_of_pallets')->default(0);
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('pallet_id')->references('id')->on('pallets_master')->onDelete('cascade');   
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
        Schema::dropIfExists('booking_pallets');
    }
}
