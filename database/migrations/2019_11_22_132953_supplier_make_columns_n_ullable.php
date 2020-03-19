<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierMakeColumnsNUllable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
            $table->decimal('credit_limit_allowed', 10, 2)->nullable()->change();
            $table->decimal('min_po_amt', 10, 2)->nullable()->change();
            $table->date('date_rel_start')->nullable()->change();
            $table->text('comment')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
            $table->decimal('credit_limit_allowed', 10, 2)->change();
            $table->decimal('min_po_amt', 10, 2)->change();
            $table->date('date_rel_start')->change();
            $table->text('comment')->change();
        });
    }
}
