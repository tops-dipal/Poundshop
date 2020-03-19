<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('vendor_name')->comment('Name of vendor');
            $table->text('address1')->nullable()->comment('Address line1');
            $table->text('address2')->nullable()->comment('Address line2');
            $table->text('city')->nullable()->comment('City Name');
            $table->text('state')->nullable()->comment('State Name');
            $table->text('zipcode')->nullable()->comment('Zip Code');
            $table->text('contact_name')->nullable()->comment('Contact Name');
            $table->text('contact_title')->nullable()->comment('Title of vendor');
            $table->text('phone1')->nullable()->comment('Phone Number');
            $table->text('phone2')->nullable()->comment('Phone Number');
            $table->text('email1')->comment('Email Address of vendor');
            $table->text('email2')->nullable()->comment('Email Address of vendor');
            $table->tinyInteger('is_deleted')->comment('0 - Not Deleted, 1 - Deleted');
            $table->bigInteger('inserted_by')->nullable()->unsigned()->index()->comment('User_id who inserted this record');            
            $table->dateTime('inserted_date')->nullable()->comment('Date & time when this record was inserted');
            $table->bigInteger('modified_by')->nullable()->unsigned()->index()->comment('User_id who last modified this record');             
            $table->dateTime('modified_date')->nullable()->comment('Date & time when this record was last modified');
            $table->dateTime('last_modified');
            $table->foreign('inserted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade'); 
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor');
    }
}
