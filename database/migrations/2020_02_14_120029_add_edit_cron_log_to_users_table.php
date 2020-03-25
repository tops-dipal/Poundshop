<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditCronLogToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_log', function (Blueprint $table) {
            $table->string('store_id')->nullable()->change();
            $table->string('store_type')->nullable()->change();
            $table->integer('warehouse_id')->unsigned()->index()->nullable()->after('id')->comment('PK of warehouse_master')->after('id');
            $table->foreign('warehouse_id')->references('id')->on('warehouse_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_log', function (Blueprint $table) {
             $table->string('store_id')->change();
             $table->string('store_type')->change();
             $table->dropForeign('warehouse_id');
             $table->dropColumn('warehouse_id');
        });
    }
}
