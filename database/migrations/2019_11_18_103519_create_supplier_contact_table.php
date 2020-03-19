<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_contact', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id')->comment('from supplier master table')->unsigned()->index()->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone',20)->nullable();
            $table->string('designation')->nullable();
            $table->tinyInteger('is_primary')->default('1')->comment('0-No,1-Yes');
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            $table->foreign('supplier_id')->references('id')->on('supplier_master')->onDelete('cascade');            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');            
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
        Schema::dropIfExists('supplier_contact');
    }
}
