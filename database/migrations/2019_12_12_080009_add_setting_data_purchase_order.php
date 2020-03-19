<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingDataPurchaseOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_master', function (Blueprint $table) {
           $table->decimal('zero_rate_value',10,2)->nullable()->after('po_number');
           $table->decimal('standar_rate_value',10,2)->nullable()->after('zero_rate_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_master', function (Blueprint $table) {
          $table->dropColumn('zero_rate_value');
          $table->dropColumn('standar_rate_value');
        });
    }
}
