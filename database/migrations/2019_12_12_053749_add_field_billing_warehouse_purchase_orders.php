<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldBillingWarehousePurchaseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('purchase_order_master', function (Blueprint $table) {
           $table->string('warehouse',50)->nullable()->after('recev_warehouse');
           $table->string('street_address1',250)->nullable()->after('warehouse');
           $table->string('street_address2',250)->nullable()->after('street_address1');
           $table->string('country',50)->nullable()->after('street_address2');
           $table->string('state',50)->nullable()->after('country');
           $table->string('city',50)->nullable()->after('state');
           $table->string('zipcode',50)->nullable()->after('city');
           
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
          $table->dropColumn('warehouse');
          $table->dropColumn('street_address1');
          $table->dropColumn('street_address2');
          $table->dropColumn('country');
          $table->dropColumn('state');
          $table->dropColumn('city');
          $table->dropColumn('zipcode');
          
        });
    }
}
