<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AutoDescripancyFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_purchase_orders_discrepancy', function (Blueprint $table) {
            $table->tinyInteger('is_added_by_system')->comment('0 - No, 1 - Discrepancy added but action is not taken by system, 2 - Discrepancy is added by user and action is taken by system, 3 - Both descripancy and action is added by system')->default('0')->after('status');
            
            $table->bigInteger('booking_po_products_id')->index()->unsigned()->change();

             $table->foreign('booking_po_products_id', 'foreign_booking_po_products')->references('id')->on('booking_po_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_purchase_orders_discrepancy', function (Blueprint $table) {
            $table->dropColumn('is_added_by_system');
            $table->dropForeign('foreign_booking_po_products');
        });
    }
}
