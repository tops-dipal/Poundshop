<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedUpdatedByToImportdutyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_duty', function (Blueprint $table) {
            $table->bigInteger('created_by')->unsigned()->index()->nullable()->after('country_id');
            $table->bigInteger('modified_by')->unsigned()->index()->nullable()->after('created_by');
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
        Schema::table('import_duty', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
             
            $table->dropForeign(['modified_by']);
            
        });
    }
}
