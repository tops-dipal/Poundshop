<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PoProductsAddFieldsBookingItemLock extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('po_products', function (Blueprint $table) {
            $table->tinyInteger('is_editable')->default('0')->comment('1=>No,0=>Yes')->after('product_id');
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
            $table->dropColumn('is_editable');
        });
    }

}
