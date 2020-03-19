<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_supplier', function (Blueprint $table) {
            $table->dropForeign('product_supplier_created_by_foreign');
            $table->dropForeign('product_supplier_modified_by_foreign');
            
            $table->dropColumn('created_by');
            $table->dropColumn('modified_by');
        });

        Schema::table('product_supplier', function (Blueprint $table) {
            $table->bigInteger('modified_by')->unsigned()->index()->nullable()->after('note');
            $table->bigInteger('created_by')->unsigned()->index()->nullable()->after('note');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade'); 
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_supplier', function (Blueprint $table) {
            
        });
    }
}
