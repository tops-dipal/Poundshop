<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedPurchaseOrderDownloadField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_master', function (Blueprint $table) {
           $table->dateTime('po_updated_at')->nullable()->after('modified_by');
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
           $table->dropColumn('po_updated_at');
                   
        });
    }
}
