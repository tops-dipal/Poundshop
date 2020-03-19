<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropsellingCategoryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
           $table->dropColumn('buying_category_id');
        });

        Schema::table('products', function (Blueprint $table) {
           $table->bigInteger('buying_category_id')->unsigned()->after('parent_id')->nullable();
           $table->foreign('buying_category_id')->references('id')->on('range_master')->onDelete('cascade');
           $table->dropColumn('selling_category_id');
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
            $table->integer('selling_category_id')->nullable()->after('buying_category_id');
        });
    }
}
