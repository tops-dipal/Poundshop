<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoProductPostingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_product_posting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('store_id')->comment('Primary key of store master');
            $table->integer('magento_id')->comment('Primary Key Of magento_product');
            $table->string('magento_item_id',40)->nullable()->comment('product id of magento store');
            $table->integer('product_master_id')->comment('Primary Key Of Product master');
            $table->string('magento_product_id',15)->nullable();
            $table->enum('magento_product_id_type',['ean','upc','isbn'])->nullable();
            $table->string('sku',50)->index()->comment('a SKU (stock keeping unit) is an identifier for item defined by a seller');
            $table->integer('parent_id')->nullable()->index()->comment('parent id of the product  ');
            $table->string('parent_sku',50)->nullable()->index()->comment('parent sku of the in case of variaton product');
            $table->enum('product_type',['normal','parent','variation'])->nullable()->comment('type of the product');
            $table->string('brand',100)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('category_ids',255)->nullable();
            $table->string('main_image_url',250)->nullable();
            $table->string('manufacturer_part_number',50)->nullable()->comment('Part number provided by the original manufacturer of the merchant SKU.');
            $table->decimal('msrp', 12, 2)->nullable()->comment("The manufacturer's suggested retail price or list price for the product");
            $table->decimal('selling_price', 12, 2)->nullable()->comment("The overall price that the merchant SKU is priced at");
            $table->text('product_description')->nullable();
            $table->string('country_of_origin',20)->nullable()->comment('The country that the item was manufactured in.');
            $table->string('variation_theme',250)->nullable();
            $table->string('variation_theme_value',1000)->nullable();
            $table->integer('variation_theme_id')->nullable();
            $table->string('variation_theme_value1',50)->nullable();
            $table->string('variation_theme_value2',50)->nullable();
            $table->mediumText('item_specifics_details')->nullable();
            $table->tinyInteger('is_detail_processed')->default('0')->comment('0 - No, 1 - Yes');
            $table->tinyInteger('is_posted')->default('0')->comment('0 - No, 1 - Yes');
            $table->tinyInteger('is_revised')->nullable()->comment('if the original listing has been revised.0-false,1-true');
            $table->tinyInteger('posting_result_status')->nullable()->comment('0 - Nothing , 1 - Success , 2 - Warning , 3 - Error');
            $table->text('posting_result')->nullable();
            $table->tinyInteger('status')->nullable()->comment('0 - Inactive , 1 - Active');
            $table->decimal('weight', 12, 5)->nullable();
            $table->decimal('magento_product_height', 12, 5)->nullable();
            $table->decimal('magento_product_width', 12, 5)->nullable();
            $table->decimal('magento_product_length', 12, 5)->nullable();
            $table->integer('magento_vendor_id')->nullable();
            $table->string('meta_title',150)->nullable()->comment('product meta title');
            $table->text('meta_keyword')->nullable()->comment("product meta keywords");
            $table->longText('meta_description')->nullable()->comment("product meta description");
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->bigInteger('modified_by')->unsigned()->index()->nullable();    
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_product_posting');
    }
}
