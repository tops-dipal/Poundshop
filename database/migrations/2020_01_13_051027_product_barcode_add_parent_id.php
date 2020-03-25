<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductBarcodeAddParentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->after('barcode')->nullable()->index();
            $table->foreign('parent_id')->references('id')->on('product_barcodes')->onDelete('cascade');
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
            $table->dropForeign('product_barcodes_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
    }
}
