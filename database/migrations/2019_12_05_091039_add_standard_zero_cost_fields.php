<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStandardZeroCostFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_products', function (Blueprint $table) {
           $table->decimal('standard_rate',10,2)->nullable()->after('total_product_cost');
           $table->decimal('zero_rate',10,2)->nullable()->after('standard_rate');
           $table->smallInteger('vat_type')->default(0)->comment('0 - Standard, 1 - Zero Rated, 3 - Mixed ')->after('zero_rate');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_products', function (Blueprint $table) {
            $table->dropColumn('standard_rate');
            $table->dropColumn('zero_rate');
            $table->dropColumn('vat_type');
         });
    }
}
