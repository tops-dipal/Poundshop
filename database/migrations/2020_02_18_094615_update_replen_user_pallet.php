<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReplenUserPallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replen_user_pallet', function (Blueprint $table) {
            $table->integer('warehouse_id')->after('id')->comment('from warehouse_master');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replen_user_pallet', function (Blueprint $table) {
            $table->dropColumn('warehouse_id');
        });
    }
}
