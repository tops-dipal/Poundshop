<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Updateproductcolumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variation_themes', function (Blueprint $table) {
           $table->increments('id')->change();
        });

        Schema::table('products', function (Blueprint $table) {
           $table->integer('variation_theme_id')->unsigned()->index()->nullable()->after('main_image_marketplace')->comment('Foregin key of variation_themes');

           $table->foreign('variation_theme_id')->references('id')->on('variation_themes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variation_themes', function (Blueprint $table) {
           $table->bigIncrements('id')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_variation_theme_id_foreign');
            $table->dropColumn('variation_theme_id');
        });
    }
}
