<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductIdToBookingQcCheckListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_qc_check_lists', function (Blueprint $table) {
            $table->bigInteger('product_id')->unsigned()->index()->after('booking_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_qc_check_lists', function (Blueprint $table) {
             $table->dropColumn('product_id');
        });
    }
}
