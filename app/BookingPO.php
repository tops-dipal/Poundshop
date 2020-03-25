<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PurchaseOrder;
use DB;

class BookingPO extends Model {

    protected
            $table   = "booking_purchase_orders";
    protected
            $guarded = [];

    public
            function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public static
            function bookingProductCount($booking_id) {
        $self_object = self::selectRaw('COUNT(po_products.id) as product_count');

        $self_object->join('po_products', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'po_products.po_id');
        });

        $self_object->where('booking_purchase_orders.booking_id', $booking_id);

        $self_object->groupBy('po_products.product_id');

        $result = $self_object->get()->first();

        return $result->toArray();
    }

    // BOOKING WISE QUERY
    // public static
    //         function bookingProducts($params) {
    //     if (!empty($params['booking_id'])) {
    //         $return_to_supplier_query = DB::table('booking_po_products');
    //         $return_to_supplier_query->selectRaw("  NULL as product_identifier,
    //                                                 NULL as title,
    //                                                 NULL as main_image_internal,
    //                                                 NULL as main_image_internal_thumb,
    //                                                 NULL as is_request_new_photo,
    //                                                 NULL as is_listed_on_magento,
    //                                                 NULL as sku,
    //                                                 NULL as product_type,
    //                                                 NULL as po_number,
    //                                                 NULL as product_id,
    //                                                 NULL as barcode,
    //                                                 NULL as supplier_sku,
    //                                                 NULL as best_before_date,
    //                                                 booking_po_products.difference,
    //                                                 booking_po_products.delivery_note_qty,
    //                                                 booking_po_products.qty_received,
    //                                                 booking_po_products.barcode as booking_barcode,
    //                                                 booking_po_products.is_best_before_date,
    //                                                 booking_po_products.is_inner_outer_case,
    //                                                 booking_po_products.is_variant,
    //                                                 booking_po_products.comments,
    //                                                 booking_po_products.is_photobooth,
    //                                                 locations_master.location,
    //                                                 locations_master.type_of_location,
    //                                                 NULL as booking_total_quantity,
    //                                                 booking_po_products.id as booking_po_product_id,
    //                                                 booking_po_products.return_to_supplier,
    //                                                 booking_po_products.consider_parent_delivery_note_qty
    //                                             ");
    //         $return_to_supplier_query->where('booking_id',$params['booking_id']);
    //         $return_to_supplier_query->where('return_to_supplier',1);
    //         $return_to_supplier_query->leftJoin('locations_master', function ($join){
    //             $join->on('locations_master.id', '=', 'booking_po_products.location_id');
    //         });
    //         if (!empty($params['search']))
    //         {
    //             $return_to_supplier_query->where('barcode',$params['search']);
    //         }
    //         if ($params['search_type'] == 'pending_products') {
    //             $return_to_supplier_query->where(function($q) use($params) {
    //                 $q->whereNull('booking_po_products.id');
    //                 $q->orWhere('booking_po_products.status', 0);
    //             });
    //         }
    //         else if ($params['search_type'] == 'completed_products') {
    //             $return_to_supplier_query->where('booking_po_products.status', 1);
    //         }
    //         $self_object = self::selectRaw('
    //                                         products.product_identifier,
    //                                         products.title,
    //                                         products.main_image_internal,
    //                                         products.main_image_internal_thumb,
    //                                         products.is_request_new_photo,
    //                                         products.is_listed_on_magento,
    //                                         products.sku,
    //                                         products.product_type,
    //                                         purchase_order_master.po_number,
    //                                         po_products.product_id as product_id,
    //                                         po_products.barcode,
    //                                         po_products.supplier_sku,
    //                                         po_products.best_before_date as po_best_before_date,
    //                                         booking_po_products.difference,
    //                                         booking_po_products.delivery_note_qty,
    //                                         booking_po_products.qty_received,
    //                                         booking_po_products.barcode as booking_barcode,
    //                                         booking_po_products.is_best_before_date,
    //                                         booking_po_products.is_inner_outer_case,
    //                                         booking_po_products.is_variant,
    //                                         booking_po_products.comments,
    //                                         booking_po_products.is_photobooth,
    //                                         locations_master.location,
    //                                         locations_master.type_of_location,
    //                                         SUM(po_products.total_quantity) as booking_total_quantity,
    //                                         booking_po_products.id as booking_po_product_id,
    //                                         booking_po_products.return_to_supplier,
    //                                         booking_po_products.consider_parent_delivery_note_qty
    //                                     ');
    //         $self_object->join('po_products', function($join) {
    //             $join->on('booking_purchase_orders.po_id', '=', 'po_products.po_id');
    //         });
    //         $self_object->join('purchase_order_master', function($join) {
    //             $join->on('po_products.po_id', '=', 'purchase_order_master.id');
    //             $join->whereNull('purchase_order_master.deleted_at');
    //             $join->whereNotIn('purchase_order_master.po_status', array('10'));
    //         });
    //         $self_object->join('products', function($join) {
    //             $join->on('products.id', '=', 'po_products.product_id');
    //         });
    //         $self_object->leftJoin('booking_po_products', function($join) use($params)
    //         {
    //             $join->on('booking_purchase_orders.booking_id', '=', 'booking_po_products.booking_id');
    //             $join->where(function($q) use($params) {
    //                 $q->on('po_products.product_id','booking_po_products.product_id');
    //             });
    //         });
    //         $self_object->leftJoin('locations_master', function ($join){
    //             $join->on('locations_master.id', '=', 'booking_po_products.location_id');
    //         });
    //         $self_object->where('booking_purchase_orders.booking_id', (int) $params['booking_id']);
    //         if(!empty($params['filter_by_po']))
    //         {
    //             $self_object->where('booking_purchase_orders.po_id', $params['filter_by_po']);
    //         }
    //         if ($params['search_type'] == 'pending_products') {
    //             $self_object->where(function($q) use($params) {
    //                 $q->whereNull('booking_po_products.id');
    //                 $q->orWhere('booking_po_products.status', 0);
    //             });
    //         }
    //         else if ($params['search_type'] == 'completed_products') {
    //             $self_object->where('booking_po_products.status', 1);
    //         }
    //         if (!empty($params['search']))
    //         {
    //             $self_object->leftJoin('product_barcodes', function($join)
    //             {
    //                 $join->on('products.id', '=', 'product_barcodes.product_id');
    //             });
    //             $self_object->where(function($where_q) use($params)
    //             {
    //                 $where_q->where(function($q) use($params) {
    //                     $q->where('products.sku', $params['search']);
    //                     $q->orWhere('products.product_identifier', $params['search']);
    //                     $q->orWhere('po_products.barcode', $params['search']);
    //                     $q->orWhere('po_products.supplier_sku', $params['search']);
    //                     $q->orWhere('product_barcodes.barcode', $params['search']);
    //                     $q->orWhere('products.title', 'like', '%' . $params['search'] . '%');
    //                 });
    //                 $where_q->orWhereExists(function($q) use($params){
    //                     $q->selectRaw("var_booking_po_products.id from booking_po_products as var_booking_po_products INNER JOIN products  ON products.id = var_booking_po_products.product_id AND var_booking_po_products.product_parent_id IS NOT NULL LEFT JOIN product_barcodes as var_product_barcode ON products.id = var_product_barcode.product_id WHERE var_booking_po_products.parent_id = booking_po_products.id AND var_booking_po_products.product_parent_id IS NOT NULL AND (products.sku = '".$params['search']."' OR products.product_identifier = '".$params['search']."' OR products.title LIKE '%".$params['search']."%' OR var_product_barcode.barcode = '".$params['search']."') LIMIT 1 ");
    //                 });
    //             });
    //         }
    //         $self_object->groupBy('po_products.product_id');
    //         if ($params['sort_by'] == 'id') {
    //             $params['sort_by'] = 'products.id';
    //         }
    //         elseif ($params['sort_by'] == 'title') {
    //             $params['sort_by'] = 'products.title';
    //         }
    //         elseif ($params['sort_by'] == 'order') {
    //             $params['sort_by'] = 'booking_total_quantity';
    //         }
    //         elseif ($params['sort_by'] == 'quantity_received') {
    //             $params['sort_by'] = 'booking_po_products.qty_received';
    //         }
    //         elseif ($params['sort_by'] == 'delivery_note_qty') {
    //             $params['sort_by'] = 'booking_po_products.delivery_note_qty';
    //         }
    //         elseif ($params['sort_by'] == 'location') {
    //             $params['sort_by'] = 'locations_master.location';
    //         }
    //         $self_object->orderBy($params['sort_by'], $params['sort_direction']);
    //         $self_object->union($return_to_supplier_query);
    //         $result = $self_object->paginate($params['per_page']);
    //         return $result;
    //     }
    //     else {
    //         return array();
    //     }
    // }

    // PO WISE QUERY
    public static
            function bookingProducts($params) {

        if (!empty($params['booking_id'])) {

            $case_select = '
                                NULL as product_case_id,
                                NULL as case_product_id,
                                NULL as case_barcode,
                                NULL as case_barcode_type,
                                NULL as case_quantity
                           ';

            $return_to_supplier_query = DB::table('booking_po_products');

            $return_to_supplier_query->selectRaw("  NULL as product_identifier,
                                                    NULL as title,
                                                    NULL as main_image_internal,
                                                    NULL as main_image_internal_thumb,
                                                    NULL as is_request_new_photo,
                                                    NULL as is_new_product,
                                                    NULL as sku,
                                                    NULL as product_type,
                                                    NULL as po_number,
                                                    NULL as po_id,
                                                    NULL as po_product_id,
                                                    NULL as product_id,
                                                    NULL as barcode,
                                                    NULL as supplier_sku,
                                                    NULL as po_best_before_date,
                                                    NULL as po_is_variant,
                                                    booking_po_products.put_away_quantity,
                                                    booking_po_products.difference,
                                                    booking_po_products.delivery_note_qty,
                                                    booking_po_products.qty_received,
                                                    booking_po_products.barcode as booking_barcode,
                                                    booking_po_products.is_best_before_date,
                                                    booking_po_products.is_inner_outer_case,
                                                    booking_po_products.is_variant,
                                                    booking_po_products.comments,
                                                    booking_po_products.is_photobooth,
                                                    NULL as total_quantity,
                                                    booking_po_products.id as booking_po_product_id,
                                                    booking_po_products.return_to_supplier,
                                                    booking_po_products.consider_parent_delivery_note_qty,
                                                    booking_po_products.lock_discrepancy,
                                                    booking_po_products.scan_by_user_id,
                                                    booking_po_products.scan_date," . $case_select);

            $return_to_supplier_query->where('booking_id', $params['booking_id']);

            $return_to_supplier_query->where('return_to_supplier', 1);

            if (!empty($params['search'])) {
                $return_to_supplier_query->where('barcode', addslashes($params['search']));
            }

            if ($params['search_type'] == 'pending_products') {
                $return_to_supplier_query->where(function($q) use($params) {
                    $q->whereNull('booking_po_products.id');
                    $q->orWhere('booking_po_products.status', 0);
                });
            }
            else if ($params['search_type'] == 'completed_products') {
                $return_to_supplier_query->where('booking_po_products.status', 1);
            }

            if($params['show_discrepancies'] == 1)
            {
                $return_to_supplier_query->join('booking_purchase_orders_discrepancy',function($join){
                    $join->on('booking_purchase_orders_discrepancy.booking_po_products_id','=','booking_po_products.id');
                });
            }   

            if (!empty($params['search'])) {
                $case_select = '
                                    (CASE
                                        WHEN products.product_type = "parent" THEN var_product_barcodes.id
                                        WHEN products.product_type = "normal"  THEN product_barcodes.id
                                        ELSE NULL
                                        END
                                    ) as product_case_id,
                                    (CASE
                                        WHEN products.product_type = "parent" THEN var_product_barcodes.product_id
                                        WHEN products.product_type = "normal"  THEN product_barcodes.product_id
                                        ELSE NULL
                                        END
                                    ) as case_product_id,
                                    (CASE
                                        WHEN products.product_type = "parent" THEN var_product_barcodes.barcode
                                        WHEN products.product_type = "normal"  THEN product_barcodes.barcode
                                        ELSE NULL
                                        END
                                    ) as case_barcode,
                                    (CASE
                                        WHEN products.product_type = "parent" THEN var_product_barcodes.barcode_type
                                        WHEN products.product_type = "normal"  THEN product_barcodes.barcode_type
                                        ELSE NULL
                                        END
                                    ) as case_barcode_type,
                                    (CASE
                                        WHEN products.product_type = "parent" THEN var_product_barcodes.case_quantity
                                        WHEN products.product_type = "normal"  THEN product_barcodes.case_quantity
                                        ELSE NULL
                                        END
                                    ) as case_quantity
                                ';
            }

            $self_object = self::selectRaw('
                                            products.product_identifier,
                                            products.title,
                                            products.main_image_internal,
                                            products.main_image_internal_thumb,
                                            products.is_request_new_photo,
                                            po_products.is_new_product,
                                            products.sku,
                                            products.product_type,
                                            purchase_order_master.id as po_id,
                                            purchase_order_master.po_number,
                                            po_products.id as po_product_id,
                                            po_products.product_id as product_id,
                                            po_products.barcode,
                                            po_products.supplier_sku,
                                            po_products.best_before_date as po_best_before_date,
                                            po_products.is_variant as po_is_variant,
                                            booking_po_products.put_away_quantity,
                                            booking_po_products.difference,
                                            booking_po_products.delivery_note_qty,
                                            booking_po_products.qty_received,
                                            booking_po_products.barcode as booking_barcode,
                                            booking_po_products.is_best_before_date,
                                            booking_po_products.is_inner_outer_case,
                                            booking_po_products.is_variant,
                                            booking_po_products.comments,
                                            booking_po_products.is_photobooth,
                                            po_products.total_quantity,
                                            booking_po_products.id as booking_po_product_id,
                                            booking_po_products.return_to_supplier,
                                            booking_po_products.consider_parent_delivery_note_qty,
                                            booking_po_products.lock_discrepancy,
                                            booking_po_products.scan_by_user_id,
                                            booking_po_products.scan_date,' . $case_select);

            $self_object->join('purchase_order_master', function($join) {
                $join->on('booking_purchase_orders.po_id', '=', 'purchase_order_master.id');
                $join->whereNull('purchase_order_master.deleted_at');
                $join->whereNotIn('purchase_order_master.po_status', array('10'));
            });

            $self_object->join('po_products', function($join) {
                $join->on('booking_purchase_orders.po_id', '=', 'po_products.po_id');
            });

            $self_object->join('products', function($join) {
                $join->on('products.id', '=', 'po_products.product_id');
            });

            $self_object->leftJoin('booking_po_products', function($join) use($params) {
                $join->on('po_products.id', '=', 'booking_po_products.po_product_id');
            });

            $self_object->where('booking_purchase_orders.booking_id', (int) $params['booking_id']);


            // $self_object->whereIn('products.product_type', array('parent', 'normal'));

            if (!empty($params['filter_by_po'])) {
                $self_object->where('booking_purchase_orders.po_id', $params['filter_by_po']);
            }

            if ($params['search_type'] == 'pending_products') {
                $self_object->where(function($q) use($params) {
                    $q->whereNull('booking_po_products.id');
                    $q->orWhere('booking_po_products.status', 0);
                });
            }
            else if ($params['search_type'] == 'completed_products') {
                $self_object->where('booking_po_products.status', 1);
            }

            if (!empty($params['search'])) {
                $self_object->leftJoin('product_barcodes', function($join) use ($params) {
                    $join->on('products.id', '=', 'product_barcodes.product_id');
                    // $join->where('product_barcodes.barcode', addslashes($params['search']));
                });

                // variations
                $self_object->leftJoin('products as var_products', function($join) {
                    $join->on('products.id', '=', 'var_products.parent_id');
                    $join->where('products.product_type', '=', 'parent');
                });

                $self_object->leftJoin('product_barcodes as var_product_barcodes', function($join) use ($params) {
                    $join->on('var_products.id', '=', 'var_product_barcodes.product_id');
                    // $join->where('var_product_barcodes.barcode', addslashes($params['search']));
                });

                $self_object->where(function($where_q) use($params) {
                    $where_q->where(function($q) use($params) {
                        $q->where('products.sku', addslashes($params['search']));
                        $q->orWhere('products.product_identifier', addslashes($params['search']));
                        $q->orWhere('po_products.barcode', addslashes($params['search']));
                        $q->orWhere('po_products.supplier_sku', addslashes($params['search']));
                        $q->orWhere('products.title', 'like', '%' . addslashes($params['search']) . '%');
                        $q->orWhere('product_barcodes.barcode', addslashes($params['search']));

                        // variations
                        $q->orWhere('var_products.sku', addslashes($params['search']));
                        $q->orWhere('var_products.product_identifier', addslashes($params['search']));
                        $q->orWhere('var_products.title', 'like', '%' . addslashes($params['search']) . '%');
                        $q->orWhere('var_product_barcodes.barcode', addslashes($params['search']));
                    });

                    // $where_q->orWhereExists(function($q) use($params){
                    //     $q->selectRaw("var_booking_po_products.id from booking_po_products as var_booking_po_products INNER JOIN products  ON products.id = var_booking_po_products.product_id AND var_booking_po_products.product_parent_id IS NOT NULL LEFT JOIN product_barcodes as var_product_barcode ON products.id = var_product_barcode.product_id WHERE var_booking_po_products.parent_id = booking_po_products.id AND var_booking_po_products.product_parent_id IS NOT NULL AND (products.sku = '".addslashes($params['search'])."' OR products.product_identifier = '".addslashes($params['search'])."' OR products.title LIKE '%".addslashes($params['search'])."%' OR var_product_barcode.barcode = '".addslashes($params['search'])."') LIMIT 1 ");
                    // });
                });
            }

            if($params['show_discrepancies'] == 1)
            {
                $self_object->join('booking_purchase_orders_discrepancy',function($join){
                    $join->on('booking_purchase_orders_discrepancy.booking_po_products_id','=','booking_po_products.id');
                });
            }    

            if ($params['sort_by'] == 'id') {
                $params['sort_by'] = 'products.id';
            }
            elseif ($params['sort_by'] == 'title') {
                $params['sort_by'] = 'products.title';
            }
            elseif ($params['sort_by'] == 'order') {
                $params['sort_by'] = 'total_quantity';
            }
            elseif ($params['sort_by'] == 'quantity_received') {
                $params['sort_by'] = 'booking_po_products.qty_received';
            }
            elseif ($params['sort_by'] == 'delivery_note_qty') {
                $params['sort_by'] = 'booking_po_products.delivery_note_qty';
            }
            elseif ($params['sort_by'] == 'location') {
                $params['sort_by'] = 'locations_master.location';
            }

            $self_object->groupBy('po_products.id');

            $self_object->orderBy($params['sort_by'], $params['sort_direction']);

            $self_object->union($return_to_supplier_query);

            $result = $self_object->paginate($params['per_page']);
            
            return $result;
        }
        else {
            return array();
        }
    }

    public
            function getMainImageInternalThumbAttribute() {
        if (!empty($this->attributes['main_image_internal_thumb']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_internal_thumb'];
        else
            return url('/storage/uploads/product-images/no-image.jpeg');
    }

    public
            function getMainImageInternalAttribute() {
        if (!empty($this->attributes['main_image_internal']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_internal'];
        else
            return url('/storage/uploads/product-images/no-image.jpeg');
    }

    public
            function booking_details() {
        return $this->belongsTo(booking::class, 'booking_id');
    }

    public
            function po_id() {
        return $this->belongsTo(booking::class, 'booking_id');
    }

    /**
     * @author Hitesh Tank
     * @param type $ids
     * @return type
     */
    public
            function removePos($ids) {
        if (!isset($ids) || empty($ids)) {
            return $this->delete();
        }
        else {
            return $this->whereNotIn($ids)->delete();
        }
    }

    /**
      get booking po products for qc checklist

      author kinjal* */
    public static
            function getBookingProducts($booking_id) {
        
        $self_object = self::selectRaw('products.id,
                                        products.title,
                                        products.main_image_internal,
                                        products.main_image_internal_thumb,
                                        products.is_request_new_photo,
                                        products.sku,
                                        po_products.product_id as product_id,
                                        po_products.barcode,
                                        po_products.supplier_sku,
                                        po_products.best_before_date as po_best_before_date,
                                        booking_po_products.id as booking_po_product_id,
                                        booking_po_products.difference,
                                        booking_po_products.delivery_note_qty,
                                        booking_po_products.qty_received,
                                        booking_po_products.barcode as booking_barcode,
                                        booking_po_products.is_best_before_date,
                                        booking_po_products.is_inner_outer_case,
                                        booking_po_products.is_variant,
                                        booking_po_products.comments,
                                        booking_po_products.pick_pallet_qty,
                                        booking_po_products.bulk_pallet_qty,
                                        booking_po_products.is_photobooth,
                                        po_products.total_quantity,
                                        po_products.unit_price,
                                        products.is_listed_on_magento');

        $self_object->join('po_products', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'po_products.po_id');
        });

        $self_object->join('products', function($join) {
            $join->on('products.id', '=', 'po_products.product_id');
        });

        $self_object->leftJoin('booking_po_products', function($join) {
            $join->on('po_products.id', '=', 'booking_po_products.po_product_id');

            $join->on('booking_purchase_orders.booking_id', '=', 'booking_po_products.booking_id');
        });

        $self_object->where('booking_purchase_orders.booking_id', (int) $booking_id);

        $result = $self_object->get();

        return $result;
    }

    public static
            function getBookingProductDetails($product_id, $booking_id) {
        $self_object = self::selectRaw('po_products.unit_price');

        $self_object->join('po_products', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'po_products.po_id');
        });

        $self_object->join('booking_po_products', function($join) {
            $join->on('booking_purchase_orders.booking_id', '=', 'booking_po_products.booking_id');
            $join->on('po_products.product_id', '=', 'booking_po_products.product_id');
        });

        $self_object->where('booking_purchase_orders.booking_id', $booking_id);

        $self_object->groupBy('po_products.product_id');

        return $self_object->get()->first();
    }

    /** get  quarantin location products  - Kinjal* */
    public static
            function getQuarantinLocationProducts($booking_id) {
        $self_object = self::selectRaw('products.id,
                                            products.title');



        $self_object->join('po_products', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'po_products.po_id');
        });

        $self_object->join('products', function($join) {
            $join->on('products.id', '=', 'po_products.product_id');
        });

        $self_object->leftJoin('booking_po_products', function($join) {
            $join->on('booking_purchase_orders.booking_id', '=', 'booking_po_products.booking_id');
            $join->on('po_products.product_id', '=', 'booking_po_products.product_id');
        });

        $self_object->leftJoin('booking_po_product_case_details', function($join) {
            $join->on('booking_po_product_case_details.booking_po_product_id', '=', 'booking_po_products.id');
        });

        $self_object->leftJoin('booking_po_product_locations', function($join) {
            $join->on('booking_po_product_locations.case_detail_id', '=', 'booking_po_product_case_details.id');
        });

        $self_object->join('locations_master', function($join) {
            //$join->on(function($query){
            // $query->orOn('locations_master.id','=','booking_po_products.location_id');
            //   $query->orOn('locations_master.id','=','booking_po_product_locations.location_id');
            // });

            $join->on('locations_master.id', '=', 'booking_po_product_locations.location_id');

            $join->where('locations_master.type_of_location', '=', 8);
        });

        $self_object->where('booking_purchase_orders.booking_id', (int) $booking_id);

        $self_object->groupBy('po_products.product_id');

        $result = $self_object->get();

        return $result;
    }

}
