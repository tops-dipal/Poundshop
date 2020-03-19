<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('states', function (Blueprint $table) 
        {  
            //drop foreign key
            $table->dropForeign('states_country_id_foreign');                        
        });

        Schema::table('cities', function (Blueprint $table) 
        {
            $table->dropForeign('cities_state_id_foreign');                        
        });

        Schema::table('users', function (Blueprint $table) 
        {
            //drop foreign key
            $table->dropForeign('users_country_id_foreign');            
            $table->dropForeign('users_state_id_foreign');            
            $table->dropForeign('users_city_id_foreign');
        });

        Schema::table('supplier_master', function (Blueprint $table) 
        {
            $table->dropForeign('supplier_master_bank_city_foreign');            
            $table->dropForeign('supplier_master_bank_country_foreign');            
            $table->dropForeign('supplier_master_bank_state_foreign');            
            $table->dropForeign('supplier_master_bene_city_foreign');
            $table->dropForeign('supplier_master_bene_country_foreign');
            $table->dropForeign('supplier_master_bene_state_foreign');
            $table->dropForeign('supplier_master_city_id_foreign');
            $table->dropForeign('supplier_master_country_id_foreign');
            $table->dropForeign('supplier_master_state_id_foreign');
        });

        Schema::table('supplier_contact', function (Blueprint $table) 
        {
            $table->dropForeign('supplier_contact_supplier_id_foreign');            
        });

        Schema::table('supplier_term_condition_trans', function (Blueprint $table) 
        {
            $table->dropForeign('supplier_term_condition_trans_supplier_id_foreign');            
        });

        Schema::table('purchase_order_master', function (Blueprint $table) 
        {
            $table->dropForeign('purchase_order_master_supplier_id_foreign');            
            $table->dropForeign('purchase_order_master_supplier_contact_foreign');            
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
        Schema::table('states', function (Blueprint $table) 
        {      
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        Schema::table('cities', function (Blueprint $table) 
        {
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) 
        {
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');            
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');            
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });

        Schema::table('supplier_master', function (Blueprint $table) 
        {
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');            
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');            
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');                        
            $table->foreign('bene_country')->references('id')->on('countries')->onDelete('cascade');            
            $table->foreign('bene_state')->references('id')->on('states')->onDelete('cascade');            
            $table->foreign('bene_city')->references('id')->on('cities')->onDelete('cascade'); 
            $table->foreign('bank_country')->references('id')->on('countries')->onDelete('cascade');            
            $table->foreign('bank_state')->references('id')->on('states')->onDelete('cascade');            
            $table->foreign('bank_city')->references('id')->on('cities')->onDelete('cascade');
        });

        Schema::table('supplier_contact', function (Blueprint $table) 
        {
            $table->foreign('supplier_id')->references('id')->on('supplier_master')->onDelete('cascade');            
        });

        Schema::table('supplier_term_condition_trans', function (Blueprint $table) 
        {
            $table->foreign('supplier_id')->references('id')->on('supplier_master')->onDelete('cascade');
        });

        Schema::table('purchase_order_master', function (Blueprint $table) 
        {
            $table->foreign('supplier_id')->references('id')->on('supplier_master')->onDelete('cascade');
            $table->foreign('supplier_contact')->references('id')->on('supplier_contact')->onDelete('cascade');
        });
    }
}
