<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductBarcodeRemoveUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
             $table->dropUnique('product_barcodes_barcode_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
            $table->string('barcode')->unique()->index()->change();
        });
    }
}
