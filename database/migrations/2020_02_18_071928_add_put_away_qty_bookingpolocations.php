<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPutAwayQtyBookingpolocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_product_locations', function (Blueprint $table) {
           $table->renameColumn('pending_put_away_qty','put_away_qty');
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
            $table->renameColumn('put_away_qty','pending_put_away_qty');
        });
    }
}
