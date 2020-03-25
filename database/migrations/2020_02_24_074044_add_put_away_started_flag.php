<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPutAwayStartedFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $table->smallInteger('put_away_started')->comment('0 - No, 1 - Yes')->after('is_without_case_location')->default(0);
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
            $table->dropColumn('put_away_started');
        });
    }
}
