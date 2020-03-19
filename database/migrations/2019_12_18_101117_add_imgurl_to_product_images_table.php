<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImgurlToProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->string('image_url',250)->nullable()->after('image_thumb');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('main_image_marketplace_url',250)->nullable()->after('main_image_marketplace');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('main_image_marketplace_url');
        });
    }
}
