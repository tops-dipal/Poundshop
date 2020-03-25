<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovelocationIdforgienMaterialrecet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->dropForeign('booking_po_products_location_id_foreign');
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
            $table->foreign('location_id')->references('id')->on('locations_master')->onDelete('cascade');
        });
    }
}
