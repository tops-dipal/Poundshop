<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsPoBillingAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('purchase_order_master', function (Blueprint $table) {
           $table->string('billing_street_address1',250)->nullable()->after('city');
           $table->string('billing_street_address2',250)->nullable()->after('billing_street_address1');
           $table->string('billing_country',50)->nullable()->after('billing_street_address2');
           $table->string('billing_state',50)->nullable()->after('billing_country');
           $table->string('billing_city',50)->nullable()->after('billing_state');
           $table->string('billing_zipcode',50)->nullable()->after('billing_city');
           
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
          $table->dropColumn('billing_street_address1');
          $table->dropColumn('billing_street_address2');
          $table->dropColumn('billing_country');
          $table->dropColumn('billing_state');
          $table->dropColumn('billing_city');
          $table->dropColumn('billing_zipcode');
          
        });
    }
}
