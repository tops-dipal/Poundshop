<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CaseDetailsWithoutCaselocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $table->smallInteger('is_without_case_location')->comment('1 - Yes, 0 - No')->default(0)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $table->dropColumn('is_without_case_location');
        });
    }
}
