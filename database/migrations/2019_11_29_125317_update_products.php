<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if(Schema::hasColumn('products','product_type'))
           {
             $table->dropColumn('product_type');
           }
        });

        Schema::table('products', function (Blueprint $table) {

           $table->foreign('commodity_code_id')->references('id')->on('commodity_codes')->onDelete('cascade'); 
           $table->enum('product_type',['normal','parent','variation'])->default('normal')->after('product_identifier_type');
           $table->renameColumn('brand_id','brand');
           $table->decimal('vat_percent', 10, 2)->nullable()->after('bulk_quantity');
           $table->renameColumn('bulk_quantity','bulk_selling_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_commodity_code_id_foreign');
            $table->integer('commodity_code_id')->nullable()->change();
            $table->smallInteger('product_type')->default('1')->comment('1. Normal, 2. Parent, 3. Variation')->change();
            $table->renameColumn('brand','brand_id');
            $table->dropColumn('vat_percent');
            $table->renameColumn('bulk_quantity', 'bulk_selling_quantity');

        });
    }
}
