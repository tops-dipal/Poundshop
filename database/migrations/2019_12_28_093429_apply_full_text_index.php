<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApplyFullTextIndex extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        DB::statement('ALTER TABLE products ADD FULLTEXT search(title)');
        DB::statement('ALTER TABLE products ADD FULLTEXT search_sku(sku)');
        DB::statement('ALTER TABLE product_barcodes ADD FULLTEXT search_barcode(barcode)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('products', function($table) {
            $table->dropIndex('search');
            $table->dropIndex('search_sku');
        });
        Schema::table('product_barcodes', function($table) {
            $table->dropIndex('search_barcode');
        });
    }

}
