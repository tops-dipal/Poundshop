<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierColumnNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
            // $table->dropForeign('supplier_master_country_id_foreign');
            $table->dropColumn('country_id');

            // $table->dropForeign('supplier_master_state_id_foreign');
            $table->dropColumn('state_id');

            // $table->dropForeign('supplier_master_city_id_foreign');
            $table->dropColumn('city_id');

            $table->dropColumn('bene_country');
            $table->dropColumn('bene_state');
            $table->dropColumn('bene_city');

            $table->dropColumn('bank_country');
            $table->dropColumn('bank_state');
            $table->dropColumn('bank_city');

        });    

        Schema::table('supplier_master', function (Blueprint $table) {
            
            $table->bigInteger('city_id')->comment('from cities table')->unsigned()->index()->nullable()->after('address_line2');
            
            $table->bigInteger('state_id')->comment('from states table')->unsigned()->index()->nullable()->after('address_line2');

            $table->bigInteger('country_id')->comment('from countries table')->unsigned()->index()->nullable()->after('address_line2');

            // 
            $table->bigInteger('bene_city')->comment('from cities table')->unsigned()->index()->nullable()->after('bene_address2');
            
            $table->bigInteger('bene_state')->comment('from states table')->unsigned()->index()->nullable()->after('bene_address2');

            $table->bigInteger('bene_country')->comment('from countries table')->unsigned()->index()->nullable()->after('bene_address2');

            // 
            $table->bigInteger('bank_city')->comment('from cities table')->unsigned()->index()->nullable()->after('bank_address2');
            
            $table->bigInteger('bank_state')->comment('from states table')->unsigned()->index()->nullable()->after('bank_address2');

            $table->bigInteger('bank_country')->comment('from countries table')->unsigned()->index()->nullable()->after('bank_address2');

            
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade'); 

            // 
            $table->foreign('bene_country')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('bene_state')->references('id')->on('states')->onDelete('cascade');

            $table->foreign('bene_city')->references('id')->on('cities')->onDelete('cascade'); 

            // 
            $table->foreign('bank_country')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('bank_state')->references('id')->on('states')->onDelete('cascade');

            $table->foreign('bank_city')->references('id')->on('cities')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_master', function (Blueprint $table) {
           
        });    
    }
}
