<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLocationTranFieldTyoe extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('location_assign_trans', function (Blueprint $table) {
            $table->bigInteger('booking_po_product_id')->nullable()->change();
            $table->bigInteger('booking_po_case_detail_id')->nullable()->change();
            $table->bigInteger('booking_po_product_location_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('location_assign_trans', function (Blueprint $table) {
            $table->bigInteger('booking_po_product_id')->nullable(false)->change();
            $table->bigInteger('booking_po_case_detail_id')->nullable(false)->change();
            $table->bigInteger('booking_po_product_location_id')->nullable(false)->change();
        });
    }

}
