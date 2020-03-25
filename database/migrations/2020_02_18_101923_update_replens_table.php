<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReplensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replens', function (Blueprint $table) {
            $table->string('selected_bulk_location', 100)->after('cron_replan_qty')->comment('Selected Bulk location for the product')->nullable();
            $table->string('selected_pro_barcode', 100)->after('selected_bulk_location')->comment('Selected product Barcode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('replens', function (Blueprint $table) {
            $table->dropColumn('selected_bulk_location');
            $table->dropColumn('selected_pro_barcode');
        });
    }
}
