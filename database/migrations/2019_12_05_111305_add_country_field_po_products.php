<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryFieldPoProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_master', function (Blueprint $table) {
           $table->bigInteger('country_id')->unsigned()->nullable()->comment('reference to country table')->after('supplier_contact');
           $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
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
           $table->dropColumn('country_id');
         });
    }
}
