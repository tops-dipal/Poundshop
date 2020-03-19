<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePoProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('po_products', function (Blueprint $table) {
            $table->decimal('net_selling_price_excluding_vat',32,2)->nullable()->after('landed_product_cost');
            $table->decimal('total_net_selling_price',32,2)->nullable()->after('net_selling_price_excluding_vat');
            $table->decimal('total_net_profit',32,2)->nullable()->after('total_net_selling_price');
            $table->decimal('total_net_margin',32,2)->nullable()->after('total_net_profit');
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
            $table->dropColumn('net_selling_price_excluding_vat');
            $table->dropColumn('total_net_selling_price');
            $table->dropColumn('total_net_profit');
            $table->dropColumn('total_net_margin');
        });
    }
}
