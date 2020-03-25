<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BoookingPoProductsAddPhotoBoth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_po_products', function (Blueprint $table) {
            $table->dropColumn('best_before_date');
            
            $table->smallInteger('is_photobooth')->after('is_variant')->nullable()->default(0)->comment('0 - No, 1 - Yes');
        });

        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $table->dropColumn('is_photobooth');
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
            
            $table->date('best_before_date')->after('is_variant')->nullable();

            $table->dropColumn('is_photobooth');
        });

        Schema::table('booking_po_product_case_details', function (Blueprint $table) {
            $table->smallInteger('is_photobooth')->after('total')->nullable()->default(0)->comment('0 - No, 1 - Yes');
        });
    }
}
