<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierAlterCreaditLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
            $table->smallInteger('payment_term')->default(NULL)->nullable()->comment('1 - End Of The Month Following, 2 - Proforma Invoice, 3 - supplier_master.payment_days days after delivery date')->change();

            $table->smallInteger('retro_type')->after('retro_percent_discount')->comment('1 - Order Placed, 2 - Delivered')->nullable();
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
            $table->smallInteger('payment_term')->default(1)->comment('1 - Cash on delivery, 2 - Perform Invoice, 3 - supplier_master.payment_days days after delivery date')->change();

            $table->dropColumn('retro_type');
        });
    }
}
