<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStandardRateValuePoProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('po_products', function (Blueprint $table) {
           $table->decimal('standard_rate_value',10,2)->nullable()->after('standard_rate');
           $table->decimal('zero_rate_value',10,2)->nullable()->after('standard_rate_value');
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
           $table->dropColumn('standard_rate_value');
           $table->dropColumn('zero_rate_value');
         });
    }
}
