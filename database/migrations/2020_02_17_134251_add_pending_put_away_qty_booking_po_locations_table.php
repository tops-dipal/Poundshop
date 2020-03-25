<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingPutAwayQtyBookingPoLocationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('booking_po_product_locations', function (Blueprint $table) {
            $table->integer('pending_put_away_qty')->default(0)->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('booking_po_product_locations', function (Blueprint $table) {
            $table->dropColumn('pending_put_away_qty');
        });
    }

}
