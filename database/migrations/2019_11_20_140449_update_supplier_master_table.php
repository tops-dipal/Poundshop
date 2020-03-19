<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSupplierMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('supplier_master', function (Blueprint $table) {            
            //add new fields           
            $table->tinyInteger('supplier_category')->default('1')->comment('1-stock supplier(default),2-no stock supplier, 3-carrier, 4-drop shippling supplier')->after('avg_lead_time');
            $table->decimal('credit_limit_allowed', 10, 2)->after('supplier_category');
            $table->date('date_rel_start')->after('zipcode');
            $table->text('comment')->after('date_rel_start');            
            
            //change fields data type            
            $table->integer('id')->change();
            $table->integer('country_id')->change();
            $table->integer('state_id')->change();
            $table->integer('city_id')->change();
            $table->integer('bene_country')->change();
            $table->integer('bene_state')->change();
            $table->integer('bene_city')->change();
            $table->integer('bank_country')->change();
            $table->integer('bank_state')->change();
            $table->integer('bank_city')->change();            
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
        Schema::table('supplier_master', function (Blueprint $table) {

            $table->bigInteger('id')->change();

            $table->bigInteger('country_id')->change();
            $table->bigInteger('state_id')->change();
            $table->bigInteger('city_id')->change();

            $table->bigInteger('bene_country')->change();
            $table->bigInteger('bene_state')->change();
            $table->bigInteger('bene_city')->change();

            $table->bigInteger('bank_country')->change();
            $table->bigInteger('bank_state')->change();
            $table->bigInteger('bank_city')->change();

            $table->dropColumn('supplier_category');
            $table->dropColumn('credit_limit_allowed');
            $table->dropColumn('date_rel_start');
            $table->dropColumn('comment');            

        });
    }
}
