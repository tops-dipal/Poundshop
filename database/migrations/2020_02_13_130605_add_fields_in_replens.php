<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInReplens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replens', function (Blueprint $table) {
            $table->tinyInteger('replen_status')->default('0')->comment('1-Dispatch - Next Day,2-Dispatch - Standard,3-Short Dated,4-Expired,5-Promotion,6-Seasonal Products');
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
            $table->dropColumn('replen_status');
        });
    }
}
