<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductTypeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropColumn();

        Schema::table('products', function (Blueprint $table) {
             $table->enum('product_type',['normal','parent','variation'])->nullable()->after('product_identifier_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropColumn();

        Schema::table('products', function (Blueprint $table) {
            $table->enum('product_type',['normal','parent','variation'])->default('normal')->after('product_identifier_type');
        });
    }

    public function dropColumn()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });    
    }
}
