<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAProductBarcodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
            $table->integer('outer_case_qunatity')->nullable()->after('inner_case_qunatity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
            $table->dropColumn('outer_case_qunatity');
        });
    }
}
