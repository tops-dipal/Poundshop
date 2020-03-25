<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoOrderTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::dropIfExists('magento_order_trans');

        Schema::create('magento_order_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('magento_id')->unsigned()->index()->comment('Foreign key of magento_order_details');
            $table->string('sku', 100)->comment('The seller SKU of the item');
            $table->string('magento_order_item_id', 14)->comment('Magento-defined order item identifier');
            $table->string('title', 500)->comment('The name of the item');
            $table->integer('qty_ordered')->unsigned()->comment('The number of items in the order');
            $table->integer('qty_shipped')->unsigned()->comment('The number of items shipped');
            $table->decimal('item_price', 12, 2)->unsigned()->comment('Selling price of single quantity of the order item. This value is derived by dividing the "item_total_price" by qty_ordered');
            $table->decimal('item_total_price', 12, 2)->unsigned()->comment("Selling price of total quantity of the order item. This value is retured by Amazon API for item_price but actaully its item_total_price.");
            $table->string('item_price_currency', 3)->comment('Three-digit currency code');
            $table->enum('price_designation', ['0', '1'])->comment("0 - if normal price, 1 - if its business price. Indicates that the selling price is a special price that is available only for Magento Business orders. Returned only for business orders");
            $table->decimal('shipping_price', 12, 2)->unsigned()->comment("Shipping price of single quantity of the order item. This value is derived by dividing the shipping_total_price by qty_ordered");
            $table->decimal('shipping_total_price', 12, 2)->unsigned()->comment("Shipping price of total quantity of the order item. This value is retured by Amazon API for shipping_price but actaully its shipping_total_price.");
            $table->string('shipping_price_currency', 3)->comment('Three-digit currency code');
            $table->decimal('giftwrap_price', 12, 2)->unsigned()->comment('Gift wrap price of single quantity of the order item. This value is derived by dividing the "giftwrap_total_price" by "qty_ordered"');
            $table->decimal('giftwrap_total_price', 12, 2)->unsigned()->comment('Gift wrap price of total quantity of the order item. This value is retured by Amazon API for giftwrap_price but actaully its giftwrap_total_price.');
            $table->string('giftwrap_price_currency', 3)->comment('Three-digit currency code');
            $table->string('gift_message_text', 250)->comment('A gift message provided by the buyer');
            $table->string('gift_wrap_level', 250)->comment('The gift wrap level specified by the buyer');
            $table->decimal('item_tax', 12, 2)->unsigned()->comment('Item tax price of single quantity of the order item. This value is derived by dividing the "item_total_tax" by "qty_ordered"');
            $table->decimal('item_total_tax', 12, 2)->unsigned()->comment('Item tax price of total quantity of the order item. This value is retured by Amazon API for item_tax but actaully its item_total_tax.');
            $table->string('item_tax_currency', 3)->comment('Three-digit currency code');
            $table->decimal('shipping_tax', 12, 2)->unsigned()->comment('Shipping tax price of single quantity of the order item. This value is derived by dividing the "shipping_total_tax" by "qty_ordered"');
            $table->decimal('shipping_total_tax', 12, 2)->unsigned()->comment('Shipping tax price of total quantity of the order item. This value is retured by Amazon API for shipping_tax but actaully its shipping_total_tax.');
            $table->string('shipping_tax_currency', 3)->comment('Three-digit currency code');
            $table->decimal('giftwrap_tax', 12, 2)->unsigned()->comment('Gift wrap tax price of single quantity of the order item. This value is derived by dividing the "giftwrap_total_tax" by "qty_ordered"');
            $table->decimal('giftwrap_total_tax', 12, 2)->unsigned()->comment('Gift wrap tax price of total quantity of the order item. This value is retured by Amazon API for giftwrap_tax but actaully its giftwrap_total_tax.');
            $table->string('giftwrap_tax_currency', 3)->comment('Three-digit currency code');
            $table->decimal('shipping_discount', 12, 2)->unsigned()->comment('Shipping discount price of single quantity of the order item. This value is derived by dividing the "shipping_total_discount" by "qty_ordered"');
            $table->decimal('shipping_total_discount', 12, 2)->unsigned()->comment('Shipping discount price of total quantity of the order item. This value is retured by Amazon API for shipping_discount but actaully its shipping_total_discount');
            $table->string('shipping_discount_currency', 3)->comment('Three-digit currency code');
            $table->decimal('promotion_discount', 12, 2)->unsigned()->comment('Promotion discount price of single quantity of the order item. This value is derived by dividing the "promotion_total_discount" by "qty_ordered"');
            $table->decimal('promotion_total_discount', 12, 2)->unsigned()->comment('Promotion discount price of total quantity of the order item. This value is retured by Amazon API for promotion_discount but actaully its promotion_total_discount.');
            $table->string('promotion_discount_currency', 3)->comment('Three-digit currency code');
            $table->string('promotion_id', 250)->comment('A list of PromotionIds');
            $table->enum('condition_id', ['New', 'Used', 'Collectible', 'Refurbished', 'Preorder', 'Club'])->comment('The condition of the item');
            $table->enum('condition_subtype_id', ['New', 'Mint', 'Very Good', 'Good', 'Acceptable', 'Poor', 'Club', 'OEM', 'Warranty', 'Refurbished Warranty', 'Refurbished', 'Open Box', 'Any', 'Other'])->comment('The subcondition of the item');
            $table->string('condition_note', 250)->comment('The condition of the item as described by the seller');
           
            $table->enum('updated', ['0', '1'])->default('0')->comment('0 if the item isnt updated in order_master table, 1 if the item is updated');
            
            $table->timestamps();
            
            $table->foreign('magento_id')->references('id')->on('magento_order_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_order_trans');
    }
}
