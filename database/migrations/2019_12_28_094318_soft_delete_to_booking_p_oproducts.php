<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SoftDeleteToBookingPOproducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('booking_po_product_locations', function (Blueprint $table) {
            $table->softDeletes();
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
            $this->dropColumn('deleted_at');
        });

        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $this->dropColumn('deleted_at');
        });

        Schema::table('booking_po_product_locations', function (Blueprint $table) {
            $this->dropColumn('deleted_at');
        });
    }
}
