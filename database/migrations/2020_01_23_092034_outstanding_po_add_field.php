<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OutstandingPoAddField extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->tinyInteger('is_outstanding_po')->default(0)->comment('0=>No, 1=>Yes')->after("is_drop_shipping");
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
            $table->dropColumn('is_outstanding_po');
        });
    }

}
