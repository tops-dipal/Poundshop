<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPoProductCubePerBoxFraction extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::table('po_products', function (Blueprint $table) {
            $table->decimal('cube_per_box', 32, 2)->default(0)->change();
            $table->decimal('total_num_cubes', 32, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::table('po_products', function (Blueprint $table) {
            $table->integer('cube_per_box')->nullable()->change();
            $table->integer('total_num_cubes')->nullable()->change();
        });
    }

}
