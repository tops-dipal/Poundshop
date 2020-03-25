<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConsiderParentDNQtyFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->smallInteger('consider_parent_delivery_note_qty')->default('0')->nullable()->comment('0 - No, 1 - Yes')->after('return_to_supplier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->dropColumn('consider_parent_delivery_note_qty');
        });
    }
}
