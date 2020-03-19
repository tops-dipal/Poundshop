<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnPurchaseOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->decimal('sub_total',32,2)->nullable()->after('terms_supplier');
            $table->decimal('total_import_duty',32,2)->nullable()->after('sub_total');
            $table->decimal('total_delivery_charge',32,2)->nullable()->after('total_import_duty');
            $table->decimal('total_cost',32,2)->nullable()->after('total_delivery_charge');
            $table->decimal('total_margin',32,2)->nullable()->after('total_cost');
            $table->decimal('total_space',32,2)->nullable()->after('total_margin');
            $table->decimal('cost_per_cube',32,2)->nullable()->after('total_space');
            $table->decimal('total_number_of_cubes',32,2)->nullable()->after('cost_per_cube');
            $table->decimal('remaining_space',32,2)->nullable()->after('total_number_of_cubes');
            
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
            $table->dropColumn('sub_total');
            $table->dropColumn('total_import_duty');
            $table->dropColumn('total_delivery_charge');
            $table->dropColumn('total_cost');
            $table->dropColumn('total_margin');
            $table->dropColumn('total_space');
            $table->dropColumn('cost_per_cube');
            $table->dropColumn('total_number_of_cubes');
            $table->dropColumn('remaining_space');
            
        });
    }
}
