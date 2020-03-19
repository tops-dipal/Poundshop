<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueBarcodePallets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('pallets_master', function (Blueprint $table) {
            
            $table->dropUnique(['barcode']);
        });
        
        Schema::table('carton_master', function (Blueprint $table) {
            
            $table->dropUnique(['barcode']);
        });
        
        Schema::table('totes_master', function (Blueprint $table) {
            
            $table->dropUnique(['barcode']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pallets_master', function (Blueprint $table) {
            $table->string('barcode')->unique();
        });
        
        Schema::table('carton_master', function (Blueprint $table) {
            $table->string('barcode')->unique();
        });
        
        Schema::table('totes_master', function (Blueprint $table) {
             $table->string('barcode')->unique();
        });
    }
}
