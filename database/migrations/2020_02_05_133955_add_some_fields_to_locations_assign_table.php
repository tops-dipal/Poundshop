<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldsToLocationsAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations_assign', function (Blueprint $table) {
             $table->dropColumn('current_qty');
             $table->integer('total_qty')->nullable()->after('is_mannual');
             $table->integer('allocated_qty')->nullable()->after('total_qty');
             $table->integer('available_qty')->nullable()->after('allocated_qty');
             $table->integer('warehouse_id')->unsigned()->index()->nullable()->after('id')->comment('PK of warehouse_master');
             $table->bigInteger('po_id')->unsigned()->index()->nullable()->after('booking_id')->comment('PK of purchase order');
             $table->foreign('warehouse_id')->references('id')->on('warehouse_master')->onDelete('cascade'); 
             $table->foreign('po_id')->references('id')->on('purchase_order_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations_assign', function (Blueprint $table) {
             $table->integer('current_qty')->default(0)->after('location_id');
             $table->dropForeign(['warehouse_id','po_id']);
             $table->dropColumn(['total_qty', 'allocated_qty', 'available_qty','warehouse_id','po_id']);
        });
    }
}
