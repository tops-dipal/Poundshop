<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValueOfColumnsbooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('total_value_payable',10,2)->default(0)->after('total_completed_products');
            $table->integer('total_qty_instock')->default(0)->after('total_completed_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('total_value_payable');
            $table->dropColumn('total_qty_instock');
        });
    }
}
