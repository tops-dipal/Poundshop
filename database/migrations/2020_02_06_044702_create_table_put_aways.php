<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePutAways extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public
            function up() {
        Schema::create('put_aways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('booking_id', false)->unsigned()->index()->nullable()->comment('reference to booking table');
            $table->bigInteger('replan_id')->unsigned()->index()->nullable()->comment('reference to replan table');
            $table->bigInteger('product_id')->unsigned()->index()->nullable()->comment('reference to product table (reference not required)');
            $table->integer('qty')->default(0);
            $table->tinyInteger('put_away_type')->default(0)->comment('0=>Default,1=>Material Receipt,3=>Replan');
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('replan_id')->references('id')->on('replens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
            function down() {
        Schema::dropIfExists('put_aways');
    }

}
