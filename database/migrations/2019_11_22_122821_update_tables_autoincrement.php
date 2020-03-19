<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesAutoincrement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('carton_master', function (Blueprint $table)
        {
            $table->integer('id')->autoIncrement()->change();
        });

        Schema::table('pallets_master', function (Blueprint $table)
        {
            $table->integer('id')->autoIncrement()->change();
        });

        Schema::table('supplier_master', function (Blueprint $table)
        {
            $table->integer('id')->autoIncrement()->change();
        });

        Schema::table('totes_master', function (Blueprint $table)
        {
            $table->integer('id')->autoIncrement()->change();
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
