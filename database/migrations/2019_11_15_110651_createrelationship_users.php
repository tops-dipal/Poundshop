<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreaterelationshipUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');            
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');            
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');            
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
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_country_id_foreign');
            //$table->dropIndex('users_country_id_foreign');
            $table->dropForeign('users_state_id_foreign');
            //$table->dropIndex('users_state_id_foreign');
            $table->dropForeign('users_city_id_foreign');
            //$table->dropIndex('users_city_id_foreign');
            $table->dropForeign('users_created_by_foreign');
            //$table->dropIndex('users_created_by_foreign');
            $table->dropForeign('users_modified_by_foreign');
            //$table->dropIndex('users_modified_by_foreign');
        });
    }
}
