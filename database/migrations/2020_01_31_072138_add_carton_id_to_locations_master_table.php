<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCartonIdToLocationsMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations_master', function (Blueprint $table) {
            $table->integer('carton_id')->index()->nullable()->comment("reference from cartons")->after('case_pack');
            $table->foreign('carton_id')->references('id')->on('carton_master')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations_master', function (Blueprint $table) {
             $table->dropColumn('carton_id');
        });
    }
}
