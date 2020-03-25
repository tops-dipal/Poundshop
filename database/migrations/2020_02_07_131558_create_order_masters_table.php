<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->bigInteger('order_reference_id')->unsigned()->comment('Primary key from the order_details table of the respective marketplace');
            $table->string('order_channel_id', 26)->comment('Order Id provided by the respective marketplace.');
            $table->integer('store_id')->index()->comment('Id of store from the store_master');
            $table->integer('items_count')->unsigned()->default('0')->comment('Total number of items in the order. Only the count of items not their quantity');
            $table->string('internal_order_id', 40)->comment('Internal id of order');
            $table->string('order_status', 20)->comment('Manipulated Order status as provided by the respective marketplace');
            $table->string('marketplace_order_status', 20)->comment('Order status as provided by the respective marketplace');
            $table->datetime('order_date')->comment('Datetime when order was placed or created');
            $table->datetime('order_date_gmt')->comment('Order date time as per GMT timezone');
            $table->datetime('last_updated_date')->comment('Datetimwe when order was last updated');
            $table->string('shipping_name', 150)->comment('Shipping address name');
            $table->string('shipping_line1', 250)->comment('Address line 1');
            $table->string('shipping_line2', 250)->comment('Address line 2');
            $table->string('shipping_line3', 250)->comment('Address line 3');
            $table->string('shipping_city', 50)->comment('Shipping City');
            $table->string('shipping_county', 50)->comment('Shipping County');
            $table->string('shipping_district', 50)->comment('Shipping District');
            $table->string('shipping_state', 50)->comment('Shipping State');
            $table->string('shipping_zipcode', 10)->comment('Shipping zipcode');
            $table->bigInteger('shipping_country_id')->index()->unsigned();
            $table->string('shipping_phone', 20)->comment('Shipping contact number');
            $table->string('buyer_name', 150)->comment('Name of the buyer who placed the order');
            $table->string('buyer_email', 150)->comment('Email address of the buyer');
            $table->string('buyer_userid', 100)->comment('user id of buyer');
            $table->decimal('order_total', 12, 2)->unsigned()->comment('Total amount of the order which includes everthing');
            $table->decimal('order_discount', 12, 2)->unsigned()->comment('Discount of total quantity of the order.');
            $table->string('order_total_currency', 3)->comment('Three-digit currency code');
            $table->string('order_system_currency', 3)->comment('system calculation currency');
            $table->decimal('order_system_currency_rate', 8, 4)->comment('system calculation currency rate');
            $table->decimal('order_total_system_currency', 10, 2)->comment('system calculation currency amount');
            $table->integer('magento_entity_id')->unsigned();
            $table->decimal('magento_shipping_price', 10, 2)->unsigned();
            $table->decimal('magento_shipping_tax_amount', 10, 2)->unsigned();
            $table->text('order_note')->comment('order note');
            $table->string('payment_status', 20)->comment('Payment Status');
            $table->datetime('payment_date');
            $table->string('payment_reference_number', 20)->comment('Payment Status');
            $table->string('payment_method', 20)->comment('Payment Status');
            $table->string('internal_order_status', 200)->default('InProcess')->comment('order status for internal use only');
            $table->enum('is_shipped', ['0', '1', '2'])->default('0')->comment('0-Unshipped, 1- Partially Shipped, 2-Shipped');
            $table->enum('address_type', ['residential', 'business'])->comment('residential - Address is residential, business - Address is business');
            $table->decimal('refund_amount', 12, 2)->unsigned()->comment('Total amount of the refund which includes everthing');
            $table->enum('is_rufunded', ['0', '1', '2'])->default('0')->comment('0-Refund Pending, 1- Partially Refunded, 2-Refunded');
            $table->enum('is_cancelled', ['0', '1', '2'])->default('0')->comment('0-Not Cancel, 1- Partially Cancelled, 2-Cancelled');
            $table->enum('is_replaced', ['0', '1'])->default('0')->comment('order replace or not.0-fasle,1-true');
            $table->enum('is_qty_deducted', ['0', '1'])->default('0')->comment('0-Order Quantity not deducted,1-order quantity deducted');
            $table->enum('is_shipping_assignment_process', ['0', '1'])->default('0')->comment('0 - Assign preference is not processed, 1 - Assign preference is processed');
            $table->enum('system_status', ['Pick', 'Shipped'])->comment('System status for pick pack ship');
            $table->enum('order_process_status', ['0', '1', '2', '3'])->default('0')->comment('0 = Not in PO/PPS, 1 = All items In PO, 2 = All items In PPS, 3 = Some items in PO and some items in PPS');
            $table->datetime('order_estimate_ship_date')->comment('order estimate ship date');
            $table->datetime('order_estimate_delivery_date')->comment('order estimate delivery date');
            $table->enum('is_profit_loss_processed', ['0', '1'])->default('0')->comment('0 - Not Processed Of Profit and Loss , 1 - Processed');
            $table->enum('is_tracking_ftp_posted', ['0', '1'])->default('0')->comment('0 - Not Posted , 1 - Yes Posted');
            $table->enum('is_sales_tax_processed', ['0', '1'])->default('0')->comment('0 - No , 1- Yes');
            $table->enum('is_shipping_issue', ['0', '1', '2'])->default('0')->comment('0 - Shipping dont have issue, 1 - Shipping have issue, 2 - Shipping issue has resolved');
            $table->datetime('ship_date')->comment('Date when order is mark as ship');
           
            $table->timestamps();
            $table->unique(["order_channel_id", "store_id"]);
            $table->index(["order_reference_id"]);
            $table->index(["order_status"]);
            $table->index(["order_date"]);
            $table->index(["internal_order_id"]);
            $table->index(["internal_order_status"]);    

            $table->foreign('store_id')->references('id')->on('store_master')->onDelete('cascade');

            $table->foreign('shipping_country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_master');
    }
}
