<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThumbcolumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('main_image_internal_thumb',191)->nullable()->comment('thum url of internal image')->after('main_image_internal');
            $table->string('main_image_marketplace_thumb',191)->nullable()->comment('thum url of marketplace image')->after('main_image_marketplace');
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
            $table->dropColumn('main_image_internal_thumb');
            $table->dropColumn('main_image_marketplace_thumb');
        });
    }
}
