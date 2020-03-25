<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookingPOproductPOID extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->bigInteger('po_product_id')->unsigned()->index()->after('product_id')->nullable()->comment('Primary key of po_products');

            $table->bigInteger('po_id')->unsigned()->index()->after('product_id')->nullable()->comment('Primary key of purchase_order_master');
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
            $table->dropColumn('po_product_id');
            $table->dropColumn('po_id');
        });
    }
}
