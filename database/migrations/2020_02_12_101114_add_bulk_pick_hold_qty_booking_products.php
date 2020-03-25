<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBulkPickHoldQtyBookingProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->integer('return_to_supplier_pallet_qty')->after('lock_discrepancy')->default(0);
            $table->integer('quarantine_pallet_qty')->after('lock_discrepancy')->default(0);
            $table->integer('onhold_pallet_qty')->after('lock_discrepancy')->default(0);
            $table->integer('bulk_pallet_qty')->after('lock_discrepancy')->default(0);
            $table->integer('pick_pallet_qty')->after('lock_discrepancy')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->dropColumn('return_to_supplier_pallet_qty');
            $table->dropColumn('quarantine_pallet_qty');
            $table->dropColumn('onhold_pallet_qty');
            $table->dropColumn('bulk_pallet_qty');
            $table->dropColumn('pick_pallet_qty');
        });
    }
}
