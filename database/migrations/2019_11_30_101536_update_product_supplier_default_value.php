<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductSupplierDefaultValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_supplier', function (Blueprint $table) {
            $table->integer('available')->default(0)->change();
            $table->smallInteger('is_default')->default(0)->comment('1=>yes,0=>no')->change();
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
            $table->smallInteger('available')->default(1)->comment('1=>yes,0=>no')->change();
            $table->smallInteger('is_default')->default(1)->comment('1=>yes,0=>no')->change();
        });
    }
}
