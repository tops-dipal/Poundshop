<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLoctionColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->dropColumn('location_id');
            $table->dropColumn('supplier_sku');
            $table->dropColumn('qty_ordered');
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
            $table->bigInteger('location_id')->after('is_photobooth')->unsigned()->index()->nullable();
            $table->string('supplier_sku')->after('barcode')->nullable();
            $table->string('qty_ordered')->after('barcode')->nullable();
        });
    }
}
