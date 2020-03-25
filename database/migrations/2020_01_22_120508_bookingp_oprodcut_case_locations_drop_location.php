<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookingpOprodcutCaseLocationsDropLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_product_locations', function (Blueprint $table) {
            $table->dropColumn('location');
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
            $table->string('location')->nullable()->after('best_before_date');
        });
    }
}
