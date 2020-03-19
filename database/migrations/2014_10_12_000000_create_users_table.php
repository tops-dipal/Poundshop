<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('user_role')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');            
            $table->tinyInteger('gender')->default('1')->comment('1-male,2-female');
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->bigInteger('country_id')->comment('from countries table')->unsigned()->index();
            $table->bigInteger('state_id')->comment('from states table')->unsigned()->index();
            $table->bigInteger('city_id')->comment('from cities table')->unsigned()->index();
            $table->string('zipcode',20)->nullable();
            $table->string('profile_pic_org_name')->nullable();
            $table->string('profile_pic')->nullable();
            $table->string('phone_no',20)->nullable();
            $table->rememberToken();
            $table->bigInteger('created_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->comment('from users table')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default('1')->comment('1-Active,2-Inactive');
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';               
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
