<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLocationAssignTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_assign_trans', function (Blueprint $table) {                       
            $table->dropColumn('barcode');
            $table->bigInteger('barcode_id')->unsigned()->index()->nullable()->after('best_before_date')->comment('PK of product_barcodes');
            $table->integer('qty_per_box')->nullable()->after('barcode_id');
            $table->integer('total_boxes')->nullable()->after('qty_per_box');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_assign_trans', function (Blueprint $table) {
            $table->dropColumn('barcode_id');
            $table->string('barcode',191)->nullable()->after('best_before_date');
            $table->dropColumn('qty_per_box');
            $table->dropColumn('total_boxes');
        });
    }
}
