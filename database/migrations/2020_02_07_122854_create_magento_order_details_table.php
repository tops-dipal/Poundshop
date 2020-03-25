<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagentoOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('magento_order_id', 19)->comment('An Magento-defined order identifier');

            $table->string('magento_entity_id', 20)->comment("Entity Id is a field which gives relation between order and its items,Magento's entity_id == item's order_id. To find individual order details, we pass entity_id as order_id while doing request");
            
            $table->integer('store_id')->index()->comment('Id of store from the store_master');

            $table->integer('items_count')->unsigned()->default('0')->comment('Total number of items in the order. Only the count of items not their quantity');

            $table->datetime('purchase_date')->comment('The date when the order was created');
            
            $table->datetime('last_updated_date')->comment('The date when the order was last updated');
            
            $table->enum('order_status', ['pending', 'pending_paypal', 'pending_payment', 'payment_review', 'processing', 'fraud', 'holded', 'complete', 'closed', 'canceled', 'paypal_canceled_reversal'])->comment('The current order status');
            
            $table->enum('order_type', ['StandardOrder', 'Preorder'])->comment('The type of the order. StandardOrder - An order that contains items for which you currently have inventory in stock, Preorder - An order that contains items with a release date that is in the future');

            $table->string('order_channel', 20)->comment('The order channel of the first item in the order');

            $table->text('shipping_description')->comment('Shipping description from magento');
            $table->string('shipping_method', 50)->comment('shipping method of an order');
            
            $table->decimal('total_shipping_amount', 12, 2)->comment('Total shipping amount of an order');
            
            $table->decimal('discount_amount', 10, 2);
            
            $table->decimal('shipping_tax_amount', 12, 2)->comment('shipping tax on shipping amount');

            $table->string('ship_service_level', 30)->comment('The shipment service level of the order');
            
            $table->enum('shipping_service_level_category', ['Expedited', 'FreeEconomy', 'NextDay', 'SameDay', 'SecondDay', 'Scheduled', 'Standard'])->comment('The shipment service level category of the order');
            
            $table->string('shipping_label_cba', 50)->comment('A seller-customized shipment service level that is mapped to one of the four standard shipping settings supported by Checkout by Amazon (CBA). Note: CBA is available only to sellers in the United States (US), the United Kingdom (UK), and Germany (DE)');
            
            $table->string('shipping_address_name', 150)->comment('The name');
            
            $table->string('shipping_address_line1', 250)->comment('The street address');
            
            $table->string('shipping_address_line2', 250)->comment('Additional street address information, if required');
            
            $table->string('shipping_address_line3', 250)->comment('Additional street address information, if required');
            
            $table->string('shipping_address_city', 50)->comment('The city');
            
            $table->string('shipping_address_county', 50)->comment('The county');
            
            $table->string('shipping_address_district', 50)->comment('The district');
            
            $table->string('shipping_address_state', 50)->comment('The state or region');
            
            $table->string('shipping_address_zipcode', 10)->comment('The postal code');
            
            $table->bigInteger('shipping_address_country_id')->index()->unsigned();
            
            $table->string('shipping_address_phone', 20)->comment('The phone number. Optional. Not returned for Fulfillment by Amazon (FBA) orders');

            $table->string('billing_address_name', 150)->comment('Billing Name');
            
            $table->string('billing_address_line1', 250)->comment('Billing Street Address');
            
            $table->string('billing_address_line2', 250)->comment('Additional billing street address information, if required');
            
            $table->string('billing_address_line3', 250)->comment('Additional billing street address information, if required');
            
            $table->string('billing_address_city', 50)->comment('Billing City');
            
            $table->string('billing_address_county', 50)->comment('Billing Country');
            
            $table->string('billing_address_district', 50)->comment('Billing district');
            
            $table->string('billing_address_state', 50)->comment('Billing state or region');
            
            $table->string('billing_address_zipcode', 10)->comment('Billing adrress postal code');
            
            $table->string('billing_address_country', 2)->comment('Billing two-digit country code');
            
            $table->string('billing_address_phone', 20)->comment('Billing phone number. Optional. Not returned for Fulfillment by Amazon (FBA) orders');
            
            $table->integer('items_shipped')->unsigned()->comment('The number of items shipped');
            
            $table->integer('items_unshipped')->unsigned()->comment('The number of items unshipped');
            
            $table->decimal('total_tax_amount', 12, 2)->comment('total tax on amount');
            
            $table->decimal('order_total', 12, 2)->unsigned()->comment('The total stringge for the order');
            
            $table->decimal('subtotal', 12, 2)->comment('subtotal of an order');
            
            $table->string('order_currency', 3)->comment('Three-digit currency code');
            
            $table->enum('payment_method', ['COD', 'CVS', 'Other'])->comment(' The main payment method of the order');
            
            $table->integer('magento_customer_id')->comment('customer id of magento store');
            
            $table->text('buyer_comment')->comment('comment from buyer');
            
            $table->string('buyer_name', 150)->comment('The name of the buyer');
            
            $table->string('buyer_email', 150)->comment('The anonymized e-mail address of the buyer');
            
            $table->datetime('ship_date_earliest')->comment('The start of the time period that you have committed to ship the order');
            
            $table->datetime('ship_date_latest')->comment('The end of the time period that you have committed to ship the order');
            
            $table->datetime('delivery_date_earliest')->comment('The start of the time period that you have commited to fulfill the order');
            
            $table->datetime('delivery_date_latest')->comment('The end of the time period that you have commited to fulfill the order');
            
            $table->string('buyer_po_number', 50)->comment('The purchase order (PO) number entered by the buyer at checkout. Optional. Returned only for orders where the buyer entered a PO number at checkout');
            
            $table->enum('is_prime_order', ['0', '1'])->comment('0 - Normal Order, 1 - Prime Order. Indicates that the order is a seller-fulfilled Amazon Prime order');
            
            $table->enum('is_business_order', ['0', '1'])->comment('0 - Normal Order, 1 - Business Order. Indicates that the order is an Amazon Business order. An Amazon Business order is an order where the buyer is a Verified Business Buyer and the seller is an Amazon Business Seller');
            
            $table->enum('is_premium_order', ['0', '1'])->comment('0 - Normal Order, 1 - Premium Order. Indicates that the order has a Premium Shipping Service Level Agreement');
            
            $table->enum('processed', ['0','1', '2'])->default('0')->comment("0 if the order isn't processed for getting items, 1 if the order is processed, 2 if the order is processed but there are updates & we need to update items");

            $table->enum('updated', ['0', '1'])->default('0')->comment("0 if the order isn't updated in order_master table, 1 if the order is updated");

            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('store_master')->onDelete('cascade');

            $table->foreign('shipping_address_country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_order_details');
    }
}
