<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_setting', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('dist_aisle_rack')->comment('distance b/w aisle and rack in cm');
            $table->integer('walk_speed')->comment('Avg walk speed in cm/second');
            $table->integer('time_multipick')->comment('Additional Time for multipick in second');
            $table->integer('time_singlepick')->comment('Time to pick 1 pick in second');
            $table->integer('storage_buffer')->comment('Time to pick 1 pick in CBM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_setting');
    }
}
