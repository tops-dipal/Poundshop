<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateproductsisDeleted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_deleted')->after('seasonal_to_date')->default('0')->comment('0 - No, 1 - Yes');
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
            $table->dropColumn('is_deleted');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_deleted')->after('seasonal_to_date')->default('0')->comment('1 - No, 2 - Yes');
        });
    }
}
