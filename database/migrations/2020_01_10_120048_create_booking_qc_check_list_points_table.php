<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingQcCheckListPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_qc_check_list_points', function (Blueprint $table) {
            $table->bigIncrements('id');
             $table->unsignedBigInteger('qc_check_list_id')->index()->comment('reference to booking_qc_check_lists');
             $table->smallInteger('is_checked')->default(0)->comment('0-No, 1-Yes');
            $table->unsignedBigInteger('qc_option_id')->index()->nullable();
            $table->string('option_caption',255);
            $table->string('image',151)->nullable();
            $table->text('comments')->nullable();
            $table->bigInteger('created_by')->unsigned()->index();
            $table->bigInteger('modified_by')->unsigned()->index();
            $table->timestamps();
            $table->foreign('qc_check_list_id')->references('id')->on('booking_qc_check_lists')->onDelete('cascade');       
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
        Schema::dropIfExists('booking_qc_check_list_points');
    }
}
