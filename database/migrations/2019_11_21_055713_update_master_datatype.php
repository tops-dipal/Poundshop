<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('carton_master', function (Blueprint $table) {  
            $table->integer('id')->change();
        });

        Schema::table('pallets_master', function (Blueprint $table) {  
            $table->integer('id')->change();
        });

        Schema::table('totes_master', function (Blueprint $table) {  
            $table->integer('id')->change();
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
        Schema::table('carton_master', function (Blueprint $table) {  
            $table->bigInteger('id')->change();        
        });
        
        Schema::table('pallets_master', function (Blueprint $table) {  
            $table->bigInteger('id')->change();        
        });

        Schema::table('totes_master', function (Blueprint $table) {  
            $table->bigInteger('id')->change();        
        });
    }
}
