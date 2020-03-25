<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LocationIdBookingPoproductLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_product_locations', function (Blueprint $table) {
             $table->bigInteger('location_id')->nullable()->after('location')->index()->unsigned();
            $table->foreign('location_id')->references('id')->on('locations_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_po_product_locations', function (Blueprint $table) {
            $table->dropForeign('booking_po_product_locations_location_id_foreign');
            $table->dropColumn('location_id');
        });
    }
}
