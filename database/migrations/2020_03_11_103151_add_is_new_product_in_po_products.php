<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsNewProductInPoProducts extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('po_products', function (Blueprint $table) {
            $table->tinyInteger('is_new_product')->default(0)->comment('0=>new,1=>old')->after('is_editable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('po_products', function (Blueprint $table) {
            $table->dropColumn('is_new_product');
        });
    }

}
