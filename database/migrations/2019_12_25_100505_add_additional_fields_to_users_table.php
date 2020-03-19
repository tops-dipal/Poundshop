<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile_no',20)->nullable()->after('phone_no');
            $table->string('emergency_contact_num',20)->nullable()->after('mobile_no');
            $table->string('emergency_contact_name',191)->nullable()->after('emergency_contact_num');
            $table->text("comment")->nullable()->after("date_enroll");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn('mobile_no');
             $table->dropColumn('emergency_contact_num');
             $table->dropColumn('emergency_contact_name');
             $table->dropColumn('comment');
        });
    }
}
