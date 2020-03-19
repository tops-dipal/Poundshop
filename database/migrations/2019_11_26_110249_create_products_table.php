<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->index()->nullable()->comment('Id of products');
            $table->integer('buying_category_id')->nullable()->comment('Foreign key of range');
            $table->integer('selling_category_id')->nullable()->comment('Foreign key of marketplace_categories');
            $table->string('product_id',20)->index()->nullable();
            $table->tinyInteger('product_id_type')->default('1')->comment('1 - UPC, 2 - EAN');
            $table->tinyInteger('product_type')->default('1')->comment('1. Normal, 2. Parent, 3. Variation');
            $table->string('title')->nullable();
            $table->string('short_title')->nullable();
            $table->string('sku', 100)->nullable()->unique()->index();
            $table->unsignedBigInteger('country_of_origin')->index()->comment('Foreign key of countries')->nullable();
            $table->integer('threshold_quantity')->nullable();
            $table->string('brand_id')->nullable();
            $table->decimal('last_cost_price', 10, 2)->nullable();
            $table->decimal('single_selling_price', 10, 2)->nullable();
            $table->decimal('bulk_selling_price', 10, 2)->nullable();
            $table->integer('bulk_quantity')->nullable();
            $table->decimal('estimated_margin', 10, 2)->nullable();
            $table->decimal('recom_retail_price', 10, 2)->nullable();
            $table->text('long_description')->nullable();
            $table->mediumText('short_description')->nullable();
            $table->decimal('product_length', 10, 2)->nullable();
            $table->decimal('product_width', 10, 2)->nullable();
            $table->decimal('product_height', 10, 2)->nullable();
            $table->decimal('product_weight', 10, 2)->nullable();
            $table->integer('commodity_code')->nullable();
            $table->string('main_image_internal')->nullable();
            $table->string('main_image_marketplace')->nullable();
            $table->string('variation_theme_name')->nullable();
            $table->string('variation_theme_value')->nullable();
            $table->mediumText('comment')->nullable();
            $table->tinyInteger('is_deleted')->default('0')->comment('1 - No, 2 - Yes');
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->unsigned()->index()->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('country_of_origin')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
