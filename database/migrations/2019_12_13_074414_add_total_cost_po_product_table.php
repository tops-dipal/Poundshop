<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalCostPoProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_products', function (Blueprint $table) {
             $table->decimal('total_cost',32,2)->nullable()->after('vat_in_amount');
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
             $table->removeColumn('total_cost');
        });
    }
}
