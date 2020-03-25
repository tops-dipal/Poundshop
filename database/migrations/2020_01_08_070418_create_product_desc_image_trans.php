<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDescImageTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_purchase_orders_discrepancy', function (Blueprint $table) {            
            $table->string('qty',50)->after('product_id')->nullable();
        });

        Schema::create('booking_purchase_orders_discrepancy_image', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->Integer('book_pur_desc_id')->comment('from booking_purchase_orders_discrepancy table')->unsigned()->index()->nullable();
            $table->string('image');            
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
            $table->dropColumn('qty');
        });

        Schema::dropIfExists('booking_purchase_orders_discrepancy_image');
    }
}
