<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedUpdatedByToMagentoQuantityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_quantity_log', function (Blueprint $table) {
            $table->bigInteger('created_by')->unsigned()->nullable()->after('is_quantity_posted');
            $table->bigInteger('modified_by')->unsigned()->nullable()->after('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_quantity_log', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('modified_by');
        });
    }
}
