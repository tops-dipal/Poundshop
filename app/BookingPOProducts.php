<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Products;

use App\BookingPODiscrepancy;
use Batch;

class BookingPOProducts extends Model {

    protected
            $table   = "booking_po_products";
    protected
            $guarded = [];

    /**
     * Get Booking pending product count
     * @author : Shubham Dayma
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static
            function pendingProductDetails($booking_id) {
        $result = array();

        if (!empty($booking_id)) {
            $self_object = self::select('booking_po_products.id', 'booking_po_products.difference',
            'booking_po_products.product_id',
            );

            $self_object->where('booking_po_products.booking_id', $booking_id);

            $self_object->whereNotNUll('booking_po_products.difference');

            $self_object->with(['bookingPODiscrepancy' => function($query) {
                    $query->selectRaw('SUM(qty_discrepancy) as qty_discrepancy_by_type, discrepancy_type, booking_po_products_id');
                    $query->groupBy('discrepancy_type');
                    $query->groupBy('booking_po_products_id');
                }]);

            $result = $self_object->get();

            $result = $result->toArray();
        }

        return $result;
    }

    public
            function bookingPODiscrepancy() {
        return $this->hasMany(BookingPODiscrepancy::class, 'booking_po_products_id');
    }

    public
            function outerCaseDetails() {
        return $this->hasMany(BookingPOProductCaseDetails::class, 'booking_po_product_id');
    }

    /**
     * @author Hitesh tAnk
     * @return type
     * @description Relation with products
     */
    public
            function product() {
        return $this->belongsTo(Products::class, 'product_id');
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function purchaseOrderProduct() {
        return $this->belongsTo(PurchaseOrderProduct::class, 'po_product_id');
    }

    public
            function stockPayFor() {
        return $this::join('booking_po_product_case_details', function($q) {
                            $q->on('booking_po_products.id', 'booking_po_product_case_details.booking_po_product_id');
                        })
                        ->join('booking_po_product_locations', function($q) {
                            $q->on('booking_po_product_case_details.id', 'booking_po_product_locations.case_detail_id');
                        })
                        ->join('locations_master', function($q) {
                            $q->on('booking_po_product_locations.location_id', 'locations_master.id');
                        })
                        ->whereIn('type_of_location', [1, 2, 3, 4])
                        ->where('booking_po_products.id', $this->id)
                        ->selectRaw('SUM(booking_po_product_locations.qty) as stockToPay')->first();
    }

    public static
            function getVariants($params) {
        if (!empty($params['parent_ids'])) {
            $self_object = self::selectRaw('
                                        products.product_identifier,
                                        products.title,
                                        products.main_image_internal,
                                        products.main_image_internal_thumb,
                                        products.is_request_new_photo,
                                        products.is_listed_on_magento,
                                        products.sku,
                                        products.product_type,
                                        booking_po_products.po_id,
                                        booking_po_products.parent_id,
                                        booking_po_products.put_away_quantity,
                                        booking_po_products.difference,
                                        booking_po_products.delivery_note_qty,
                                        booking_po_products.qty_received,
                                        booking_po_products.barcode as booking_barcode,
                                        booking_po_products.is_best_before_date,
                                        booking_po_products.is_inner_outer_case,
                                        booking_po_products.is_variant,
                                        booking_po_products.is_photobooth,
                                        booking_po_products.comments,
                                        booking_po_products.id as booking_po_product_id,
                                        booking_po_products.product_id,
                                        booking_po_products.product_parent_id,
                                        booking_po_products.consider_parent_delivery_note_qty,
                                        booking_po_products.lock_discrepancy,
                                        booking_po_products.scan_by_user_id,
                                        booking_po_products.scan_date
                                ');

            $self_object->join('products', function($join) {
                $join->on('products.id', '=', 'booking_po_products.product_id');
            });

            if (!empty($params['booking_id'])) {
                $self_object->where('booking_po_products.booking_id', $params['booking_id']);
            }

            if (!empty($params['parent_ids'])) {
                $self_object->whereIn('booking_po_products.parent_id', $params['parent_ids']);
            }

            return $self_object->get();
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

    public function getPOProductDetails()
    {
        return $this->belongsTo(PurchaseOrderProduct::class, 'po_product_id');
    }

    public static function setParentData($parent_id, $extra_data = array(), $parent_details = array())
    {
        $auto_discrepancy_location = array();

        $ordered_qty  = 0;

        $variation_id = array();

        if(empty($parent_details))
        {    
            $parent_details = self::find($parent_id);
        }

        $po_product_details = $parent_details->getPOProductDetails;

        if(!empty($po_product_details))
        {
            $ordered_qty = $po_product_details->total_quantity;    
        }    

        $self_object = self::selectRaw('id, qty_received, status, delivery_note_qty, is_photobooth');
        

        $self_object->where('parent_id', $parent_id);

        $result = $self_object->get()->toArray();

        if (!empty($result)) {
            $parent_qty_received = 0;

            $parent_delivery_note_qty = 0;

            $photobooth_qty = 0;

            foreach($result as $row)
            {
                $variation_id[] = $row['id'];

                if(!empty($row['delivery_note_qty']))
                {    
                    $parent_delivery_note_qty = $parent_delivery_note_qty + $row['delivery_note_qty'];
                }

                if (!empty($row['qty_received'])) {
                    $parent_qty_received = $parent_qty_received + $row['qty_received'];
                }

                if($row['is_photobooth'] == 1)
                {
                    $photobooth_qty = $photobooth_qty + 1; 
                }    
            }

            if(!empty($variation_id))
            {
                $self_obj = new self();

                $auto_discrepancy_location = $self_obj->bookingProductLocationTypeQty($variation_id);

                if($photobooth_qty > 0)
                {
                    $auto_discrepancy_location['photobooth_qty'] = $photobooth_qty;
                }    
            }

            $parent_details->pick_pallet_qty = 0;
            
            $parent_details->bulk_pallet_qty = 0;
            
            $parent_details->onhold_pallet_qty = 0;
            
            $parent_details->quarantine_pallet_qty = 0;
            
            $parent_details->return_to_supplier_pallet_qty = 0;

            if(!empty($auto_discrepancy_location))
            {
                // add qty to pallet
                if(!empty($auto_discrepancy_location[3]))
                {
                    $parent_details->pick_pallet_qty = $auto_discrepancy_location[3];
                }  
                
                if(!empty($auto_discrepancy_location[4]))
                {
                    $parent_details->bulk_pallet_qty = $auto_discrepancy_location[4];
                }
                
                if(!empty($auto_discrepancy_location[9]))
                {
                    $parent_details->onhold_pallet_qty = $auto_discrepancy_location[9];
                }
                
                if(!empty($auto_discrepancy_location[8]))
                {
                    $parent_details->quarantine_pallet_qty = $auto_discrepancy_location[8];
                }
                
                if(!empty($auto_discrepancy_location[10]))
                {
                    $parent_details->return_to_supplier_pallet_qty = $auto_discrepancy_location[10];
                }  
            }    

            if($parent_details->consider_parent_delivery_note_qty == 0)
            {    
                $parent_details->delivery_note_qty = $parent_delivery_note_qty;
            }
            
            $parent_details->difference = $parent_qty_received - $ordered_qty;

            if($parent_details->difference == 0 && $parent_qty_received > 0)
            {
                $parent_status = 1;
            }
            else {
                $parent_status = 0;
            }

            $parent_details->qty_received = $parent_qty_received;

            $parent_details->status = $parent_status;

            if (!empty($extra_data)) {
                foreach ($extra_data as $column => $value) {
                    $parent_details->$column = $value;
                }
            }

            $parent_details->save();
            
            // MANAGE AUTO DISCREPANCY FOR VARIATION PRODUCTS
            $parent_details_to_array = $parent_details->toArray();
            
            $parent_details_to_array['is_photobooth'] = 0;
            
            $auto_discrepancy_array['booking_product_details'] = $parent_details_to_array;
        
            $auto_discrepancy_array['location_array'] = $auto_discrepancy_location;
            
            $auto_discrepancy_array['qty_ordered'] = $ordered_qty;

            $product_status = self::manageAutoDiscrepancies($auto_discrepancy_array);
            
            return $product_status;
        }    
    }

    public static function manageAutoDiscrepancies($data)
    {
        /* LOCATION ARRAY :
            1 - Pick Location
            2 - Bulk Location
            3 - Pick Putaway Pallet
            4 - Bulk Putaway Pallet
            5 - Dispatch Location
            6 - Dropshipping Location
            7 - Aerosol Cage Location     
            8 - Quarantine Location
            9 - On Hold
            10 - Return to Supplier
        */
        $booking_product_details = $data['booking_product_details'];

        $over_details_for_auto = array();
        $product_status = 1;
        $shortage_qty = 0;
        $return_qty_to_supplier = 0;
        $difference = $booking_product_details['difference'];
        $qty_received = !empty($booking_product_details['qty_received']) ? $booking_product_details['qty_received'] : 0;
        $qty_ordered = !empty($data['qty_ordered']) ? $data['qty_ordered'] : 0;
        
        $photobooth_qty = !empty($booking_product_details['is_photobooth']) ? 1 : 0;

        if($photobooth_qty == 0 && !empty($data['location_array']['photobooth_qty']))
        {
            $photobooth_qty = $data['location_array']['photobooth_qty'];
        }    

        // GET DISCREPANCY RECORDS
        $where_array['booking_po_products_id'] = $booking_product_details['id'];
        
        $temp_discrepancy_details = BookingPODiscrepancy::where($where_array)->get()->toArray();

        if(!empty($temp_discrepancy_details))
        {
            foreach($temp_discrepancy_details as $discrepancy)
            {

                if($discrepancy['discrepancy_type'] == 1)
                {
                    $shortage_qty = $shortage_qty + $discrepancy['qty'];
                }

                if($discrepancy['status'] == 4 
                    && !in_array($discrepancy['discrepancy_type'], array('1')))
                {
                    $return_qty_to_supplier = $return_qty_to_supplier + $discrepancy['qty'];
                }    

                if($discrepancy['discrepancy_type'] == 2 && in_array($discrepancy['is_added_by_system'], array('1', '3')))
                {    
                    $over_details_for_auto[$discrepancy['status']] = $discrepancy['id'];
                }
            }    
        }    

        /* -- MANAGE AUTO DESCRIPANCY START -- */
        if(empty($booking_product_details['lock_discrepancy']))
        {    
            $total_pick_pallet_qty = 0;

            $keep_it_qty = 0;

            $over_qty = 0;
            
            $return_to_supplier_qty = 0;

            $db_array = array();
            
            $update_data = array();
            
            $insert_data = array();
            
            // OVER DISCREPANCY WITH ACTION "KEEP IT" FOR PICK AND BULK PALLET LOCATION
            if(!empty($data['location_array'][3]) || !empty($data['location_array'][4]) 
            )
            {
                if(!empty($data['location_array'][3]))
                {
                    $total_pick_pallet_qty = $total_pick_pallet_qty + $data['location_array'][3];
                }

                if(!empty($data['location_array'][4]))
                {
                    $total_pick_pallet_qty = $total_pick_pallet_qty + $data['location_array'][4];
                }    
                
                if($total_pick_pallet_qty > $qty_ordered)
                {    
                    $keep_it_qty = ($total_pick_pallet_qty + $photobooth_qty) - $qty_ordered;

                    // THIS IS SET TO 0 DUE TO, WHEN WE ADD "OVER DESCRIPANACY WITH NO ACTION" WE ARE CONSIDERING KEEP IT QTY ALONG WITH PHOTOBOOTH QTY.
                    $photobooth_qty = 0;

                    $db_array = array(
                                    'qty' => $keep_it_qty,
                                    'discrepancy_type' => 2,
                                    'status' => 2,
                                    'is_added_by_system' => 3,
                                );
                    
                    if(!empty($over_details_for_auto[$db_array['status']]))
                    {
                        $db_array['id'] = $over_details_for_auto[$db_array['status']];
                        $update_data[] = $db_array;
                        unset($over_details_for_auto[$db_array['status']]);

                    }
                    else
                    {
                        $insert_data[] = $db_array;
                    } 
                }       
            }

            // OVER DISCREPANCY WITHOUT ACTION
            if(!empty($data['location_array'][10]))
            {
                $return_to_supplier_qty = $data['location_array'][10];
            }    

            $over_qty = $qty_received - $return_to_supplier_qty - $keep_it_qty - $qty_ordered - $photobooth_qty;

            if($over_qty > 0)
            {    
                $db_array = array(
                                'qty' => $over_qty,
                                'discrepancy_type' => 2,
                                'status' => 0,
                                'is_added_by_system' => 1,
                            );

                if(!empty($over_details_for_auto[$db_array['status']]))
                {
                    $db_array['id'] = $over_details_for_auto[$db_array['status']];
                    $update_data[] = $db_array;
                    unset($over_details_for_auto[$db_array['status']]);
                }
                else
                {
                    $insert_data[] = $db_array;
                }  
            }   

            
            $dis_obj = new BookingPODiscrepancy;
            
            if(!empty($insert_data))
            {
                $insert_data = array_map(function($value) use ($booking_product_details){
                    $value['booking_po_products_id'] = $booking_product_details['id'];
                    $value['product_id'] = $booking_product_details['product_id'];
                    return $value;
                }, $insert_data);

                $dis_obj->insert($insert_data);
            }

            if(!empty($update_data))
            {
                Batch::update($dis_obj, $update_data, 'id');
            }

            if(!empty($over_details_for_auto))
            {
                $dis_obj->whereIn('id', $over_details_for_auto)->delete();
            } 

        }    
        /* -- MANAGE AUTO DESCRIPANCY END -- */   

        /* -- MANAGE BOOKING PRODUCT STATUS START -- */   
        // IF OVER DESCRIPENCY WITHOUT ANY ACTION IS ADDED
        if($product_status == 1)
        {    
            if(!empty($over_qty) && @$over_qty > 0 && empty($booking_product_details['lock_discrepancy']))
            {
                $product_status = 0;
            }
        }

        // IF PRODUCT IS PLACED ON QUARANTINE OR ON HOLD LOCATION
        if($product_status == 1)
        {    
            if(!empty($data['location_array'][8]) || !empty($data['location_array'][9]))
            {
                $product_status = 0;
            }
        }

        // IF PRODUCT QTY IS LESS RECEIVED
        if($product_status == 1)
        {
            if($qty_received < $qty_ordered)
            {
                $actual_shortage_qty = $qty_ordered - $qty_received;
                
                if($actual_shortage_qty != $shortage_qty)
                {
                    $product_status = 0;
                }    
            }
            elseif(!empty($shortage_qty) && ($qty_received >= $qty_ordered))
            {
                $product_status = 0;
            }
        }

        // IF PALLET RETURN TO SUPPLIER QTY != DIS. ACTION RETURN TO SUPPLIER
        if($product_status == 1)
        {

            $pallet_return_to_supplier_qty = 0;

            if(!empty($data['location_array'][10]))
            {
                $pallet_return_to_supplier_qty = $data['location_array'][10];
            }    

            if($return_qty_to_supplier != $pallet_return_to_supplier_qty)
            {
                $product_status = 0;
            }    
        } 

        // UPDATE BOOKING PRODUCT STATUS
        if(($booking_product_details['status'] != $product_status) || !isset($booking_product_details['status']))
        {
            $update_array['status'] = $product_status;

            self::where('id', $booking_product_details['id'])->update($update_array);
        }    

        return $product_status;

        /* -- MANAGE BOOKING PRODUCT STATUS END -- */   
    }

    public function bookingProductLocationTypeQty($booking_po_product_id)
    {
        $auto_discrepancy_location  = array();
        
        if(!empty($booking_po_product_id))
        {
            $case_object = \App\BookingPOProductCaseDetails::selectRaw('locations_master.type_of_location, SUM(qty) as pallet_qty');
            
            $case_object->join('booking_po_product_locations', function($join) {
                $join->on('booking_po_product_case_details.id', '=', 'booking_po_product_locations.case_detail_id');
            });

            $case_object->join('locations_master', function($join) {
                $join->on('locations_master.id', '=', 'booking_po_product_locations.location_id');
            });
            
            $case_object->whereIn('booking_po_product_case_details.booking_po_product_id', $booking_po_product_id);

            $case_object->groupBy('locations_master.type_of_location');
        
            $auto_discrepancy_location = $case_object->pluck('pallet_qty', 'locations_master.type_of_location')->toArray();

        }

        return $auto_discrepancy_location;    
    }

    // public static function getBookingProductLocations($booking_po_product_id)
    // {
    //     $case_object = \App\BookingPOProductCaseDetails::selectRaw('SUM(booking_po_product_locations.put_away_qty) as total_put_away_qty, location_id, booking_po_product_locations.id as id');
        
    //     $case_object->join('booking_po_product_locations', function($join) {
    //         $join->on('booking_po_product_case_details.id', '=', 'booking_po_product_locations.case_detail_id');
    //     });

    //     $case_object->where('booking_po_product_id', $booking_po_product_id);

    //     $case_object->havingRaw('SUM(booking_po_product_locations.put_away_qty) > 0');
        
    //     $case_object->groupBy('location_id');
        
    //     return $case_object->pluck('total_put_away_qty', 'location_id')->toArray();
    // }

     public static function getBookingProductLocations($booking_po_product_id, $where_raw_string = "")
    {
        $case_object = \App\BookingPOProductCaseDetails::selectRaw('
                        booking_po_product_case_details.booking_po_product_id,
                        booking_po_product_case_details.barcode,
                        booking_po_product_locations.location_id,
                        booking_po_product_locations.best_before_date,
                        booking_po_product_locations.put_away_qty
                    ');
        
        $case_object->leftJoin('booking_po_product_locations', function($join) {
            $join->on('booking_po_product_case_details.id', '=', 'booking_po_product_locations.case_detail_id');
        });

        if(!is_array($booking_po_product_id))
        {    
            $case_object->where('booking_po_product_id', $booking_po_product_id);
        }
        else
        {
            $case_object->whereIn('booking_po_product_id', $booking_po_product_id);
        }

        $case_object->where('is_include_count', 1);

        if(!empty($where_raw_string))
        {
            $case_object->whereRaw($where_raw_string);
        }    
        
        return $case_object->get()->toArray();
    }

    public static function getVariationPutawayQty($booking_po_product_parent_id)
    {
        $self_object = self::selectRaw('
                        booking_po_products.product_id,
                        booking_po_product_case_details.booking_po_product_id,
                        booking_po_product_case_details.barcode,
                        booking_po_product_locations.location_id,
                        booking_po_product_locations.best_before_date,
                        booking_po_product_locations.put_away_qty
                    ');
        
        $self_object->leftJoin('booking_po_product_case_details', function($join) {
            $join->on('booking_po_product_case_details.booking_po_product_id', '=', 'booking_po_products.id');
        });

        $self_object->leftJoin('booking_po_product_locations', function($join) {
            $join->on('booking_po_product_case_details.id', '=', 'booking_po_product_locations.case_detail_id');
        });

        $self_object->where('booking_po_products.parent_id', $booking_po_product_parent_id);

        $self_object->where('is_include_count', 1);

        $self_object->whereRaw('booking_po_product_locations.put_away_qty > 0');    
        
        return $self_object->get();
    }
}
