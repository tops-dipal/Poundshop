<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('qc_id')->unsigned()->comment('Primary key of QCChecklist');
            $table->foreign('qc_id')->references('id')->on('qc_checklists')->onDelete('cascade');
            $table->string("title",255);
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');            
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_points');
    }
}
