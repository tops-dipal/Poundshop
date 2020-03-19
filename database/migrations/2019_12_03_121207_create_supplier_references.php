<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_references', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('supplier_id')->comment('from supplier_master table')->unsigned()->index()->nullable();
            $table->string('supplier_name',250);
            $table->string('contact_person',250);
            $table->string('contact_no',20);
            $table->string('contact_email',250);
            $table->string('attachment_name',250)->nullable(); 
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_references');
    }
}
