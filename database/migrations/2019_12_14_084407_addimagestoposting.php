<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Addimagestoposting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->text('image_details')->nullable()->after('main_image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_product_posting', function (Blueprint $table) {
            $table->dropColumn('image_details');
        });
    }
}
