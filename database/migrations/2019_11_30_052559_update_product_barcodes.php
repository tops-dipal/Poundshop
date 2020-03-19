<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductBarcodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
            if(Schema::hasColumn('product_barcodes','inner_case_qunatity'))
            {
                $table->renameColumn('inner_case_qunatity', 'case_quantity');
            }
            if(Schema::hasColumn('product_barcodes','outer_case_qunatity'))
            {
                $table->dropColumn('outer_case_qunatity');
            }
            
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
            $table->renameColumn('case_quantity', 'inner_case_qunatity');
            $table->integer('outer_case_qunatity')->nullable()->after('case_quantity');
        });
    }
}
