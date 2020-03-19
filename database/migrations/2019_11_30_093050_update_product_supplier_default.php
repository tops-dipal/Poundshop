<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductSupplierDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_supplier', function (Blueprint $table) {
           $table->renameColumn('defaults', 'is_default');
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
           $table->renameColumn('is_default', 'defaults');
        });
    }
}
