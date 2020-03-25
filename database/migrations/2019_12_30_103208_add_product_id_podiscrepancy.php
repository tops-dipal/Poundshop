<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductIdPodiscrepancy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_purchase_orders_discrepancy', function (Blueprint $table) {
            $table->bigInteger('product_id')->comment('Forign key of products')->nullable()->after('booking_po_products_id')->unsigned()->index();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
            $table->dropForeign('booking_purchase_orders_discrepancy_product_id_foreign');
            $table->dropColumn('product_id');
        });
    }
}
