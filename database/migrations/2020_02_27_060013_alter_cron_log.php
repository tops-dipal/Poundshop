<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCronLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_log', function (Blueprint $table) 
        {
            $table->tinyInteger('is_cron_failed')->default('0')->comment('1=>No,0=>Yes')->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_log', function (Blueprint $table) 
        {
            $table->dropColumn('is_cron_failed');
        });
    }
}
