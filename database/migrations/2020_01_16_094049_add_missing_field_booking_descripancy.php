<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingFieldBookingDescripancy extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->string('supplier_sku', 30)->after('barcode')->nullable();
            $table->integer('qty_ordered')->after('supplier_sku')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->dropColumn('supplier_sku');
            $table->dropColumn('qty_ordered');
        });
    }

}
