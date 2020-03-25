<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LocationAssignTransRemoveCaseDetailId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_assign_trans', function (Blueprint $table) {
            
            $table->smallInteger('case_type')->nullable()->comment('1-Loose, 2-Inner,3-Outer')->change();

            $table->dropForeign('location_assign_trans_case_detail_id_foreign');
            $table->dropColumn('case_detail_id');
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
            
            $table->smallInteger('case_type')->nullable()->comment('1-Outer, 2-Inner,3-Loose')->change();
            
            $table->bigInteger('case_detail_id')->unsigned()->index()->nullable()->comment('PK of booking_po_product_case_details')->after('loc_ass_id');

            $table->foreign('case_detail_id')->references('id')->on('booking_po_product_case_details')->onDelete('cascade');
        });
    }
}
