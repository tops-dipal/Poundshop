<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookingCaseDetailsChangeCOmment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $table->smallInteger('case_type')->comment('1 - Loose, 2 - Inner, 3 - Outer')->nullable()->change();
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
            $table->smallInteger('case_type')->comment('1 - Outer, 2 - Inner, 3 - Loose')->nullable()->change();
        });
    }
}
