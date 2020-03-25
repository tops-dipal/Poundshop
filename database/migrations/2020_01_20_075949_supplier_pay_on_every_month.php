<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierPayOnEveryMonth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
            $table->integer('pay_on_date_every_month')->nullable()->after('retro_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
            $table->dropColumn('pay_on_date_every_month');
        });
    }
}
