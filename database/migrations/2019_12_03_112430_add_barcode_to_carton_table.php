<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBarcodeToCartonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carton_master', function (Blueprint $table) {
            $table->string('barcode')->unique()->index()->nullable()->after('recycle_carton');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carton_master', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
}
