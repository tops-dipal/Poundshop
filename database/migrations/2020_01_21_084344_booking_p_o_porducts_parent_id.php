<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookingPOPorductsParentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->bigInteger('parent_id')->after('booking_id')->index()->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('booking_po_products')->onDelete('cascade');
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
            $table->dropForeign('booking_po_products_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
    }
}
