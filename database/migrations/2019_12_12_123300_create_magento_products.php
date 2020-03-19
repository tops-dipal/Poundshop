<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('store_id');
            $table->integer('magento_product_id');
            $table->string('sku',100)->nullable();
            $table->integer('parent_id');
            $table->integer('product_id');
            $table->string('upc',50)->nullable();
            $table->string('ean',50)->nullable();
            $table->string('isbn',50)->nullable();
            $table->string('magento_item_condition',50)->nullable();
            $table->string('magento_condition_notes',255)->nullable();
            $table->integer('magento_vendor_id');
            $table->string('parent_sku',50)->nullable();            
            $table->tinyInteger('product_type')->default('1')->comment('1-normal,2-variation,3-parent');
            $table->string('product_title',250)->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('quantity');
            $table->tinyInteger('visibility')->default('2')->comment('1-For Not Visible Individually, 2-For Catalog, 3-For search and 4-For search with catalog');
            $table->dateTime('magento_create_date')->nullable();
            $table->dateTime('magento_modified_date')->nullable();
            $table->decimal('selling_price', 10, 2); 
            $table->string('category',255)->nullable();
            $table->string('category_id',100)->nullable();         
            $table->dateTime('sold_date')->nullable();            
            $table->double('shipping_cost', 12, 2);
            $table->string('order_number',100)->nullable();
            $table->integer('return_id');
            $table->tinyInteger('is_in_stock')->default('0')->comment('stock status');
            $table->string('main_image_url',255)->nullable();
            $table->string('variation_theme',255)->nullable();
            $table->string('variation_theme_value',255)->nullable();
            $table->longText('description');
            $table->text('short_description');
            $table->string('category_ids',255)->nullable();
            $table->string('meta_title',255)->nullable();
            $table->text('meta_keyword');
            $table->longText('meta_description');
            $table->string('url_key',250)->nullable();
            $table->tinyInteger('is_feature')->default('0')->comment('0 = if not featured product, 1 = if featured product ');
            $table->decimal('product_weight', 10, 2); 
            $table->double('product_height', 12, 2);
            $table->double('product_width', 12, 2);
            $table->double('product_length', 12, 2);
            $table->tinyInteger('tax_class')->default('0')->comment('Taxable Goods');
            $table->string('brand',255)->nullable();
            $table->string('manufacturer_part_number',255)->nullable();
            $table->string('country_of_origin',255)->nullable();
            $table->string('manufacturer',255)->nullable();
            $table->tinyInteger('is_display')->default('1')->comment('0 - product will not display, 1 - product will display');
            $table->tinyInteger('is_enabled')->default('1')->comment('0-if the product is disable, 1-if the SKU is Enable, 2 = if product is deleted');
            $table->tinyInteger('is_detail_processed')->default('0')->comment('0 - No , 1- Yes');
            $table->tinyInteger('is_detail_updated')->default('0')->comment('0 = if not featured product, 1 = if featured product ');
            $table->tinyInteger('is_deleted_product')->default('0')->comment('0 - No , 1- Mark to delete , 2 - Delete , 3 - Fail to delete, 4 - Requested for relist');
            $table->tinyInteger('is_updated_in_product_master')->default('0')->comment('0 - No, 1 - Yes Updated');
            $table->dateTime('inserted_date')->nullable();
            $table->integer('modified_by');
            $table->dateTime('modified_date')->nullable();
            $table->dateTime('last_modified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_products');
    }
}
