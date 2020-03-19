<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPoImportType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_master', function (Blueprint $table)
        {
            $table->tinyInteger('po_import_type')->after('po_number')->comment('1=>UK PO,2=>Import PO');
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
            $table->dropColumn('po_import_type');
        });
    }
}
