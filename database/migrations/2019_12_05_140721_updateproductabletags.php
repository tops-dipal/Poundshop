<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Updateproductabletags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_heavy')->after('seasonal_to_date')->nullable()->default('0')->comment('0 - No, 1 - Yes');
            $table->tinyInteger('is_do_not_buy_again')->after('seasonal_to_date')->nullable()->default('0')->comment('0 - No, 1 - Yes');
            $table->tinyInteger('is_reduced')->after('seasonal_to_date')->nullable()->default('0')->comment('0 - No, 1 - Yes');
            $table->tinyInteger('is_flammable')->after('seasonal_to_date')->nullable()->default('0')->comment('0 - No, 1 - Yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('heavy');
            $table->dropColumn('do_not_buy_again');
            $table->dropColumn('reduced');
            $table->dropColumn('flammable');
        });
    }
}
