<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBookingFieldLocationAssignTrans extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('location_assign_trans', function (Blueprint $table) {
            $table->bigInteger('booking_po_product_id')->unsigned()->nullable()->index()->comment('reference to booking_po_product table')->after('loc_ass_id');
            $table->bigInteger('booking_po_case_detail_id')->nullable()->index()->unsigned()->comment('reference to booking_po_product_case table')->after('booking_po_product_id');
            $table->bigInteger('booking_po_product_location_id')->nullable()->index()->unsigned()->comment('reference to booking_po_product_location table')->after('booking_po_case_detail_id');
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
            $table->dropColumn('booking_po_product_id');
            $table->dropColumn('booking_po_case_detail_id');
            $table->dropColumn('booking_po_product_location_id');
        });
    }

}
