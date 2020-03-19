<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id')->comment('from supplier master table')->unsigned()->index()->nullable();
            $table->bigInteger('supplier_contact')->comment('from supplier contact table')->unsigned()->index()->nullable();
            $table->string('po_number')->unique();
            $table->tinyInteger('po_status')->default('1')->comment('1-open(default),2-awaiting for supplier approval,3-negotiating with supplier,4-approved from supplier,5-in transit,6-completed,7-cancelled,8-revised');
            $table->date('po_date')->nullable();
            $table->date('exp_deli_date')->nullable();
            $table->date('po_cancel_date')->nullable();
            $table->longText('notes')->nullable();
            $table->bigInteger('recev_warehouse')->comment('from warehouse master table')->unsigned()->index()->nullable();
            $table->decimal('po_price', 10, 2)->nullable();
            $table->longText('terms_poundshop')->nullable();
            $table->longText('terms_supplier')->nullable();            
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            $table->foreign('supplier_id')->references('id')->on('supplier_master')->onDelete('cascade');
            $table->foreign('supplier_contact')->references('id')->on('supplier_contact')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_order_master');
    }
}
