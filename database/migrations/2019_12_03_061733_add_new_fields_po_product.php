<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsPoProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('po_products', function (Blueprint $table) {
           $table->decimal('itd_vat',32,2)->nullable()->after('total_delivery_charge')->comment('Total Product Cost o+ Import Duty + Delivery Charge Excluding VAT');
           
           $table->decimal('total_vat',32,2)->nullable()->after('itd_vat');
           $table->decimal('currency_exchange_rate',10,2)->nullable()->after('total_vat');
           $table->decimal('landed_price_in_pound',32,2)->nullable()->after('currency_exchange_rate');
           $table->decimal('gross_selling_price_in_vat',32,2)->nullable()->after('landed_price_in_pound');
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
           $table->dropIfExists('itd_vat');
           $table->dropIfExists('total_vat');
           $table->dropIfExists('currency_exchange_rate');
           $table->dropIfExists('landed_price_in_pound');
           $table->dropIfExists('gross_selling_price_in_vat');
        });
    }
}
