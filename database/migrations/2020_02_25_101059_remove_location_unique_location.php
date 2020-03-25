<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveLocationUniqueLocation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('locations_master', function (Blueprint $table) {
            $table->dropUnique('locations_master_location_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('locations_master', function (Blueprint $table) {
            $table->unique('location');
        });
    }

}
