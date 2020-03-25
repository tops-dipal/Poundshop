<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookingPOProductLocationsAddBookingPOProductId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_product_locations', function (Blueprint $table) {
            $table->bigInteger('booking_po_product_id')->unsigned()->nullable()->after('id')->index();
            $table->foreign('booking_po_product_id')->references('id')->on('booking_po_products')->onDelete('cascade');            
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
            $table->dropForeign('booking_po_product_locations_booking_po_product_id_foreign');
            $table->dropColumn('booking_po_product_id');
        });
    }
}
