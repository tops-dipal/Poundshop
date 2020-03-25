<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::dropIfExists('order_trans');
        Schema::create('order_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id')->unsigned()->comment('Primary key from order_master');
            $table->bigInteger('item_trans_id')->unsigned()->comment('Primary key from the order_trans table of the respective marketplace');
            $table->integer('warehouse_id')->unsigned()->comment('associated warehouse id configure which is store 
in warehouse store mapping tables');
            $table->bigInteger('product_id')->unsigned()->index()->comment('primary key of product master');
            $table->string('sku', 100)->comment('The seller SKU of the item. For Ebay in case of normal product, the SKU from ebay API will be stored here, while in case of variation order, the VariationSKU from the Ebay API will be stored here');
            $table->string('title', 500)->comment('The name of the item');
            $table->integer('qty_ordered')->unsigned()->comment('The number of items in the order');
            $table->integer('qty_return')->unsigned()->comment('The number of quantity return order Item');
            $table->decimal('item_price', 12, 2)->unsigned()->comment('Selling price of single quantity of the order item.');
            $table->decimal('item_total_price', 12, 2)->unsigned()->comment('Selling price of total quantity of the order item.');
            $table->decimal('item_total_discount', 12, 2)->unsigned()->comment('Discount of total quantity of the order item.');
            $table->string('item_price_currency', 3)->comment('Three-digit currency code');
            $table->decimal('shipping_price', 12, 2)->unsigned()->comment('Shipping price of single quantity of the order item.');
            $table->decimal('shipping_total_price', 12, 2)->unsigned()->comment('Shipping price of total quantity of the order item.');
            $table->string('shipping_price_currency', 3)->comment('Three-digit currency code');
            $table->decimal('transaction_total_price', 12, 2)->unsigned()->comment('Total price of the transaction including all the costs & removing discounts.');
            $table->string('transaction_price_currency', 3)->comment('Three-digit currency code');
            $table->datetime('item_estimate_ship_date')->comment('item estimate ship date');
            $table->datetime('item_estimate_delivery_date')->comment('item estimate delivery date');
            $table->string('magento_order_item_id', 20);
            $table->decimal('magento_total_tax', 12, 2)->unsigned();
            $table->enum('is_shipped', ['0', '1'])->default('0')->comment('0-Unshipped, 1-Shipped');
            $table->enum('is_rufunded', ['0', '1', '2'])->default('0')->comment('0-Refund Pending, 1- Partial Refunded, 2-Full Refunded');
            $table->enum('is_cancelled', ['0', '1'])->default('0')->comment('0-Not Cancel, 1-Cancelled');
            $table->enum('is_replaced', ['0', '1'])->default('0')->comment('order item replace or not.0-false,1-true');
            $table->enum('is_eligible_to_process', ['0', '1'])->default('1')->comment('0 = Not eligible to process for PPS or PO, 1 = Eligible to process for PPS or PO');
            $table->enum('item_process_status', ['0', '1', '2'])->default('0')->comment('0 = Not in PO/PPS, 1 = In PO, 2 = In PPS');
            $table->enum('is_processed', ['0', '1'])->default('0')->comment('0 = Order item is not processed for qty reservation process, 1 = Order processed');
            $table->integer('shipping_carrier')->comment('Primary key of shipping_carrier');
            $table->integer('shipping_service')->comment('Primary key of shipping_services');
            $table->decimal('insurance_amount', 10, 2)->comment('Insurance amount for shipment');
            $table->decimal('shipping_width', 10, 3)->unsigned()->comment('width of the shipping packages');
            $table->decimal('shipping_height', 10, 3)->unsigned()->comment('height of the shipping packages');
            $table->decimal('shipping_length', 10, 3)->unsigned()->comment('length of the shipping packages');
            $table->decimal('ship_weight', 10, 3)->unsigned()->comment('shipping weight');
            $table->string('shipping_label_file', 50)->comment('Shipping label file name');
            $table->decimal('estimated_shipping_price', 10, 2)->comment('Estimated price fetched from the desired carrier');
            $table->datetime('estimated_shipping_price_date')->comment('Last updated date for estimated shipping price');
            $table->enum('is_signature_required', ['0', '1'])->default('0')->comment('0 - Signature not required for shipment, 1 - Signature is required for shipment');
            $table->enum('is_label_generated', ['0', '1'])->default('0')->comment('0 - shipping label is not generated, 1 - shipping label is generated');
            $table->enum('is_invoice_generated', ['0', '1'])->default('0')->comment('0 - Invoice is not generated, 1 - Invoice is generated');
            $table->string('tracking_number', 50)->comment('Tracking number received by shipping service ');
            $table->string('tracking_id_type', 50)->comment('Shipment tracking number id type');
            $table->text('shipment_response')->comment('Serialized shipping rates response');
            $table->enum('is_profit_loss_processed', ['0', '1'])->default('0')->comment('0 - Not Processed Of Profit and Loss , 1 - Processed');
            $table->timestamps();
            $table->index(["item_trans_id"]);
            $table->index(["order_id"]);
            $table->index(["sku"]);
            
            $table->foreign('order_id')->references('id')->on('order_master')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouse_master')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_trans');
    }
}
