<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Updatelocationstable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations_master', function (Blueprint $table) {              
           $table->dropColumn('warehouse_id'); 
           $table->dropColumn('sort_order'); 
           $table->string('location',30)->nullable()->after('box');                 
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
           $table->integer('warehouse_id')->nullable()->after('id');           
           $table->integer('sort_order')->nullable()->after('box');
           $table->dropColumn('location');
        });
    }
}
