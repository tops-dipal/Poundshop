<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('account_no',40)->nullable();            
            $table->decimal('min_po_amt', 10, 2);
            $table->integer('avg_lead_time')->nullable()->comment('in number of days');
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->bigInteger('country_id')->comment('from countries table')->unsigned()->index();
            $table->bigInteger('state_id')->comment('from states table')->unsigned()->index();
            $table->bigInteger('city_id')->comment('from cities table')->unsigned()->index();
            $table->string('zipcode',20)->nullable();
            $table->tinyInteger('payment_term')->default('1')->comment('1-30 days before delivery(default),2-30 days after delivery,3-30 days net payment,4-on delivery,5-retro discount');
            $table->tinyInteger('payment_days')->nullable()->comment('for term 1,2,3 only');
            $table->string('beneficiary_name')->nullable();
            $table->string('bene_address1')->nullable();
            $table->string('bene_address2')->nullable();            
            $table->bigInteger('bene_country')->comment('from countries table')->unsigned()->index();
            $table->bigInteger('bene_state')->comment('from states table')->unsigned()->index();
            $table->bigInteger('bene_city')->comment('from cities table')->unsigned()->index();
            $table->string('bene_zipcode',20)->nullable();
            $table->string('bene_account_no',40)->nullable();
            $table->string('bene_bank_name')->nullable();
            $table->string('bank_address1')->nullable();
            $table->string('bank_address2')->nullable();
            $table->bigInteger('bank_country')->comment('from countries table')->unsigned()->index();
            $table->bigInteger('bank_state')->comment('from states table')->unsigned()->index();
            $table->bigInteger('bank_city')->comment('from cities table')->unsigned()->index();
            $table->string('bank_zipcode',20)->nullable();
            $table->string('bank_swift_code',40)->nullable();
            $table->string('bank_iban_no',40)->nullable();
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci'; 
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');            
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');            
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');            
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bene_country')->references('id')->on('countries')->onDelete('cascade');            
            $table->foreign('bene_state')->references('id')->on('states')->onDelete('cascade');            
            $table->foreign('bene_city')->references('id')->on('cities')->onDelete('cascade'); 
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
        Schema::dropIfExists('supplier_master');
    }
}
