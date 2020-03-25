<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PurchaseOrderTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS PurchaseOrderBookingAddProduct;

            CREATE TRIGGER `PurchaseOrderBookingAddProduct` AFTER INSERT ON `po_products` FOR EACH ROW 
            BEGIN
                DECLARE var_booking_id INT;
                
                select booking_id INTO var_booking_id from booking_purchase_orders where po_id = NEW.po_id;
                
                IF (var_booking_id != '') THEN
                    CALL BookingProductCount(var_booking_id);
                END IF;
            END
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS PurchaseOrderDetailRemoved;

            CREATE TRIGGER `PurchaseOrderDetailRemoved` AFTER DELETE ON `po_products`
             FOR EACH ROW BEGIN
            DECLARE totalRows INT;
            DECLARE var_booking_id INT;
                set totalRows=(SELECT count(*)
                    FROM po_products WHERE po_id = OLD.po_id);

                IF (totalRows<1) THEN
                   UPDATE purchase_order_master set sub_total=0,total_import_duty=0,total_delivery_charge=0,total_cost=0, total_margin=0 ,total_space=0,cost_per_cube=0, total_number_of_cubes=0 ,remaining_space=0 WHERE id=OLD.po_id;
                  
                END IF;

                select booking_id INTO var_booking_id from booking_purchase_orders where po_id = OLD.po_id;
                
                IF (var_booking_id != '') THEN
                    CALL BookingProductCount(var_booking_id);
                END IF;
            END
        ");

         DB::unprepared("
            DROP TRIGGER IF EXISTS PurchaseOrderDraft;

            CREATE TRIGGER `PurchaseOrderDraft` AFTER INSERT ON `purchase_order_revises`
             FOR EACH ROW BEGIN

            DECLARE isExistPO INT;
                SET isExistPO = 0;
                SELECT po_id INTO isExistPO FROM booking_purchase_orders WHERE po_id = NEW.purchase_order_id LIMIT 1;

                IF (isExistPO = 0) THEN
                    UPDATE purchase_order_master SET po_status=1,po_cancel_date = null  WHERE id=NEW.purchase_order_id;
                    
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('
                DROP TRIGGER IF EXISTS PurchaseOrderBookingAddProduct;
                DROP TRIGGER IF EXISTS PurchaseOrderDraft;
                DROP TRIGGER IF EXISTS PurchaseOrderDetailRemoved;
            ');
    }
}
