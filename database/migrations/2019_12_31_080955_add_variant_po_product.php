<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariantPoProduct extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('po_products', function (Blueprint $table) {
            $table->tinyInteger('is_variant')->default(0)->comment("1->yes,0->no")->after('supplier_sku');
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
            $table->dropColumn('is_variant');
        });
    }

}
