<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDecimalBoxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_assign_trans', function (Blueprint $table) {
            $table->integer('total_boxes')->nullable()->change();
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
            $table->decimal('total_boxes',10,1)->nullable()->change();
        });
    }
}
