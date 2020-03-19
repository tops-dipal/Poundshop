<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierCommentsFiled extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->text('supplier_comment')->nullable()->after('notes');
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
            $table->dropColumn('supplier_comment');
        });
    }

}
