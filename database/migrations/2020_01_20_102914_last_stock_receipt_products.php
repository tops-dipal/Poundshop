<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LastStockReceiptProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('last_stock_receipt_qty')->nullable()->after('is_listed_on_magento');
            $table->date('last_stock_receipt_date')->nullable()->after('is_listed_on_magento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('last_stock_receipt_qty');
            $table->dropColumn('last_stock_receipt_date');
        });
    }
}
