<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastScannedFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            
            $table->dateTime('last_scanned_datetime')->nullable()->after('ros');
            $table->bigInteger('last_scanned_by')->comment('from users table')->unsigned()->index()->nullable()->after('last_scanned_datetime');
            $table->foreign('last_scanned_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['last_scanned_datetime','last_scanned_by']);
        });
    }
}
