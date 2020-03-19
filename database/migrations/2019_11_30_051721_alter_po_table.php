<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_products', function (Blueprint $table)
        {
            $table->decimal('mros',10,2)->after('expected_mros')->nullable();
            $table->string('barcode',20)->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('po_products', function (Blueprint $table)
        {
            $table->dropColumn('mros');
            $table->dropColumn('barcode');
        });
    }
}
