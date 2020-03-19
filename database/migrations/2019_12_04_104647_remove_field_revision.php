<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldRevision extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_revises', function (Blueprint $table) {
            $table->dropColumn('purchase_order_item_content');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_revises', function (Blueprint $table) {
            $table->text('purchase_order_item_content')->nullable()->after('purchase_order_content');
       });
    }
}
