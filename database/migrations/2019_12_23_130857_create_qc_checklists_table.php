<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQcChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_checklists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name",255);
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
        Schema::dropIfExists('qc_checklists');
    }
}
