<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductCountLogic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
                DROP TRIGGER IF EXISTS BookingProductCountTriggerInsert;
                
                  CREATE TRIGGER BookingProductCountTriggerInsert  
                  AFTER INSERT ON booking_po_products
                  FOR EACH ROW
                  BEGIN
                      CALL BookingProductCount(NEW.booking_id);
                  END
            ");

        DB::unprepared("
                        DROP TRIGGER IF EXISTS BookingProductCountTriggerUpdate;
                          CREATE TRIGGER BookingProductCountTriggerUpdate  
                          AFTER UPDATE ON booking_po_products
                          FOR EACH ROW
                          BEGIN
                              CALL BookingProductCount(NEW.booking_id);
                        END
                        ");

         DB::unprepared("
                        DROP TRIGGER IF EXISTS BookingProductCountTriggerDelete;
                          CREATE TRIGGER BookingProductCountTriggerDelete  
                          AFTER DELETE ON booking_po_products
                          FOR EACH ROW
                          BEGIN
                              CALL BookingProductCount(OLD.booking_id);
                        END
                        ");

        DB::unprepared("
                        DROP TRIGGER IF EXISTS BookingPOProductCountTriggerInsert;
                        
                          CREATE TRIGGER BookingPOProductCountTriggerInsert  
                          AFTER INSERT ON booking_purchase_orders
                          FOR EACH ROW
                          BEGIN
                              CALL BookingProductCount(NEW.booking_id);
                            END
                    ");

        DB::unprepared("
                        DROP TRIGGER IF EXISTS BookingPOProductCountTriggerUpdate;
                        
                          CREATE TRIGGER BookingPOProductCountTriggerUpdate  
                          AFTER UPDATE ON booking_purchase_orders
                          FOR EACH ROW
                          BEGIN
                              CALL BookingProductCount(NEW.booking_id);
                          END
                    ");

         DB::unprepared("
                        DROP TRIGGER IF EXISTS BookingPOProductCountTriggerDelete;
                        
                          CREATE TRIGGER BookingPOProductCountTriggerDelete  
                          AFTER DELETE ON booking_purchase_orders
                          FOR EACH ROW
                          BEGIN
                              CALL BookingProductCount(OLD.booking_id);
                          END
                    ");

        DB::unprepared("
                        DROP PROCEDURE IF EXISTS BookingProductCount;

                        CREATE PROCEDURE BookingProductCount(
                          bookingId INT
                        )
                        BEGIN
                            DECLARE productCount INT DEFAULT 0;
                            DECLARE completedCount INT DEFAULT 0;
                            DECLARE completed_percent DECIMAL DEFAULT 0.00;
                          
                            select COUNT(1) INTO productCount from booking_purchase_orders INNER JOIN purchase_order_master ON booking_purchase_orders.po_id = purchase_order_master.id AND deleted_at IS NULL AND purchase_order_master.po_status NOT IN (10) INNER JOIN po_products ON purchase_order_master.id = po_products.po_id where booking_purchase_orders.booking_id = bookingId;
                            
                            select count(*) INTO completedCount from booking_po_products where booking_id = bookingId and status = 1 AND booking_po_products.product_parent_id IS NULL AND return_to_supplier != 1;
                            
                            IF productCount > 0 THEN
                              SET completed_percent = (completedCount/productCount) * 100;
                            END IF;

                            UPDATE bookings SET 
                              total_products = productCount, 
                                total_completed_products = completedCount, 
                                completed = completed_percent  
                                WHERE id = bookingId;
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
                        DROP TRIGGER IF EXISTS BookingPOProductCountTriggerInsert;
                        DROP TRIGGER IF EXISTS BookingPOProductCountTriggerUpdate;
                        DROP TRIGGER IF EXISTS BookingProductCountTriggerDelete;
                        DROP TRIGGER IF EXISTS BookingProductCountTriggerInsert;
                        DROP TRIGGER IF EXISTS BookingProductCountTriggerUpdate;
                        DROP PROCEDURE IF EXISTS BookingProductCount;
                        DROP TRIGGER IF EXISTS BookingPOProductCountTriggerDelete;
                    ');
        
    }
}
