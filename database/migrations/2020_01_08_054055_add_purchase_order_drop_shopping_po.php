<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseOrderDropShoppingPo extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->tinyInteger('is_drop_shipping')->default(0)->after('country_id')->comment("1=>Yes,0=>No");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->dropColumn('is_drop_shipping');
        });
    }

}
