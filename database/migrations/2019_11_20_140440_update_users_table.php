<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
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
            //add new fields
            $table->date('date_pass_change')->nullable()->after('phone_no');
            $table->date('date_enroll')->nullable()->after('date_pass_change');
            //change fields data type
            $table->integer('country_id')->change();
            $table->integer('state_id')->change();
            $table->integer('city_id')->change();            
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
            //drop the new fields
            $table->dropColumn('date_pass_change');
            $table->dropColumn('date_enroll');
            //change the datatype to previous one
            $table->bigInteger('country_id')->change();
            $table->bigInteger('state_id')->change();
            $table->bigInteger('city_id')->change();
        });
    }
}
