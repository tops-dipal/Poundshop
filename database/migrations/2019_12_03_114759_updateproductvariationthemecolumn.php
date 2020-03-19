<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Updateproductvariationthemecolumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('variation_theme_value');
            $table->dropColumn('variation_theme_name');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('variation_theme_value2')->nullable()->after('variation_theme_id');
            $table->string('variation_theme_value1')->nullable()->after('variation_theme_id');
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
            $table->dropColumn('variation_theme_value2');
            $table->dropColumn('variation_theme_value1');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('variation_theme_value')->nullable()->after('variation_theme_id');
            $table->string('variation_theme_name')->nullable()->after('variation_theme_id');
        });
    }
}
