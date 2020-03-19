<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderRevisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_revises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('purchase_order_id')->unsigned()->index()->comment('reference to purchase order table');
            $table->smallInteger('revision_type')->default(1)->comment('1=>revision,2=>cancelled');
            $table->string('purchase_order_number_sequence',30);
            $table->text('purchase_order_content')->nullable();
            $table->text('purchase_order_item_content')->nullable();
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_order_master')->onDelete('cascade');            
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
        Schema::dropIfExists('purchase_order_revises');
    }
}
