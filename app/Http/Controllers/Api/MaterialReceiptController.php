<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Common\CreateRequest;
use App\BookingPOProducts;
use App\BookingPO;
use App\BookingPOProductLocation;
use App\BookingPOProductCaseDetails;
use App\BookingPurchaseOrdersDiscrepancyImage;
use Batch;
use DB;
use App\Events\SendMail;
use App\Booking;
use App\BookingPODiscrepancy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Imagine;
use Intervention\Image\ImageManagerStatic as Image;
use Gumlet\ImageResize;
use App\ProductBarcode;
use App\Products;
use App\Locations;
use App\PurchaseOrder;
use App\LocationAssign;

class MaterialReceiptController extends Controller {

    /**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    function __construct(Request $request) {
        $route = $request->route();

        if (!empty($route)) {
            $action_array = explode('@', $route->getActionName());

            $function_name = !empty($action_array[1]) ? $action_array[1] : '';

            if (!empty($function_name)) {
                if ($function_name == 'pendingProductCount') {
                    CreateRequest::$roles_array = [
                        'booking_id' => 'required',
                    ];
                }

                if ($function_name == 'saveProduct') {
                    CreateRequest::$roles_array = [
                        'booking_id'        => 'required',
                        // 'product_id' => 'required',
                        'barcode'           => 'required',
                        // 'delivery_note_qty' => 'required',
                        // 'qty_received'      => 'required',
                        // 'location' => 'required',
                        // 'var_sku.*'         => 'sometimes|required|unique:products,sku',
                    ];
                }

                if ($function_name == 'setBookingArriveddate') {
                    CreateRequest::$roles_array = [
                        'booking_id'   => 'required',
                        'arrived_date' => 'required',
                    ];
                }

                if ($function_name == 'saveProductCaseDetails') {
                    CreateRequest::$roles_array = [
                        'booking_po_product_id' => 'required',
                    ];
                }

                if ($function_name == 'saveProductComment') {
                    CreateRequest::$roles_array = [
                        'booking_po_product_id' => 'required',
                    ];
                }

                if ($function_name == 'setProductWeb') {
                    CreateRequest::$roles_array = [
                        'booking_id' => 'required',
                            // 'product_id' => 'required',
                    ];
                }

                if ($function_name == 'setBookingCompleted') {
                    CreateRequest::$roles_array = [
                        'booking_id' => 'required',
                    ];
                }

                if ($function_name == 'saveProductVariations') {
                    CreateRequest::$roles_array = [
                        'booking_po_product_id' => 'required',
                        'booking_id'            => 'required',
                        'product_id'            => 'required',
                        'variation_theme'       => 'required',
                        'var_sku_id.0'          => 'required',
                        // 'var_sku.*'             => 'sometimes|required|unique:products,sku',
                    ];
                }

                if ($function_name == 'saveProductForReturnToSupplier') {
                    CreateRequest::$roles_array = [
                        'booking_id' => 'required',
                        'barcode'    => 'required',
                    ];
                }

                if ($function_name == 'setParentProductDeliveryNoteQty') {
                    CreateRequest::$roles_array = [
                        'booking_po_product_id'             => 'required',
                        'consider_parent_delivery_note_qty' => 'required',
                    ];
                }

                if ($function_name == 'productList') {
                    CreateRequest::$roles_array = [
                        'booking_id' => 'required',
                    ];
                }

                if ($function_name == "manageVariations") {
                    CreateRequest::$roles_array = [
                        // 'booking_id' => 'required',
                        'product_id' => 'required',
                    ];
                }

                if ($function_name == "removeProduct") {
                    CreateRequest::$roles_array = [
                        'booking_po_product_id' => 'required',
                    ];
                }

                if($function_name == 'storeDescrepancy'){
                    CreateRequest::$roles_array = [
                        'booking_po_product_id' => 'required',
                    ];
                }

                if($function_name == 'deletDescrepancy'){
                    CreateRequest::$roles_array = [
                        'delete_id' => 'required',
                        'booking_po_product_id' => 'required',
                    ];
                }    
            }
        }
    }

    public
            function setProductWeb(CreateRequest $request) {
        try {

            $response = $this->saveProduct($request);

            if (empty($response->original)) {
                return $this->sendValidation(array('Unable to save record, please try again'), 422);
            }

            if ($response->original['status'] == false) {
                $errors = array('Unable to save record, please try again');

                if (!empty($response->original['errors'])) {
                    $errors = $response->original['errors'];
                }
                return $this->sendValidation($errors, 422);
            }

            if (empty($response->original['data']['booking_po_product_id'])) {
                return $this->sendValidation(array('Unable to save record, please try again'), 422);
            }

            $booking_po_product_id = $response->original['data']['booking_po_product_id'];

            $request->request->add(['booking_po_product_id' => $booking_po_product_id]);

            $on_error_data =  !empty($booking_po_product_id) ? array('booking_po_product_id' => $booking_po_product_id)  : array();

            $response = $this->saveProductCaseDetails($request);

            if (empty($response->original)) {
                return $this->sendValidation(array('Unable to save record, please try again'), 422, $on_error_data);
            }

            if ($response->original['status'] == false) {
                $errors = array('Unable to save record, please try again');

                if (!empty($response->original['errors'])) {
                    $errors = $response->original['errors'];
                }
                return $this->sendValidation($errors, 422, $on_error_data);
            }

            $response = $this->saveProductComment($request);

            if (empty($response->original)) {
                return $this->sendValidation(array('Unable to save record, please try again'), 422, $on_error_data);
            }

            if ($response->original['status'] == false) {
                $errors = array('Unable to save record, please try again');

                if (!empty($response->original['errors'])) {
                    $errors = $response->original['errors'];
                }
                return $this->sendValidation($errors, 422, $on_error_data);
            }

            if (!empty($request->var_sku_id)) {
                $response = $this->saveProductVariations($request);

                if (empty($response->original)) {
                    return $this->sendValidation(array('Unable to save record, please try again', $on_error_data), 422);
                }

                if ($response->original['status'] == false) {
                    $errors = array('Unable to save record, please try again');

                    if (!empty($response->original['errors'])) {
                        $errors = $response->original['errors'];
                    }
                    return $this->sendValidation($errors, 422, $on_error_data);
                }
            }

            $data['booking_po_product_id'] = $booking_po_product_id;

            return $this->sendResponse('Record saved successfully', 200, $data);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function pendingProductCount(CreateRequest $request) {
        try {
            $db_total_product_count = BookingPO::bookingProductCount($request->booking_id);

            if (!empty($db_total_product_count)) {
                $total_product_count = !empty($db_total_product_count['product_count']) ? $db_total_product_count['product_count'] : 0;

                if (!empty($total_product_count)) {

                    if ($temp_pending_product_details = BookingPOProducts::pendingProductDetails($request->booking_id)) {
                        $total_completed_product_count = 0;

                        $pending_product_details = $this->get_pending_detail_array($temp_pending_product_details);

                        if (!empty($pending_product_details)) {
                            foreach ($pending_product_details as $pending_product_detail) {
                                if ($pending_product_detail['difference'] == $pending_product_detail['total_discrepancy_managed']) {
                                    $total_completed_product_count++;
                                }
                            }
                        }

                        $total_pending_count = ($total_product_count - $total_completed_product_count);

                        $response_array = array(
                            'total_product_count'           => $total_product_count,
                            'total_completed_product_count' => $total_completed_product_count,
                            'total_pending_count'           => ($total_pending_count < 0) ? 0 : $total_pending_count,
                        );

                        return $this->sendResponse('Booking Pending Product Count', 200, $response_array);
                    }
                }
                else {
                    return $this->sendValidation(array('No product found with this booking id'), 422);
                }
            }
            else {
                return $this->sendValidation(array('No product found with this booking id'), 422);
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function validateCaseData(CreateRequest $request, $booking_po_products_details, $warehouse_id, $case_detail_ids) {

        $result = array(
            'status' => true,
            'msg'    => "",
        );
        
        $location_where_array['status'] = 1;
        
        $location_where_array['site_id'] = $warehouse_id;

        $total_qty_received = 0;
        
        $this->CASE_LOCATION_DETAILS = array();

        $location_array = array();
        
        $location_details = array();

        $find_barcode_id = array();
        
        $check_availability = array();
        
        if (!empty($request->inner_outer_case_detail)) {
            foreach ($request->inner_outer_case_detail as $inner_outer_case_detail) {
                $outer_box_qty     = 0;
                $outer_box         = 0; 
                $outer_box_loc     = 0; 

                $inner_box_qty     = 0;
                $inner_box         = 0; 
                $inner_box_loc = 0;

                if (!empty($inner_outer_case_detail['outer'])) {
                    
                    $putaway_started = false;

                    if(!empty($inner_outer_case_detail['outer']['id']))
                    {
                        if(!empty($case_detail_ids[$inner_outer_case_detail['outer']['id']]))
                        {
                            if($case_detail_ids[$inner_outer_case_detail['outer']['id']]['put_away_started'] == 1)
                            {
                                $putaway_started = true;
                            }  

                            if($putaway_started == false && $case_detail_ids[$inner_outer_case_detail['outer']['id']]['inner_cases'])
                            {
                                $inner_details = $case_detail_ids[$inner_outer_case_detail['outer']['id']]['inner_cases'];
                                
                                if($inner_details[0]['put_away_started'] == 1)
                                {
                                    $putaway_started = true;
                                }    
                            }    
                        }    
                    }
                    
                    $find_barcode_id[] =  $inner_outer_case_detail['outer']['barcode'];

                    // validate qty per case for outer and inner
                    if(!empty($inner_outer_case_detail['outer']['qty_per_box']) 
                        && !empty($inner_outer_case_detail['inner']['qty_per_box'])
                        && is_numeric($inner_outer_case_detail['inner']['qty_per_box'])
                    )
                    {
                        if(
                            ($inner_outer_case_detail['outer']['qty_per_box'] < $inner_outer_case_detail['inner']['qty_per_box'])
                            || ($inner_outer_case_detail['outer']['qty_per_box'] % $inner_outer_case_detail['inner']['qty_per_box'] != 0)
                        )
                        {
                            $result = array(
                                'status' => false,
                                'msg'    => "Outer quantity per case should be greater than Inner quantity per case and Inner quantity per case should be divisible by Outer quantity per case.",
                            );

                            break(1);
                        }    
                    }    

                    // Validation if inner case is entered, it is mandatory to add outer details
                    if(
                        !empty($inner_outer_case_detail['inner']['barcode'])
                        || !empty($inner_outer_case_detail['inner']['qty_per_box'])
                        || !empty($inner_outer_case_detail['inner']['no_of_box'])
                     )
                     {
                       if(
                        empty($inner_outer_case_detail['outer']['barcode'])
                        || empty($inner_outer_case_detail['outer']['qty_per_box'])
                        )
                       {
                            $result = array(
                                'status' => false,
                                'msg'    => "Outer case fields are mandatory, if you choose to store only Inner case data.",
                            );

                            break(1);
                       }
                     }   

                    if ($inner_outer_case_detail['outer']['is_include_count'] == 1) {
                        
                        if(
                            empty($inner_outer_case_detail['outer']['barcode'])
                            || empty($inner_outer_case_detail['outer']['qty_per_box'])
                            || empty($inner_outer_case_detail['outer']['no_of_box'])
                            || !is_numeric($inner_outer_case_detail['outer']['qty_per_box'])
                            || !is_numeric($inner_outer_case_detail['outer']['no_of_box'])
                        )
                        {
                            $result = array(
                                'status' => false,
                                'msg'    => 'Outer case fields are mandatory with valid input values, if you choose to set "Is Include In Count" to Yes.',
                            );

                            break(1);
                        }


                        $outer_box = $inner_outer_case_detail['outer']['no_of_box'];

                        $outer_box_qty = $inner_outer_case_detail['outer']['qty_per_box'] * $inner_outer_case_detail['outer']['no_of_box'];
                        
                        // Booking PO total received qty
                        // $total_qty_received = $total_qty_received + $outer_box_qty;
                    
                        if (!empty($inner_outer_case_detail['outer']['location'])) {
                            foreach ($inner_outer_case_detail['outer']['location'] as $location_key => $locations) {
                                
                                if(!empty($locations))
                                {
                                    $location_array[] = $locations;

                                    if($putaway_started == false && !in_array($locations, $check_availability))
                                    {
                                        $check_availability[] = $locations;
                                    }

                                    $outer_box_loc = $outer_box_loc + $inner_outer_case_detail['outer']['qty'][$location_key];
                                }    
                            }
                        }
                    }    
                }

                if ($outer_box != $outer_box_loc) {
                    $result = array(
                        'status' => false,
                        'msg'    => "Total outer box and count of location wise split outer boxes does not match.",
                    );

                    break(1);
                }

                if (!empty($inner_outer_case_detail['inner'])) {
                    
                    $find_barcode_id[] =  $inner_outer_case_detail['inner']['barcode'];

                    if ($inner_outer_case_detail['inner']['is_include_count'] == 1) {
                        
                        if(
                            empty($inner_outer_case_detail['inner']['barcode'])
                            || empty($inner_outer_case_detail['inner']['qty_per_box'])
                            || empty($inner_outer_case_detail['inner']['no_of_box'])
                            || !is_numeric($inner_outer_case_detail['inner']['qty_per_box'])
                            || !is_numeric($inner_outer_case_detail['inner']['no_of_box'])
                        )
                        {
                            $result = array(
                                'status' => false,
                                'msg'    => 'Inner case fields are mandatory with valid input values, if you choose to set "Is Include In Count" to Yes.',
                            );

                            break(1);
                        }

                        $inner_box = $inner_outer_case_detail['inner']['no_of_box'];

                        $inner_box_qty = $inner_outer_case_detail['inner']['qty_per_box'] * $inner_box;

                        // Booking PO total received qty
                        // $total_qty_received = $total_qty_received + $inner_box_qty;
                    

                        if (!empty($inner_outer_case_detail['inner']['location'])) {
                            foreach ($inner_outer_case_detail['inner']['location'] as $location_key => $locations) {
                                
                                if(!empty($locations))
                                {    
                                    $location_array[] = $locations;

                                    if($putaway_started == false && !in_array($locations, $check_availability))
                                    {
                                        $check_availability[] = $locations;
                                    }

                                    $inner_box_loc = $inner_box_loc + $inner_outer_case_detail['inner']['qty'][$location_key];
                                }    
                            }
                        }
                    }
                }
                
                if ($inner_box != $inner_box_loc) {
                    $result = array(
                        'status' => false,
                        'msg'    => "Total inner box and count of location wise split inner boxes does not match.",
                    );

                    break(1);
                }
            }
        }

        if (!empty($request->inner_outer_case_detail['loose']) && $result['status'] == true) {

            foreach ($request->inner_outer_case_detail['loose'] as $loose) {
                
                $find_barcode_id[] = $loose['barcode'];

                $loose_box_qty = 0;

                $loose_box_loc_qty = 0;

                $loose_box_qty = $loose['qty_per_box'];

                $putaway_started = false;

                if(!empty($loose['id']))
                {    
                    if(!empty($case_detail_ids[$loose['id']]))
                    {
                        if($case_detail_ids[$loose['id']]['put_away_started'] == 1)
                        {
                            $putaway_started = true;
                        }  
                    }    
                }

                // Booking PO total received qty
                // $total_qty_received = $total_qty_received + $loose_box_qty;

                if (!empty($loose['location'])) {
                    foreach ($loose['location'] as $location_key => $locations) {
                        
                        if(!empty($locations))
                        {    
                            $location_array[] = $locations;
                            
                            if($putaway_started == false && !in_array($locations, $check_availability))
                            {
                                $check_availability[] = $locations;
                            }

                            $loose_box_loc_qty = $loose_box_loc_qty + $loose['qty'][$location_key];
                        }    
                    }
                }

                if ($loose_box_qty != $loose_box_loc_qty) {
                    $result = array(
                        'status' => false,
                        'msg'    => "Actual single case quantity and location wise split quantity does not match.",
                    );

                    break(1);
                }
            }
        }

        // VALIDATE UNIQUE BARCODE
        if(!empty($find_barcode_id) && $result['status'] == true)
        {
            $product_type = 'normal';

            $check_barcode_product_ids = array();
            
            $product_details = array();

            if(!empty($booking_po_products_details->product))
            {
                $product_details = $booking_po_products_details->product->toArray();
                
                $product_type = $product_details['product_type'];
            }   

            if($product_type == 'normal' && !empty($product_details))
            {
                $check_barcode_product_ids = array($product_details['id']);
            }
            elseif(!empty($product_details))
            {
                if($product_type == 'parent')
                {
                    $parent_id  = $product_details['id'];
                }
                elseif($product_type == 'variation')
                {
                    $parent_id  = $product_details['parent_id'];
                }    
                
                if(!empty($parent_id))
                {
                    $check_barcode_product_ids[] = $parent_id;
                    
                    $variations_ids = Products::where('parent_id', $parent_id)->pluck('id')->toArray();
                    
                    if(!empty($variations_ids))
                    {
                        $check_barcode_product_ids = array_merge($check_barcode_product_ids, $variations_ids);
                    }   
                }    
            }    

            if(!empty($check_barcode_product_ids))
            {    
                $find_barcode_id  = array_unique($find_barcode_id);

                $barcode_exist_for_other_products = ProductBarcode::whereIn('barcode',$find_barcode_id)
                                  ->whereNotIn('product_id',$check_barcode_product_ids)
                                  ->pluck('barcode','id')
                                  ->toArray();
                
                if(!empty($barcode_exist_for_other_products))
                {   
                    $barcode_exist_for_other_products  = array_unique(array_map( "strtolower", $barcode_exist_for_other_products ));

                    $barcode_string = implode(', ', $barcode_exist_for_other_products);

                    $result = array(
                            'status' => false,
                            'msg'    => $barcode_string.", these barcode(s) are reserved for other products.",
                        );                  
                }
            }                      
        }

        if (!empty($location_array) && $result['status'] == true) {
            $location_array = array_unique($location_array);

            $case_location_details = Locations::select('locations_master.*');

            $case_location_details->whereIn('location', $location_array);
            
            $case_location_details->where('locations_master.status', '1');

            $case_location_details->where('locations_master.site_id', $warehouse_id);

            $case_location_details = $case_location_details->get()->toArray();
            
            if(!empty($case_location_details))
            {   
                foreach($case_location_details as $case_location_detail)
                {    
                    $this->CASE_LOCATION_DETAILS[$case_location_detail['location']] = $case_location_detail;
                    
                    if(in_array($case_location_detail['location'], $check_availability) 
                        && in_array($case_location_detail['type_of_location'], array(3,4))
                    )
                    {    
                        $location_details[$case_location_detail['id']] = $case_location_detail;
                    }
                }
            }    

            if (count($this->CASE_LOCATION_DETAILS) != count($location_array)) {
                
                $found_locations = array_keys($this->CASE_LOCATION_DETAILS);
                
                $not_in_warehouse_locations = array_diff($location_array,$found_locations);

                $not_in_warehouse_locations_str = implode(', ', $not_in_warehouse_locations);

                $result = array(
                    'status' => false,
                    'msg'    => $not_in_warehouse_locations_str.", location(s) does not belong to current warehouse",
                );
            }
        }

        if(!empty($location_details) && $result['status'] == true)
        {
            $check_occuppied_locations = array_keys($location_details);
           
            $location_assing_obj = LocationAssign::select('locations_master.*');
            
            $location_assing_obj->join('locations_master',function($join){ 
                $join->on('locations_assign.location_id','=', 'locations_master.id');
                $join->whereIn('locations_master.type_of_location',array(3,4));
            });

            $location_assing_obj->whereIn('locations_master.id',$check_occuppied_locations);
            
            $location_assing_obj->where('locations_assign.booking_id', '!=', $request->booking_id);

            $pallet_occupied = $location_assing_obj->pluck('locations_master.location')->toArray();
            
            if(!empty($pallet_occupied))
            {
                $pallet_occupied = array_unique($pallet_occupied);

                $pallet_occupied_str = implode(', ', $pallet_occupied);

                $result = array(
                    'status' => false,
                    'msg'    => $pallet_occupied_str.", these location(s) are not available.",
                );   
            }    
        }

        // if($result['status'] == true)
        // {    
        //     $where_raw_string = 'booking_po_product_case_details.is_without_case_location = 0';

        //     $result = $this->validatePutaway($this->LOCATION_ASSIGN_ARRAY, $request->booking_po_product_id, $where_raw_string);
        // }

        return $result;
    }

    // public function validatePutaway($location_assing_array, $booking_po_product_id, $location_where_raw_string = "")
    // {
    //     $result = array(
    //         'status' => true,
    //         'msg'    => "",
    //     );

    //     $booking_locations = BookingPOProducts::getBookingProductLocations($booking_po_product_id,$location_where_raw_string);

    //     if(!empty($booking_locations))
    //     {
    //         foreach($booking_locations as $booking_location_details)
    //         {
    //             $location_id = $booking_location_details['location_id'];

    //             $barcode = $booking_location_details['barcode'];
                
    //             $best_before_date = $booking_location_details['best_before_date'];

    //             if(empty($best_before_date))
    //             {
    //                 $best_before_date = 'no-best-before';
    //             }    

    //             $location_wise_existing_qty = !empty($this->LOCATION_PUTAWAY_QTY['location_wise'][$booking_location_details['location_id']]) ? $this->LOCATION_PUTAWAY_QTY['location_wise'][$booking_location_details['location_id']] : 0;

    //             $this->LOCATION_PUTAWAY_QTY['location_wise'][$location_id] = (int) $location_wise_existing_qty + (int) $booking_location_details['put_away_qty'];

    //             $location_barcode_wise_existing_qty = !empty($this->LOCATION_PUTAWAY_QTY['location_barcode_wise'][$location_id][$barcode][$best_before_date]) ? $this->LOCATION_PUTAWAY_QTY['location_barcode_wise'][$location_id][$barcode][$best_before_date] : 0;

    //             $this->LOCATION_PUTAWAY_QTY['location_barcode_wise'][$location_id][$barcode][$best_before_date] = (int) $location_barcode_wise_existing_qty + (int) $booking_location_details['put_away_qty'];
    //         }    
    //     }    

    //     $putaway_records = $this->LOCATION_PUTAWAY_QTY['location_wise'];
        
    //     if(!empty($putaway_records))
    //     {
    //         foreach($putaway_records as $location_id => $putaway_qty)
    //         {
    //             if(isset($location_assing_array[$location_id]))
    //             {
    //                 if((int)$location_assing_array[$location_id] < (int)$putaway_qty)
    //                 {
    //                     $result = array(
    //                         'status' => false,
    //                         'msg'    => 'Quantity received on '.$this->CASE_LOCATION_DETAILS['id'][$location_id]['location'].' should be greated than put away quantity i.e '.$putaway_qty,
    //                     );

    //                     break(1);
    //                 }
    //                 else
    //                 {
    //                     unset($putaway_records[$location_id]);
    //                 }    
    //             }    
    //         }

    //         if(!empty($putaway_records) && $result['status'] == true)
    //         {
    //             $set_error = false;

    //             $msg = 'There must be below mention quantities on respective pallet(s) ';

    //             $location_ids = array_keys($putaway_records);

    //             $location_details = Locations::whereIn('id', $location_ids)->pluck('location','id')->toArray();

    //             foreach($putaway_records as $location_id => $putaway_qty)
    //             {
    //                 if($putaway_qty > 0)
    //                 {
    //                     $set_error = true;

    //                     $msg .= $location_details[$location_id].' : '.$putaway_qty;
    //                 }
    //             }   

    //             if($set_error == true)
    //             {    
    //                 $result = array(
    //                         'status' => false,
    //                         'msg'    => $msg,
    //                     );                
    //             }    
    //         }    
    //     } 
        
    //     return $result;   
    // }

    // public function setCaseGlobalVariables($location_details, $quantity, $case_details, $case_type,$loc_key, $is_best_before_date)
    // {

    //     $pallet_qty = $quantity;

    //     $type_of_location = $location_details['type_of_location'];
    //     $location_id = $location_details['id'];

    //     if(isset($this->AUTO_DISCREPANCY_LOCATION[$type_of_location]))
    //     {
    //         $pallet_qty = $this->AUTO_DISCREPANCY_LOCATION[$type_of_location] + $pallet_qty;
    //     }

    //     $this->AUTO_DISCREPANCY_LOCATION[$type_of_location] = $pallet_qty;


    //     $pallet_qty_for_loc = $quantity;

    //     if(isset($this->LOCATION_ASSIGN_ARRAY[$location_id]))
    //     {
    //         $pallet_qty_for_loc = $this->LOCATION_ASSIGN_ARRAY[$location_id] + $pallet_qty_for_loc;
    //     }

    //     $this->LOCATION_ASSIGN_ARRAY[$location_id] = $pallet_qty_for_loc;

    //     $best_before_date = 'no-best-before';

    //     if (!empty($case_details['best_before_date'][$loc_key]) && !empty($is_best_before_date)) {
    //         $best_before_date = db_date($case_details['best_before_date'][$loc_key]);
    //     }

    //     $location_best_before_qty = $quantity;  

    //     if(isset($this->LOCATION_ASSIGN_ARRAY_BARCODE[$location_id][$case_details['barcode']][$best_before_date]))
    //     {
    //         $location_best_before_qty = $this->LOCATION_ASSIGN_ARRAY_BARCODE[$location_id][$case_details['barcode']][$best_before_date]['qty'] + $location_best_before_qty;
    //     }
    //     else
    //     {
    //         $this->LOCATION_ASSIGN_ARRAY_BARCODE['find_barcode_ids'][] = $case_details['barcode']; 
    //     }    

    //     $this->LOCATION_ASSIGN_ARRAY_BARCODE[$location_id][$case_details['barcode']][$best_before_date] = array(
    //                         'qty_per_box' => ($case_type == 3) ? $case_details['qty_per_box'] : 0,
    //                         'total_boxes' => !empty($case_details['no_of_box']) ? $case_details['no_of_box'] : 1,
    //                         'qty' => $location_best_before_qty,
    //                         'case_type' => $case_type,
    //                      );
    // }

    public
            function get_pending_detail_array($temp_pending_product_details) {
        $pending_product_details = array();

        if (!empty($temp_pending_product_details)) {
            foreach ($temp_pending_product_details as $temp_pending_product) {
                if (!isset($pending_product_details[$temp_pending_product['product_id']])) {
                    $pending_product_details[$temp_pending_product['product_id']] = $temp_pending_product;

                    $discrepancy_details = $pending_product_details[$temp_pending_product['product_id']]['booking_p_o_discrepancy'];

                    $pending_product_details[$temp_pending_product['product_id']]['booking_p_o_discrepancy'] = array();

                    $pending_product_details[$temp_pending_product['product_id']]['total_discrepancy_managed'] = 0;

                    if (!empty($discrepancy_details)) {
                        foreach ($discrepancy_details as $discrepancy_detail) {
                            $pending_product_details[$temp_pending_product['product_id']]['total_discrepancy_managed'] = $pending_product_details[$temp_pending_product['product_id']]['total_discrepancy_managed'] + (int) $discrepancy_detail['qty_discrepancy_by_type'];

                            $pending_product_details[$temp_pending_product['product_id']]['booking_p_o_discrepancy'][$discrepancy_detail['discrepancy_type']] = $discrepancy_detail;
                        }
                    }
                }
                else {

                    // Add Difference
                    $pending_product_details[$temp_pending_product['product_id']]['difference'] = $temp_pending_product['difference'] + $pending_product_details[$temp_pending_product['product_id']]['difference'];

                    if (!empty($temp_pending_product['booking_p_o_discrepancy'])) {

                        foreach ($temp_pending_product['booking_p_o_discrepancy'] as $booking_p_o_discrepancy) {
                            $pending_product_details[$temp_pending_product['product_id']]['total_discrepancy_managed'] = $pending_product_details[$temp_pending_product['product_id']]['total_discrepancy_managed'] + (int) $booking_p_o_discrepancy['qty_discrepancy_by_type'];

                            if (!isset($pending_product_details[$temp_pending_product['product_id']]['booking_p_o_discrepancy'][$booking_p_o_discrepancy['discrepancy_type']])) {
                                $pending_product_details[$temp_pending_product['product_id']]['booking_p_o_discrepancy'][$booking_p_o_discrepancy['discrepancy_type']] = $booking_p_o_discrepancy;
                            }
                            else {
                                $pending_product_details[$temp_pending_product['product_id']]['booking_p_o_discrepancy'][$booking_p_o_discrepancy['discrepancy_type']]['qty_discrepancy_by_type'] = (int) $pending_product_details[$temp_pending_product['product_id']]['booking_p_o_discrepancy'][$booking_p_o_discrepancy['discrepancy_type']]['qty_discrepancy_by_type'] + (int) $booking_p_o_discrepancy['qty_discrepancy_by_type'];
                            }
                        }
                    }
                }
            }
        }

        return $pending_product_details;
    }

    public
            function saveProduct(CreateRequest $request) {
        try {

            $booking_details = Booking::find($request->booking_id);
            
            // COMMENTED DUE TO HIDING LOCATION INPUT BOX IN VIEW
            // $location_details = array();

            // VALIDATE BARCODE
            if(!empty($request->product_id))
            {
                $inv_product_details = Products::find($request->product_id);
                
                if(!empty($inv_product_details) && !empty($request->barcode))
                {
                    if($inv_product_details->product_type == 'parent')
                    {
                        $parent_id = $inv_product_details->id;
                    }
                    elseif($inv_product_details->product_type == 'variation')
                    {
                        $parent_id = $inv_product_details->parent_id;
                    } 
                    
                    if(!empty($parent_id))                     
                    {
                        $check_barcode_product_ids[] = $parent_id;

                        $variations_ids = Products::where('parent_id', $parent_id)->pluck('id')->toArray();

                        if(!empty($variations_ids))
                        {
                            $check_barcode_product_ids =  array_merge($check_barcode_product_ids, $variations_ids);
                        }   
                    }
                    else
                    {
                        $check_barcode_product_ids = array($inv_product_details->id);
                    } 

                    if(!empty($check_barcode_product_ids))
                    {

                        $barcode_exist_for_other_products = ProductBarcode::where('barcode',$request->barcode)
                              ->whereNotIn('product_id',$check_barcode_product_ids)
                              ->pluck('barcode','id')
                              ->toArray();
                        
                        if(!empty($barcode_exist_for_other_products))
                        {   
                            $barcode_exist_for_other_products  = array_unique(array_map( "strtolower", $barcode_exist_for_other_products ));

                            $barcode_string = implode(', ', $barcode_exist_for_other_products);

                            $msg = $barcode_string.", these barcode is reserved for other product.";

                            return $this->sendValidation(array($msg), 422);
                        }
                    }  
                }    
            }    

            // DECLAIRE DEFAULT VARIABLES
            $booking_po_products_details = array();

            $po_product_details = array();
            
            $booking_product_status = "";

            // GET PO PRODUCT ORDERED QTY, QTY FOR SCANNED VARIATION PRODUCT WILL NOT BE ACHIVED FROM HERE. SO ORDRE_QTY WILL BE ALWAYS 0 IF PRODUCT TYPE IS VARIATION.
            if(!empty($request->po_product_id))
            {
                $po_product_details = \App\PurchaseOrderProduct::find($request->po_product_id);
                
                if(!empty($po_product_details))
                {
                    $ordered_qty = $po_product_details->total_quantity;
                }    
            }    

            // CREATE DB ARRAY
            $db_array['id'] = !empty($request->booking_po_product_id) ? $request->booking_po_product_id : '';

            $db_array['booking_id']        = $booking_details->id;
            $db_array['product_id']        = !empty($request->product_id) ? $request->product_id : NULL;

            if(!empty($po_product_details))
            {    
                $db_array['po_id']             =  $po_product_details->po_id;
                
                $db_array['po_product_id']     =  $po_product_details->id;
            }

            $db_array['barcode']           = !empty($request->barcode) ? $request->barcode : '';
            
            $db_array['delivery_note_qty'] = !empty($request->delivery_note_qty) ? $request->delivery_note_qty : NULL;

            $db_array['scan_by_user_id']   = $request->user()->id;
            
            $db_array['scan_date']         = date('Y-m-d H:i:s');
            
            $db_array['is_photobooth']     = !empty($request->is_photobooth) ? $request->is_photobooth : 0;

            if (isset($request->is_variant)) {
                $db_array['is_variant'] = $request->is_variant;
            }

            if(empty($db_array['id'])) 
            {
                // SET START DATE FOR BOOKING
                $booking_product_exist = BookingPOProducts::select('id')->where(array('booking_id' => $db_array['booking_id']))->first();

                if (empty($booking_product_exist)) 
                {
                    $booking_details->start_date = date('Y-m-d H:i:s');

                    $booking_details->status = "4";

                    // SET BOOKING ARRIVED DATE
                    if (empty($booking_details->arrived_date)) {
                        $booking_details->arrived_date = date('Y-m-d H:i:s');
                    }

                    $booking_details->save();

                    $po_ids = $booking_details->bookingPOs->pluck('po_id')->toArray();

                    // IF PO IS NOT MARKED AS COMPLETE THAN SET PO STATUS AS ARRIVED
                    if (!empty($po_ids) && $booking_details->status != 6) {
                        $db_update_po['po_status'] = '7';

                        PurchaseOrder::whereIn('id', $po_ids)->update($db_update_po);
                    }
                }

                // INSERT BOOKING PRODUCT
                $db_array['status']      = 0;
                $db_array['created_by']  = $request->user()->id;
                $db_array['modified_by'] = $request->user()->id;
                $q_result                = $booking_po_product_id   = BookingPOProducts::create($db_array)->id;
                
                if(!empty($db_array['po_product_id']) && !empty($po_product_details))
                {
                    $po_product_details->is_editable = 1;
                    $po_product_details->save();   
                }    
            }
            else 
            {
                // UPDATE BOOKING PRODUCT
                $booking_po_products_details = BookingPOProducts::find($db_array['id']);

                $db_array['modified_by'] = $request->user()->id;

                // DELETE VARIATIONS, if no qty is received for them.
                if (isset($db_array['is_variant']) && $db_array['is_variant'] == 0) {
                    $variation_ids = BookingPOProducts::where('parent_id', $booking_po_products_details->id)->pluck('id')->toArray();
                    
                    $variation_has_cases = BookingPOProductCaseDetails::selectRaw('count(*) as cases_count')->whereIn('booking_po_product_id',$variation_ids)->first()->toArray();
                    
                    if($variation_has_cases['cases_count'] > 0)
                    {    
                        $msg = "You cannot set variants to No, onces you have received variant product's quantity.";

                        return $this->sendValidation(array($msg), 422);
                    }
                    else{
                        BookingPOProducts::whereIn('id',$variation_ids)->delete();
                    }
                }

                $q_result = BookingPOProducts::where('id', $booking_po_products_details->id)->update($db_array);

                // CHECK IF PRODUCT WAS VARIATION IF YES SET PARENT PRODUCT DATA
                if (!empty($booking_po_products_details->parent_id)) {
                    $booking_product_status = BookingPOProducts::setParentData($booking_po_products_details->parent_id);
                }

                $booking_po_product_id = $booking_po_products_details->id;
            }

            // UPDATE INVENTORY DATA
            if (!empty($db_array['product_id'])) {
                // Array to create barcode in Product Modules
                $product_barcode_create[$db_array['barcode']] = array(
                                        'barcode_type' => '1',
                                    );

                if (!empty($product_barcode_create)) {
                    // saving barcodes to product
                    ProductBarcode::materialReceiptAddBarcodes($db_array['product_id'], $product_barcode_create, $request);
                }
            }

            if (!empty($q_result)) {
                $resp_msg                      = "Record saved successfully";
                $data['booking_po_product_id'] = $booking_po_product_id;
                return $this->sendResponse($resp_msg, 200, $data);
            }
            else {
                return $this->sendValidation(array('Unable to save record, please try again'), 422);
            }


        }
        catch (Exception $ex) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    // public
    //         function make_product_complete($booking_po_product_id) {
    //     $booking_details       = BookingPOProducts::where('id', $booking_po_product_id)->get();
    //     $booking_details_array = $booking_details->toArray();
    //     if (!empty($booking_details) && !empty($booking_details_array)) {
    //         if (!empty($booking_details_array[0]['consider_parent_delivery_note_qty'])) {
    //             $difference           = $booking_details_array[0]['qty_received'] - $booking_details_array[0]['delivery_note_qty'];
    //             $descripency_over_qty = $this->get_discrepency_data($booking_po_product_id, 2);
    //             $descripency_less_qty = $this->get_discrepency_data($booking_po_product_id, '');
    //             $diff_desc            = $descripency_over_qty + (-$descripency_less_qty);
    //             if ($diff_desc == $difference) {
    //                 $upd['difference'] = $difference;
    //                 $upd['status']     = 1;
    //                 BookingPOProducts::where('id', $booking_po_product_id)->update($upd);
    //             }
    //             else {
    //                 $upd['difference'] = $difference;
    //                 $upd['status']     = 0;
    //                 BookingPOProducts::where('id', $booking_po_product_id)->update($upd);
    //             }
    //         }
    //         else {
    //             $difference           = $booking_details_array[0]['difference'];
    //             $descripency_over_qty = $this->get_discrepency_data($booking_po_product_id, 2);
    //             $descripency_less_qty = $this->get_discrepency_data($booking_po_product_id, '');
    //             $diff_desc            = $descripency_over_qty + (-$descripency_less_qty);

    //             if ($diff_desc == $difference) {
    //                 $upd['status'] = 1;
    //                 BookingPOProducts::where('id', $booking_po_product_id)->update($upd);
    //             }
    //             else {
    //                 $upd['status'] = 0;
    //                 BookingPOProducts::where('id', $booking_po_product_id)->update($upd);
    //             }

    //             //make parent product completed or not completed
    //             if (!empty($booking_details_array[0]['parent_id'])) {
    //                 //check if
    //                 $booking_details_all_child = BookingPOProducts::where('parent_id', $booking_details_array[0]['parent_id'])->get();
    //                 $not_completed             = 0;
    //                 if (!empty($booking_details_all_child) && !empty($booking_details_all_child->toArray())) {
    //                     foreach ($booking_details_all_child as $row) {
    //                         if (empty($row->status)) {
    //                             $not_completed = 1;
    //                         }
    //                     }
    //                 }

    //                 if (!empty($not_completed)) {
    //                     $data['status'] = 0;
    //                     BookingPOProducts::where('id', $booking_details_array[0]['parent_id'])->update($data);
    //                 }
    //                 else {
    //                     $data['status'] = 1;
    //                     BookingPOProducts::where('id', $booking_details_array[0]['parent_id'])->update($data);
    //                 }
    //             }
    //         }
    //     }
    // }

    public
            function get_discrepency_data($booking_po_product_id, $type) {
        $total_discrepancy = 0;
        $total_descr       = array();
        if (!empty($type)) {
            $total_descr = BookingPODiscrepancy::where('booking_po_products_id', $booking_po_product_id)->where('discrepancy_type', $type)->get();
        }
        else {
            $total_descr = BookingPODiscrepancy::where('booking_po_products_id', $booking_po_product_id)->where('discrepancy_type', '!=', '2')->get();
        }

        if (!empty($total_descr) && $total_descr->toArray()) {
            foreach ($total_descr as $row) {
                $total_discrepancy = $total_discrepancy + $row->qty;
            }
        }

        return $total_discrepancy;
    }

    public
            function saveProductCaseDetails(CreateRequest $request) {
        try 
        {
            $where_array = [
                'id' => $request->booking_po_product_id,
            ];

            $booking_po_products_details = BookingPOProducts::select('*')->where($where_array)->first();

            $booking_details = Booking::find($booking_po_products_details->booking_id);

            if (!empty($booking_po_products_details) && !empty($booking_details)) {

                if (@$booking_po_products_details->product->product_type == 'parent')
                {
                    $request->is_inner_outer_case = 0;
                }

                if ($request->is_inner_outer_case == 0) {
                    $booking_po_products_details->outerCaseDetails()->where('is_without_case_location', '0')->delete();
                }

                if (@$booking_po_products_details->product->product_type == 'parent')
                {
                    $booking_po_products_details->is_inner_outer_case = 0;
                    $booking_po_products_details->save();
                    return $this->sendResponse("Case details saved successfully", 200);
                }    

                $case_detail_ids = $booking_po_products_details->outerCaseDetails()->with('innerCases')->get()->keyBy('id')->toArray();

                if($request->is_inner_outer_case == 1)
                {    
                    $valid = $this->validateCaseData($request, $booking_po_products_details, $booking_details->warehouse_id, $case_detail_ids);

                    if ($valid['status'] == false) {
                        return $this->sendValidation(array($valid['msg']), 422);
                    }
                }
                
                
                DB::beginTransaction();

                // Booking PO total received qty
                $total_qty_received = 0;

                $product_barcode_create = array();
                
                $remove_case_locations = array();

                $booking_product_status = "";

                $this->AUTO_DISCREPANCY_LOCATION = array();
                
                $this->LOCATION_ASSIGN_ARRAY = array();

                $this->LOCATION_ASSIGN_ARRAY_BARCODE  = array();

                $this->LOCATION_ASSIGN_NOT_CHECK_B_LOCATION_IDS = array();

                if ($request->is_inner_outer_case == '1' && (!empty($request->inner_outer_case_detail) || !empty($request->loose_case_detail)))
                {
                    $booking_po_product_id = $booking_po_products_details->id;

                    if (!empty($request->remove_case_details)) {
                        BookingPOProductCaseDetails::whereIn('id', $request->remove_case_details)->delete();
                    }

                    // inner outer case details
                    if (count($request->inner_outer_case_detail) > 0) {
                        // $this->DB_LOCATION_UPDATE = array();

                        // $this->DB_LOCATION_INSERT = array();

                        foreach ($request->inner_outer_case_detail as $case_key => $inner_outer_case_detail) {
                            if (!empty($inner_outer_case_detail['outer'])) {
                                $outer_case_id = "";

                                $db_array_outer = array();

                                $db_array_inner = array();

                                $putaway_started = false;
                                
                                if(!empty($inner_outer_case_detail['outer']['id']))
                                {
                                    $db_case_detail = $case_detail_ids[$inner_outer_case_detail['outer']['id']];
                                    
                                    if($db_case_detail['put_away_started'] == 1)
                                    {
                                        $putaway_started = true;
                                    }
                                    elseif(!empty($db_case_detail['inner_cases'][0]) && $db_case_detail['inner_cases'][0]['put_away_started'] == 1)
                                    {
                                        $putaway_started = true; 
                                    }    
                                }    

                                // save outer 
                                if(!empty($inner_outer_case_detail['outer']['barcode'])) 
                                {
                                    $db_array_outer['booking_po_product_id'] = $booking_po_product_id;

                                    $db_array_outer['id'] = !empty($inner_outer_case_detail['outer']['id']) ? $inner_outer_case_detail['outer']['id'] : '';

                                    $db_array_outer['barcode'] = $inner_outer_case_detail['outer']['barcode'];

                                    $db_array_outer['is_include_count'] = !empty($inner_outer_case_detail['outer']['is_include_count']) ? $inner_outer_case_detail['outer']['is_include_count'] : 0;

                                    $db_array_outer['qty_per_box'] = !empty($inner_outer_case_detail['outer']['qty_per_box']) ? $inner_outer_case_detail['outer']['qty_per_box'] : 0;

                                    $db_array_outer['no_of_box'] = !empty($inner_outer_case_detail['outer']['no_of_box']) ? $inner_outer_case_detail['outer']['no_of_box'] : 0;

                                    $outer_total = NULL;

                                    if ($db_array_outer['is_include_count'] == 1) {
                                        $outer_total = $db_array_outer['qty_per_box'] * $db_array_outer['no_of_box'];
                                    }
                                    
                                    if ($inner_outer_case_detail['inner']['is_include_count'] == 1) {
                                        
                                        if(!empty($inner_outer_case_detail['inner']['qty_per_box']) 
                                            && !empty($inner_outer_case_detail['inner']['no_of_box'])
                                        )
                                        {    

                                            $inner_qty_per_box = $inner_outer_case_detail['inner']['qty_per_box'];

                                            $inner_no_of_box = $inner_outer_case_detail['inner']['no_of_box'];

                                            $inner_qty = $inner_no_of_box * $inner_qty_per_box;

                                            $outer_total = $outer_total + $inner_qty;
                                        }
                                    }

                                    // Booking PO total received qty
                                    $total_qty_received = $total_qty_received + $outer_total;

                                    $db_array_outer['total'] = $outer_total;

                                    $db_array_outer['parent_outer_id'] = NULL;

                                    $db_array_outer['case_type'] = 3;

                                    if (!empty($db_array_outer['id'])) {
                                        $outer_case_id = $db_array_outer['id'];

                                        $db_array_outer['modified_by'] = $request->user()->id;

                                        // if put away is not started
                                        if($putaway_started === false)
                                        {
                                            BookingPOProductCaseDetails::where('id', $db_array_outer['id'])->update($db_array_outer);
                                        }    

                                        unset($case_detail_ids[$outer_case_id]);
                                    }
                                    else 
                                    {
                                        $db_array_outer['created_by'] = $request->user()->id;

                                        $db_array_outer['modified_by'] = $request->user()->id;

                                        $outer_case_id = BookingPOProductCaseDetails::create($db_array_outer)->id;
                                    }

                                    // save locations
                                    if (!empty($inner_outer_case_detail['outer']['qty']) && $db_array_outer['is_include_count'] == 1) {
                                        
                                        $this->createCaseLocationArray(
                                                $request,
                                                $inner_outer_case_detail['outer'],
                                                $outer_case_id,
                                                'outer',
                                                $booking_po_products_details,
                                                $putaway_started
                                            );
                                    }
                                    elseif(!empty($db_array_outer['id']) && $putaway_started === false)
                                    {
                                        $remove_case_locations[] = $db_array_outer['id'];
                                    }
                                }

                                // save inner
                                if (!empty($inner_outer_case_detail['inner']['barcode']) && !empty($outer_case_id)) {
                                    $db_array_inner['booking_po_product_id'] = $booking_po_product_id;

                                    $db_array_inner['id'] = !empty($inner_outer_case_detail['inner']['id']) ? $inner_outer_case_detail['inner']['id'] : '';

                                    $db_array_inner['barcode'] = $inner_outer_case_detail['inner']['barcode'];

                                    $db_array_inner['is_include_count'] = !empty($inner_outer_case_detail['inner']['is_include_count']) ? $inner_outer_case_detail['inner']['is_include_count'] : 0;

                                    $db_array_inner['qty_per_box'] = !empty($inner_outer_case_detail['inner']['qty_per_box']) ? $inner_outer_case_detail['inner']['qty_per_box'] : 0;

                                    $db_array_inner['no_of_box'] = !empty($inner_outer_case_detail['inner']['no_of_box']) ? $inner_outer_case_detail['inner']['no_of_box'] : 0;

                                    $db_array_inner['total'] = NULL;

                                    $db_array_inner['parent_outer_id'] = $outer_case_id;

                                    $db_array_inner['case_type'] = 2;

                                    if (!empty($db_array_inner['id'])) {
                                        $inner_case_id = $db_array_inner['id'];

                                        $db_array_inner['modified_by'] = $request->user()->id;

                                        // if put away is not started
                                        if($putaway_started === false)
                                        {
                                            BookingPOProductCaseDetails::where('id', $db_array_inner['id'])->update($db_array_inner);
                                        }    

                                        unset($case_detail_ids[$inner_case_id]);
                                    }
                                    else 
                                    {
                                        $db_array_inner['created_by'] = $request->user()->id;

                                        $db_array_inner['modified_by'] = $request->user()->id;

                                        $inner_case_id = BookingPOProductCaseDetails::create($db_array_inner)->id;
                                    }

                                    // save locations
                                    if (!empty($inner_outer_case_detail['inner']['qty']) && $db_array_inner['is_include_count'] == 1) {
                                        $this->createCaseLocationArray(
                                            $request,
                                            $inner_outer_case_detail['inner'],
                                            $inner_case_id,
                                            'inner',
                                            $booking_po_products_details,
                                            $putaway_started
                                        );
                                    }
                                    elseif(!empty($db_array_inner['id']) && $putaway_started === false)
                                    {
                                        $remove_case_locations[] = $db_array_inner['id'];
                                    }
                                }

                                if (!empty($db_array_outer['barcode']) && $putaway_started === false) {

                                    // Array to create barcode in Product Modules
                                    $product_barcode_create[$db_array_outer['barcode']] = array(
                                        'barcode_type'      => '3',
                                        'outer_barcode_qty' => $db_array_outer['qty_per_box'],
                                        'inner_barcode'     => @$db_array_inner['barcode'],
                                        'inner_barcode_qty' => @$db_array_inner['qty_per_box'],
                                    );
                                }
                                
                            }
                        }

                        // loose case details
                        if (!empty($request->inner_outer_case_detail['loose'])) {

                            foreach ($request->inner_outer_case_detail['loose'] as $loose_key => $loose_case_details) {

                                $putaway_started = false;

                                if(!empty($loose_case_details['id']))
                                {    
                                    if($case_detail_ids[$loose_case_details['id']]['put_away_started'] == 1)
                                    {
                                        $putaway_started = true;
                                    }
                                }    

                                // If putaway is not started
                                if(!empty($loose_case_details['barcode']))
                                {
                                    $db_array_loose['booking_po_product_id'] = $booking_po_product_id;

                                    $db_array_loose['id'] = !empty($loose_case_details['id']) ? $loose_case_details['id'] : '';

                                    $db_array_loose['barcode'] = $loose_case_details['barcode'];

                                    $db_array_loose['is_include_count'] = 1;

                                    $db_array_loose['qty_per_box'] = !empty($loose_case_details['qty_per_box']) ? $loose_case_details['qty_per_box'] : 0;

                                    $db_array_loose['no_of_box'] = 1;

                                    $db_array_loose['total'] = $db_array_loose['qty_per_box'];

                                    $db_array_loose['parent_outer_id'] = NULL;

                                    $db_array_loose['case_type'] = 1;

                                    // Booking PO total received qty
                                    $total_qty_received = $total_qty_received + $db_array_loose['total'];

                                    if (!empty($db_array_loose['id'])) {
                                        $loose_case_id = $db_array_loose['id'];

                                        $db_array_loose['modified_by'] = $request->user()->id;

                                        if($putaway_started === false)
                                        {    
                                            BookingPOProductCaseDetails::where('id', $db_array_loose['id'])->update($db_array_loose);
                                        }
                                        unset($case_detail_ids[$loose_case_id]);
                                    }
                                    else {
                                        $db_array_loose['created_by'] = $request->user()->id;

                                        $db_array_loose['modified_by'] = $request->user()->id;

                                        $loose_case_id = BookingPOProductCaseDetails::create($db_array_loose)->id;
                                    }
                                    
                                    // save locations
                                    if (!empty($loose_case_details['qty'])) {
                                        $this->createCaseLocationArray(
                                            $request,
                                            $loose_case_details,
                                            $loose_case_id,
                                            'loose',
                                            $booking_po_products_details,
                                            $putaway_started
                                        );
                                    }

                                    // check only if putaway is not started
                                    if($putaway_started === false)
                                    {   
                                        // Array to create barcode in Product Modules 
                                        $product_barcode_create[$loose_case_details['barcode']] = array(
                                            'barcode_type' => '1',
                                        );
                                    }
                                }
                                elseif(!empty($loose_case_details['id']) && !empty($loose_case_details['barcode']))
                                {
                                    unset($case_detail_ids[$loose_case_details['id']]);
                                }    
                            }
                        }

                        // Location Insert Update Query
                        // if (!empty($this->DB_LOCATION_INSERT)) {
                        //     BookingPOProductLocation::insert($this->DB_LOCATION_INSERT);
                        // }

                        // if (!empty($this->DB_LOCATION_UPDATE)) {
                        //     Batch::update(new BookingPOProductLocation, $this->DB_LOCATION_UPDATE, 'id');
                        // }

                        if (!empty($case_detail_ids)) {
                            $case_ids = array_keys($case_detail_ids);
                            BookingPOProductCaseDetails::whereIn('id', $case_ids)->delete();
                        }

                        if(!empty($remove_case_locations) 
                            || !empty($request->remove_case_locations)
                        ) {
                            
                            $case_loc_obj = new BookingPOProductLocation;

                            $case_loc_obj::where(function($q) use ($remove_case_locations, $request){
                                if(!empty($remove_case_locations))
                                {

                                    $q->whereIn('case_detail_id', $remove_case_locations);
                                }    

                                if(!empty($request->remove_case_locations))
                                {
                                    $q->orWhereIn('id', $request->remove_case_locations);  
                                }    
  
                            })->delete();
                        }    
                    }
                }

                // Add qty one if photoboth is set
                if ($booking_po_products_details->is_photobooth == '1' && $total_qty_received > 0) {
                    $total_qty_received = $total_qty_received + 1;
                }

                $booking_po_products_details->qty_received = $total_qty_received;

                // IF PRODUCT IS NORMAL
                if(empty($booking_po_products_details->product) && $booking_po_products_details->return_to_supplier == 1)
                {
                    $booking_po_products_details->difference = $booking_po_products_details->qty_received;
                }    
                elseif($booking_po_products_details->product->product_type == 'normal' || empty($booking_po_products_details->product->product_type))
                {    
                    $po_product_details = $booking_po_products_details->getPOProductDetails;    
                    
                    if(!empty($po_product_details))
                    {
                        $booking_po_products_details->difference = $total_qty_received - $po_product_details->total_quantity;
                    }   
                }
                else
                {
                    $booking_po_products_details->difference = 0;
                }
                
                $booking_po_products_details->pick_pallet_qty = 0;
        
                $booking_po_products_details->bulk_pallet_qty = 0;
                
                $booking_po_products_details->onhold_pallet_qty = 0;
                
                $booking_po_products_details->quarantine_pallet_qty = 0;
            
                $booking_po_products_details->return_to_supplier_pallet_qty = 0;

                if(!empty($this->AUTO_DISCREPANCY_LOCATION[3]))
                {
                    $booking_po_products_details->pick_pallet_qty = $this->AUTO_DISCREPANCY_LOCATION[3];
                }
                
                if(!empty($this->AUTO_DISCREPANCY_LOCATION[4]))
                {
                    $booking_po_products_details->bulk_pallet_qty = $this->AUTO_DISCREPANCY_LOCATION[4];
                }
                
                if(!empty($this->AUTO_DISCREPANCY_LOCATION[9]))
                {
                    $booking_po_products_details->onhold_pallet_qty = $this->AUTO_DISCREPANCY_LOCATION[9];
                }
                
                if(!empty($this->AUTO_DISCREPANCY_LOCATION[8]))
                {
                    $booking_po_products_details->quarantine_pallet_qty = $this->AUTO_DISCREPANCY_LOCATION[8];
                } 
                
                if(!empty($this->AUTO_DISCREPANCY_LOCATION[10]))
                {
                    $booking_po_products_details->return_to_supplier_pallet_qty = $this->AUTO_DISCREPANCY_LOCATION[10];
                }
                

                $booking_po_products_details->is_inner_outer_case = !empty($request->is_inner_outer_case) ? $request->is_inner_outer_case : 0;

                $booking_po_products_details->is_best_before_date = (!empty($request->is_best_before_date) && $request->is_inner_outer_case == '1') ? $request->is_best_before_date : 0;

                $booking_po_products_details->save();

                // Set parent data
                if (!empty($booking_po_products_details->parent_id)) {
                    $booking_product_status = BookingPOProducts::setParentData($booking_po_products_details->parent_id);
                }
                else
                {
                    
                    if(!empty($po_product_details->total_quantity)
                        && $booking_po_products_details->return_to_supplier == 0
                    )
                    {    
                        $booking_po_products_details_array = $booking_po_products_details->toArray();

                        $auto_discrepancy_array['booking_product_details'] = $booking_po_products_details_array;
                        
                        $auto_discrepancy_array['location_array'] = $this->AUTO_DISCREPANCY_LOCATION;
                        
                        $auto_discrepancy_array['qty_ordered'] = $po_product_details->total_quantity;

                        $booking_product_status = BookingPOProducts::manageAutoDiscrepancies($auto_discrepancy_array);
                    }    
                }

                if ($booking_product_status == 1 
                    && $booking_po_products_details->return_to_supplier == 0
                ) {
                    Booking::setComplete($booking_po_products_details->booking_id, true, $booking_details);
                }

                if (!empty($product_barcode_create) && !empty($booking_po_products_details->product_id)) {
                    // saving barcodes to product
                    ProductBarcode::materialReceiptAddBarcodes($booking_po_products_details->product_id, $product_barcode_create, $request);
                }
                
                if ($booking_po_products_details->return_to_supplier == 0) {
                    $data['location_details'] = $this->LOCATION_ASSIGN_ARRAY;

                    $data['location_barcodes'] = $this->LOCATION_ASSIGN_ARRAY_BARCODE;
                    
                    $data['booking_location_not_check'] = $this->LOCATION_ASSIGN_NOT_CHECK_B_LOCATION_IDS;
                    
                    $data['product_id']   = $booking_po_products_details->product_id;
                    
                    $data['booking_id']   = $booking_po_products_details->booking_id;
                    
                    $data['po_id']        = $booking_po_products_details->po_id;
                    
                    $data['user_id']      = $request->user()->id;

                    $data['warehouse_id'] = $booking_details->warehouse_id;
                    
                    $data['booking_po_products_details'] = !empty($booking_po_products_details) ? $booking_po_products_details : array();

                    LocationAssign::manageMaterialReceiptQty($data);

                    // UPDATE INVENTORY DATA. CODE IS ADDED AFTER HIDING OF LOCATION COLUMN
                    if(!empty($po_product_details->unit_price))
                    {    
                        $product_master_update['last_cost_price'] = $po_product_details->unit_price;
                    }

                    $product_master_update['last_stock_receipt_date'] = date('Y-m-d H:i:s');

                    $product_master_update['last_stock_receipt_qty']  = $booking_po_products_details->qty_received;

                    // saving data to product
                    Products::where('id', $booking_po_products_details->product_id)->update($product_master_update);
                }

                DB::commit();

                return $this->sendResponse("Case details saved successfully", 200);
            }
            else
            {
                return $this->sendValidation(array("Product details with booking_po_product_id is not found."), 422);    
            }
        }
        catch (Exception $ex) {

            DB::rollBack();

            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    public
            function createCaseLocationArray(CreateRequest $request, $case_details, $case_detail_id, $case_type, $booking_product_details, $putaway_started) {
                
        foreach ($case_details['qty'] as $loc_key => $loc_qty) {

            if (!empty($loc_qty) && !empty($case_details['location'][$loc_key])) {

                $boxes = 1;
                
                if($case_type == 'outer')
                {
                  $boxes = $loc_qty;
                    
                  $loc_qty = $boxes * $case_details['qty_per_box'];
                    
                  $case_type_loc_assig = 3;
                }
                elseif ($case_type == 'inner') {
                    $boxes = $loc_qty;
                    
                    $loc_qty = $boxes * $case_details['qty_per_box'];
                    
                    $case_type_loc_assig = 2;
                }
                elseif ($case_type == 'loose') {
                    $case_type_loc_assig = 1;
                }    

                $db_loc_array['id'] = !empty($case_details['location_id'][$loc_key]) ? $case_details['location_id'][$loc_key] : '';

                $db_loc_array['case_detail_id'] = $case_detail_id;

                $db_loc_array['qty'] = $loc_qty;
                
                $db_loc_array['boxes'] = $boxes;

                $db_loc_array['booking_po_product_id'] = $booking_product_details->id;

                $best_before_date = NULL;

                if (!empty($case_details['best_before_date'][$loc_key]) && !empty($request->is_best_before_date)) {
                    $best_before_date = db_date($case_details['best_before_date'][$loc_key]);
                }

                $db_loc_array['best_before_date'] = $best_before_date;

                $location_id = NULL;

                $location_string = trim($case_details['location'][$loc_key]);
                
                if (!empty($this->CASE_LOCATION_DETAILS[$location_string])) {
                    $location_details = $this->CASE_LOCATION_DETAILS[$location_string];

                    $location_id = $location_details['id'];
                }

                $db_loc_array['location_id'] = $location_id;

                if (!empty($db_loc_array['id'])) {
                    $db_loc_array['modified_by'] = $request->user()->id;
                    
                    if($putaway_started === false)
                    {
                        // $this->DB_LOCATION_UPDATE[] = $db_loc_array;
                        BookingPOProductLocation::where('id',$db_loc_array['id'])->update($db_loc_array);
                    }
                }
                elseif($putaway_started === false) 
                {
                    $db_loc_array['created_by'] = $request->user()->id;

                    $db_loc_array['modified_by'] = $request->user()->id;

                    // $this->DB_LOCATION_INSERT[] = $db_loc_array;
                    $db_loc_array['id'] = BookingPOProductLocation::create($db_loc_array)->id;
                }
                
                // ARRAY FOR AUTO DESCREPANCY
                if(!empty($db_loc_array['location_id']) && !empty($db_loc_array['id']))
                {
                    $pallet_qty = $db_loc_array['qty'];

                    $type_of_location = $location_details['type_of_location'];

                    if(isset($this->AUTO_DISCREPANCY_LOCATION[$type_of_location]))
                    {
                        $pallet_qty = $this->AUTO_DISCREPANCY_LOCATION[$type_of_location] + $pallet_qty;
                    }

                    $this->AUTO_DISCREPANCY_LOCATION[$type_of_location] = $pallet_qty;

                    // ARRAY FOR LOCATION ASSING
                    $pallet_qty_for_loc = $db_loc_array['qty'];
                    
                    if(isset($this->LOCATION_ASSIGN_ARRAY[$db_loc_array['location_id']]))
                    {
                        $pallet_qty_for_loc = $this->LOCATION_ASSIGN_ARRAY[$db_loc_array['location_id']] + $pallet_qty_for_loc;
                    }

                    $this->LOCATION_ASSIGN_ARRAY[$db_loc_array['location_id']] = $pallet_qty_for_loc;

                    // ARRAY FOR LOCATION TRANS
                    $location_best_before_qty = $db_loc_array['qty'];  

                    if(isset($this->LOCATION_ASSIGN_ARRAY_BARCODE[$db_loc_array['location_id']][$db_loc_array['id']]))
                    {
                        $location_best_before_qty = $this->LOCATION_ASSIGN_ARRAY_BARCODE[$db_loc_array['location_id']][$db_loc_array['id']]['qty'] + $location_best_before_qty;
                    }
                    else
                    {
                        $this->LOCATION_ASSIGN_ARRAY_BARCODE['find_barcode_ids'][] = $case_details['barcode']; 
                    }    

                    $this->LOCATION_ASSIGN_ARRAY_BARCODE[$db_loc_array['location_id']][$db_loc_array['id']] = array(
                                'booking_po_product_id' => $db_loc_array['booking_po_product_id'],
                                'booking_po_case_detail_id' => $db_loc_array['case_detail_id'],
                                'barcode' => $case_details['barcode'],
                                'best_before_date' => $db_loc_array['best_before_date'],
                                'qty_per_box' => ($case_type_loc_assig == 1) ? '1' : $case_details['qty_per_box'],
                                'total_boxes' => ($case_type_loc_assig == 1) ? $location_best_before_qty : $db_loc_array['boxes'],
                                'qty' => $location_best_before_qty,
                                'case_type' => $case_type_loc_assig,
                            );

                    if($putaway_started == true && !empty($db_loc_array['id']))
                    {
                        $this->LOCATION_ASSIGN_NOT_CHECK_B_LOCATION_IDS[] = $db_loc_array['id'];
                    }    
                }    
            }
        }
    }

    public
            function saveProductComment(CreateRequest $request) {
        try {

            $db_array = array(
                'comments' => $request->comments,
            );

            BookingPOProducts::where('id', $request->booking_po_product_id)->update($db_array);

            return $this->sendResponse("Comment saved successfully", 200);
        }
        catch (Exception $e) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    public
            function SendEmail(Request $request) {
        if (!empty($request->id)) {
            $booking_data          = new Booking;
            $supplier_contact_data = $booking_data->supplier_contact_data($request->id);
            if (!empty($supplier_contact_data)) {
                foreach ($supplier_contact_data as $supplier_data) {
                    $supplier_name  = $supplier_data->name;
                    $supplier_email = $supplier_data->email;
                    $emailData      = array('toName' => $supplier_name, 'toEmail' => $supplier_email, 'subject' => 'Booking Material Receipt', 'template' => 'emails.booking_material_receipt', 'id' => $request->id);
                    $result         = event(new SendMail($emailData)); // send mail to user for welcome
                    return $this->sendResponse('Mail send successfully to supplier contact', 200);
                }
            }
            else {
                return $this->sendError('No supplier contact found, please try again', 422);
            }
        }
        else {
            return $this->sendError('Mail send failed, please try again', 422);
        }
    }

    public
            function actionMany(Request $request) {
        // $ids    = $request->ids;
        // $status = $request->status;

        // if (BookingPODiscrepancy::whereIn('id', explode(",", $ids))->update(['status' => $status])) {
        //     return $this->sendResponse(trans('messages.api_responses.status_action_success'), 200);
        // }
        // else {
        //     return $this->sendError(trans('messages.api_responses.status_action_error'), 422);
        // }
    }

    public
            function addDescrepancy(Request $request) {
        $newcounter = $request->ids;
        $html       = '';
        $html       .= '<tr class="add_desc_tr_' . $newcounter . '">';
        $html       .= '<td><input type="textbox" class="form-control numeric_only desc_itm_qty" id="add_desc_qty_' . $newcounter . '" name="add_desc_qty[]" title="Qty"></td>';
        $html       .= '<td>';
        $html       .= '<select class="form-control desc_type" name="add_desc_type[]" id="add_desc_type_' . $newcounter . '">';
        foreach (config('params.discrepancy_type') as $key => $value) {
            if($key != 2)
            {    
                $html .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }

        $html .= '</select>';
        $html .= '</td>';
        // $html.='<td><input class="form-control" type="file" id="add_desc_image_'.$newcounter.'" name="add_desc_image[]"></td>';

        $html .= '<td>
            <div class="fancy-file wrap-label">
                <input type="file" name="add_desc_image_' . $newcounter . '[]" id="add_desc_image_' . $newcounter . '" class="inputfile-custom" multiple="" data-multiple-caption="{count} files selected" accept="image/*"/>
                <label for="add_desc_image_' . $newcounter . '"><span></span> <strong>' . trans("messages.common.choose_files") . '</strong></label>
            </div>
        </td>';

        $html .= '<td><a class="btn-delete" href="javascript:void(0);" onclick="added_desc_tr_delete(' . $newcounter . ');" title="delete"><span class="icon-moon icon-Delete"></span></a></td>';
        $html .= '</tr>';
        return $html;
    }

    public
            function storeDescrepancy(CreateRequest $request) {
        try {
            
            $product_type = "normal";

            $qty_ordered = 0;
            
            $booking_product_details = BookingPOProducts::find($request->booking_po_product_id);
            
            if($booking_product_details->return_to_supplier == 0)
            {    
                $inventory_details = $booking_product_details->product;

                if(!empty($inventory_details))
                {    
                    if($inventory_details->product_type == 'variation')
                    {
                        return $this->sendValidation(array('You cannot add descripancy on variation product.'), 422);
                    }
                    else
                    {
                        $product_type = $inventory_details->product_type;            
                    }
                }
            }    

            $booking_desc          = new BookingPODiscrepancy;
            
            $booking_po_product_id = !empty($request->booking_po_product_id) ? $request->booking_po_product_id : '';
            
            $product_id            = !empty($request->product_id) ? $request->product_id : NULL;
            
            //add process_only
            if (isset($request->add_desc_qty)) {
                $add_desc_qty  = !empty($request->add_desc_qty) ? $request->add_desc_qty : '';
                $add_desc_type = !empty($request->add_desc_type) ? $request->add_desc_type : '';

                $insert_array = array();
                if (!empty($add_desc_qty)) {
                    $i = 0;
                    $j = 1;

                    // IF DESCRIPANCY TYPE IS Damaged, Not fit for sale, Fright damaged, TAKE ACTION HAS RETURN TO SUPPLIER.
                    foreach ($add_desc_qty as $row) {
                        
                        $insert_array = array(
                            'booking_po_products_id' => $booking_po_product_id,
                            'product_id'             => $product_id,
                            'discrepancy_type'       => $add_desc_type[$i],
                            'qty'                    => $row,
                            'created_by'             => $request->user->id,
                            'created_at'             => date('Y-m-d H:i:s'),
                            'status'                 => in_array($add_desc_type[$i], array(3,4,7)) ? '4' : '0',
                            'is_added_by_system'                 => in_array($add_desc_type[$i], array(3,4,7)) ? '2' : '0',
                        );

                        if (!empty($insert_array)) {
                            
                            if($add_desc_type[$i] != '2')
                            {
                                $booking_desc_id   = $booking_desc->create($insert_array)->id;
                            }
                                
                            $ans               = $this->saveAttachments($request, '1', $j, $booking_po_product_id, $booking_desc_id);
                            unset($insert_array);
                        }
                        
                        $i++;
                        $j++;
                    }
                }
            }

            //deleted record
            if (isset($request->deleted_id)) {
                $deleted_id = !empty($request->deleted_id) ? $request->deleted_id : '';
                if (!empty($deleted_id)) {
                    if (BookingPODiscrepancy::whereIn('id', explode(",", $deleted_id))->delete()) {
                    }
                }
            }

            //update process_only
            if (isset($request->update_desc_id)) {
                $update_desc_qty  = !empty($request->update_desc_qty) ? $request->update_desc_qty : '';
                $update_desc_type = !empty($request->update_desc_type) ? $request->update_desc_type : '';
                $update_id        = !empty($request->update_desc_id) ? $request->update_desc_id : '';

                $update_array = array();
                if (!empty($update_desc_qty)) {
                    $i = 0;
                    $j = 0;

                    // IF DESCRIPANCY TYPE IS Damaged, Not fit for sale, Fright damaged, TAKE ACTION HAS RETURN TO SUPPLIER.
                    foreach ($update_desc_qty as $row) {
                        $update_array = array(
                            'id'                     => $update_id[$i],
                            'booking_po_products_id' => $booking_po_product_id,
                            'product_id'             => $product_id,
                            'discrepancy_type'       => $update_desc_type[$i],
                            'qty'                    => $row,
                            'modified_by'            => $request->user->id,
                            'updated_at'             => date('Y-m-d H:i:s'),
                            'status'                 => in_array($update_desc_type[$i], array(3,4,7)) ? '4' : '0',
                            'is_added_by_system'                 => in_array($update_desc_type[$i], array(3,4,7)) ? '2' : '0',
                        );

                        if (!empty($update_array)) {
                            if($update_desc_type[$i] != 2)
                            {
                                // $booking_desc->save($update_array);
                                BookingPODiscrepancy::where('id', $update_id[$i])->update($update_array);
                            }

                            $ans               = $this->saveAttachments($request, '2', $j, $booking_po_product_id, $update_id[$i]);
                            
                            unset($update_array);
                        }
                        
                        $i++;
                        $j++;
                    }
                }
            }

            // MANAGE AUTO DESCRIPANCY
            $booking_po_product_id = array();

            $photobooth_qty = 0;

            $po_product_details = $booking_product_details->getPOProductDetails;
            
            if(!empty($po_product_details))
            {    
                $qty_ordered = $po_product_details->total_quantity;
            }

            if($product_type == 'normal' || empty($product_type))
            {
                $booking_po_product_id = array($booking_product_details->id);
            }
            else
            {
                $booking_variations_details = BookingPOProducts::where('parent_id', $booking_product_details->id)->get()->toArray();
                
                if(!empty($booking_variations_details))
                {
                    foreach($booking_variations_details as $variation_details)
                    {
                        $booking_po_product_id[] = $variation_details['id'];

                        if($variation_details['is_photobooth'] == 1)
                        {
                            $photobooth_qty = $photobooth_qty + 1;
                        }    
                    }    
                }
            }   

            $auto_discrepancy_array['booking_product_details'] = $booking_product_details->toArray();

            $auto_discrepancy_array['qty_ordered'] = $qty_ordered;
            
            $auto_discrepancy_array['location_array'] = $booking_product_details->bookingProductLocationTypeQty($booking_po_product_id);

            if($photobooth_qty > 0)
            {
                $auto_discrepancy_array['location_array']['photobooth_qty'] = $photobooth_qty;
            }    

            $booking_product_status = BookingPOProducts::manageAutoDiscrepancies($auto_discrepancy_array);

            // SET BOOKING COMPLETE
            if ($booking_product_status == 1) {
                Booking::setComplete($booking_product_details->booking_id, true);
            }    

            return $this->sendResponse('Record saved successfully', 200);
        }
        catch (Exception $ex) {
            return $this->sendError('Unable to save record, please try again', 422);
        }
    }

    public
            function saveAttachments(Request $request, $type, $id, $booking_po_product_id, $record_id) {
        $i = 0;
        if ($type == 1) { //add
            $files = $request->file('add_desc_image_' . $id);
        }
        else { //edit
            $files = $request->file('update_desc_image_' . $record_id);
        }
        //dd($files);

        if (!empty($files)) {
            foreach ($files as $file) {
                $storeImagearray[$i]['book_pur_desc_id'] = $record_id;
                $uploadedFile                            = $file;
                $folder                                  = $baseFolder                              = "booking/discrepancy/" . $booking_po_product_id . "/" . $record_id;
                $imageExtension                          = ['jpg', 'JPG', 'JPEG', 'jpeg', 'png', "PNG"];
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }
                Storage::makeDirectory($folder, 0777, true);
                $extension = strtolower($file->getClientOriginalExtension());
                $name      = time() . $i . 'Image.' . $file->getClientOriginalExtension();
                $path      = Storage::putFileAs(($folder), $uploadedFile, $name);
                if (!empty($path) && in_array($extension, $imageExtension)) {
                    $folder = $folder . '/thumbnail/';
                    if (!Storage::exists($folder)) {
                        Storage::makeDirectory($folder, 0777, true);
                    }

                    $thumbName1   = explode('/', $path);
                    $thumbName    = $thumbName1[4];
                    $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $baseFolder . '/' . $thumbName;
                    $thumbPath    = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $folder . $thumbName;
                    Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                        $constraint->upsize();
                        $constraint->aspectRatio();
                    })->save($thumbPath, 100);

                    $storeImagearray[$i]['image'] = $path;
                }
                else {
                    $storeImagearray[$i]['image'] = $path;
                }

                $i++;
            }

            if (count($storeImagearray) > 0) {
                if (BookingPurchaseOrdersDiscrepancyImage::insert($storeImagearray)) {
                    return 1;
                }
            }
            else {
                return 0;
            }
        }
        else {
            return 0;
        }
    }

    public
            function viewDescrepancy(Request $request) {
        $booking_po_product_id = !empty($request->booking_po_product_id) ? $request->booking_po_product_id : '';
        $po_desc       = new BookingPODiscrepancy;
        $desc_list     = $po_desc->get_product_desc_data($booking_po_product_id);
        $discrepancy_type = config('params.discrepancy_type');
        
        // REMOVE DISCREPANCY TYPE OVER FROM DROPDOWN
        unset($discrepancy_type[2]);
        
        $html          = '';
        if (!empty($desc_list) && !empty($desc_list->toArray())) {
            foreach ($desc_list as $row) {
                $disable = "";
                if($row->discrepancy_type == 2)
                {
                    $disable = 'readonly="readonly"';
                }    

                $html .= '<tr class="update_desc_tr_' . $row->id . '">';
                $html .= '<input type="hidden" name="update_desc_id[]" value="' . $row->id . '">';
                $html .= '<td><input '.$disable.'  type="textbox" class="form-control numeric_only desc_itm_qty" id="update_desc_qty_' . $row->id . '" name="update_desc_qty[]" title="Qty" value="' . $row->qty . '"></td>';
                $html .= '<td>';

                if(empty($disable))
                {    
                    $html .= '<select class="form-control desc_type" name="update_desc_type[]" id="update_desc_type_' . $row->id . '">';
                    foreach ($discrepancy_type as $key => $value) {
                        if ($key == $row->discrepancy_type) {
                            $html .= '<option value="' . $key . '" selected>' . $value . '</option>';
                        }
                        else {
                            $html .= '<option value="' . $key . '">' . $value . '</option>';
                        }
                    }

                    $html .= '</select>';
                }
                else
                {
                    $html .= '<select class="form-control desc_type" name="update_desc_type[]" id="update_desc_type_' . $row->id . '" readonly>';

                    $html .= '<option value="2" selected>Over</option>';

                    $html .= '</select>';
                }   

                $html .= '</td>';
                $html .= '<td>';
                $html .= '<div class="d-flex">';
                if (!empty($row->desc_image_url) && !empty($row->desc_image_id)) {
                    $html           .= '<div class="flex-one d-flex flex-wrap desc_imag_main_' . $row->id . '">';
                    $desc_image_url = explode('||', $row->desc_image_url);
                    $desc_image_ids = explode('||', $row->desc_image_id);
                    if (!empty($desc_image_url)) {
                        $k = 0;
                        foreach ($desc_image_url as $row1) {
                            $link_array  = explode('/', $row1);
                            $last_part   = end($link_array);
                            $prelink     = str_replace($last_part, '', $row1);
                            $thumb_link  = $prelink . 'thumbnail/' . $last_part;
                            $parent_link = '';
                            $child_link  = '';
                            if (!is_null($thumb_link) && file_exists(Storage::path($thumb_link))) {
                                $child_link  = $thumb_link;
                                $parent_link = $row1;
                            }
                            else {
                                $child_link  = $row1;
                                $parent_link = $row1;
                            }

                            if (!is_null($parent_link) && file_exists(Storage::path($parent_link))) {
                                $html .= '<div class="descripancy-upload-img mr-1 mb-1 desc_parent_class_' . $row->id . ' desc_img_cl_' . $desc_image_ids[$k] . '">
                                    <a href="' . asset('storage/uploads/' . $parent_link) . '" data-rel="lightcase">
                                    <img src="' . asset('storage/uploads/' . $child_link) . '" width="35" />
                                    </a>
                                    <a href="javascript:void(0);" class="remove" onclick="delete_image_desc(' . $row->id . ',' . $desc_image_ids[$k] . ');">&times;</a>
                                </div>';
                            }
                            $k++;
                        }
                    }

                    $html .= '</div>';
                }

                $html .= '<div class="fancy-file wrap-label">
                            <input type="file" name="update_desc_image_' . $row->id . '[]" id="update_desc_image_' . $row->id . '" class="inputfile-custom" multiple="" data-multiple-caption="{count} files selected" accept="image/*"/>
                            <label for="update_desc_image_' . $row->id . '"><span></span> <strong>' . trans("messages.common.choose_files") . '</strong></label>
                        </div>';
                $html .= '</div>';
                $html .= '</td>';
                
                if(empty($disable))
                {    
                    $html .= '<td><a class="btn-delete" href="javascript:void(0);" onclick="updated_desc_tr_delete(' . $row->id . ');" title="delete"><span class="icon-moon icon-Delete"></span></a></td>';
                }

                $html .= '</tr>';
            }
        }
        return $html;
    }

    public
            function deletDescrepancyImage(Request $request) {
        $desc_image_id = $request->id;
        $image_data    = BookingPurchaseOrdersDiscrepancyImage::select('id', 'image')->where('id', $desc_image_id)->get();
        $delete_image  = 0;
        if (!empty($image_data) && !empty($image_data->toArray())) {
            foreach ($image_data as $row) {
                $link_array   = explode('/', $row->image);
                $last_part    = end($link_array);
                $prelink      = str_replace($last_part, '', $row->image);
                $thumb_link   = $prelink . 'thumbnail/' . $last_part;
                Storage::delete($thumb_link);
                Storage::delete($row->image);
                BookingPurchaseOrdersDiscrepancyImage::where('id', $row->id)->delete();
                $delete_image = 1;
            }
        }

        if (!empty($delete_image)) {
            return $this->sendResponse("Image has been deleted successfully", 200);
        }
        else {
            return $this->sendResponse("Image has not been deleted successfully", 422);
        }
    }

    public
            function setBookingArrivedDate(CreateRequest $request) {
        try {
            $db_array['arrived_date'] = db_date($request->arrived_date);
            
            if (Booking::where('id', $request->booking_id)->update($db_array)) {
                return $this->sendResponse("Arrived date saved successfully", 200);
            }
            else {
                return $this->sendValidation(array('Unable to save record, please try again'), 422);
            }
        }
        catch (Exception $e) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    //Delete Delivery Note Image author-kinjal
    public
            function removeDeliveryNoteImg(Request $request) {
        try {
            $bookingObj = Booking::find($request->id);
            Storage::delete($bookingObj->delivery_notes_picture);
            if (isset($bookingObj->delivery_notes_picture) && !empty($bookingObj->delivery_notes_picture)) {
                $thumbName = explode('/', $bookingObj->delivery_notes_picture)[1];
                Storage::delete('users/thumbnail/' . $thumbName);
            }
            if ($bookingObj->update(['delivery_notes_picture' => null])) {
                return $this->sendResponse(trans('messages.api_responses.delivery_note_img_delete_success'), 200);
            }
            else {
                return $this->sendResponse(trans('messages.api_responses.delivery_note_img_delete_error'), 422);
            }
        }
        catch (Exception $e) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    public
            function setBookingCompleted(CreateRequest $request) {
        try {
            
            $response = Booking::setComplete($request->booking_id);

            if (!empty($response)){
                
                if($response['status'] == true)
                {    
                    return $this->sendResponse($response['msg'], 200);
                }
                else
                {
                    return $this->sendValidation(array($response['msg']), 422);
                }
            }
            else {
                return $this->sendValidation(array('Unable to set booking as complete'), 422);
            }
        }
        catch (Exception $e) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    public
            function saveProductVariations(CreateRequest $request) 
    {
        try {
            $request_array = $request->input();

            $product_exist = Products::find($request->product_id);

            $variation_combinations = array();

            $booking_product_detail = BookingPOProducts::find($request->booking_po_product_id);

            if (!empty($product_exist) && !empty($booking_product_detail)) 
            {
                if (!empty($request->var_sku)) {
                    if ($product_exist->product_type == 'variation') {
                        return $this->sendValidation(array('Variation product cannot have sub variation products'), 422);
                    }

                    if ($product_exist->is_listed_on_magento != 0 && $product_exist->product_type == 'normal') {
                        return $this->sendValidation(array('Product cannot be set as variation, because it is already listed on magento'), 422);
                    }

                    $booking_product_putaway_details = $booking_product_detail->getVariationPutawayQty($booking_product_detail->id);

                    $product_putaway_start = $booking_product_putaway_details->pluck('product_id')->toArray();

                    if(!empty($product_putaway_start))
                    {   
                        foreach($product_putaway_start as $product_id)
                        {
                            if(!in_array($product_id, $request->var_sku_id))
                            {
                                return $this->sendValidation(array("You cannot remove product who's putaway is started"), 422);
                            }    
                        }    
                    }    
                    
                    $product_exist_array = object_to_array($product_exist->getOriginal());

                    $parent_product_id = $product_exist_array['id'];

                    unset($product_exist_array['id']);

                    unset($product_exist_array['sku']);

                    unset($product_exist_array['main_image_internal']);

                    unset($product_exist_array['main_image_internal_thumb']);

                    $variation_details = array();

                    if (!empty($request->var_id)) {
                        $db_variation_details = Products::select('id', 'main_image_internal', 'variation_theme_value1', 'variation_theme_value2')->whereIn('id', $request->var_id)->get();
                    }


                    if (!empty($db_variation_details)) {
                        foreach ($db_variation_details as $db_variation_detail) {
                            $variation_details[$db_variation_detail->id] = $db_variation_detail->getOriginal('main_image_internal');

                            $variation_combinations[] = trim($db_variation_detail->variation_theme_value1) . '|||' . trim($db_variation_detail->variation_theme_value2);
                        }
                    }

                    $valid = $this->validateVariationData($request, $variation_combinations);

                    if ($valid['status'] == false) {
                        return $this->sendValidation(array($valid['msg']), 422);
                    }

                    DB::beginTransaction();

                    $db_product_update = array();

                    foreach ($request->var_sku as $key => $v_sku) {

                        if (empty($request->var_id[$key]) && in_array($v_sku, $request->var_sku_id)
                        ) {
                            $db_post = array();

                            $db_post = $product_exist_array;

                            $db_post['parent_id'] = $parent_product_id;

                            $db_post['product_type'] = 'variation';

                            $db_post['variation_theme_id'] = $request->variation_theme;

                            $db_post['sku'] = $request->var_sku[$key];

                            $db_post['variation_theme_value1'] = !empty($request->var_size[$key]) ? $request->var_size[$key] : NULL;

                            $db_post['variation_theme_value2'] = !empty($request->var_color[$key]) ? $request->var_color[$key] : NULL;

                            $db_post['all_variants_place_one_location'] = !empty($request->all_variants_place_one_location) ? 1 : 0;

                            if (!empty($request->var_title[$key])) {
                                $db_post['title'] = $request->var_title[$key];
                            }

                            $db_post['created_by'] = $request->user()->id;

                            if (!empty($request->var_img[$key])) {

                                $upload_details = $this->upload_product_img($request->var_img[$key], $parent_product_id);

                                $db_post['main_image_internal'] = !empty($upload_details['image_path']) ? $upload_details['image_path'] : '';

                                $db_post['main_image_internal_thumb'] = !empty($upload_details['thumb_path']) ? $upload_details['thumb_path'] : '';
                            }

                            $var_product_id = Products::create($db_post)->id;

                            $sku_index = array_search($db_post['sku'], $request->var_sku_id);

                            $request_array['var_sku_id'][$sku_index] = $var_product_id;

                            $request->merge($request_array);
                        }
                        elseif (!empty($request->var_id[$key])) {
                            $db_post_update = array();

                            $varProductImage = $variation_details[$request->var_id[$key]];

                            $varProductImageArray = explode('/', $varProductImage);

                            $varProductImageName = end($varProductImageArray);

                            $varProductImageName = 'thumbnail/' . $varProductImageName;

                            array_pop($varProductImageArray);

                            array_push($varProductImageArray, $varProductImageName);

                            $varProductImageThumPath = implode('/', $varProductImageArray);

                            if (!empty($request->var_remove_product_image)) {
                                if (in_array($varProductImage, $request->var_remove_product_image)) {
                                    Storage::delete($varProductImage);

                                    Storage::delete($varProductImageThumPath);

                                    $db_post_update['main_image_internal'] = NULL;

                                    $db_post_update['main_image_internal_thumb'] = NULL;
                                }
                            }

                            if (!empty($request->var_img[$key])) {
                                if (!empty($varProductImage)) {
                                    Storage::delete($varProductImage);
                                    Storage::delete($varProductImageThumPath);
                                }

                                $upload_details = $this->upload_product_img($request->var_img[$key], $parent_product_id);

                                $db_post_update['main_image_internal'] = !empty($upload_details['image_path']) ? $upload_details['image_path'] : '';

                                $db_post_update['main_image_internal_thumb'] = !empty($upload_details['thumb_path']) ? $upload_details['thumb_path'] : '';
                            }

                            if (!empty($db_post_update)) {
                                $db_post_update['id'] = $request->var_id[$key];

                                $db_product_update[] = $db_post_update;
                            }
                        }
                    }

                    if (!empty($db_product_update)) {
                        $obj = new Products;

                        $result = Batch::update($obj, $db_product_update, 'id');
                    }

                    $product_exist->product_type = 'parent';

                    $product_exist->variation_theme_id = $request->variation_theme;

                    $product_exist->all_variants_place_one_location = !empty($request->all_variants_place_one_location) ? 1 : 0;

                    $product_exist->save();

                    if (!empty($request->var_sku_id)) 
                    {
                        $where_array['parent_id'] = $booking_product_detail->id;

                        $booking_variations_ids = BookingPOProducts::where($where_array)->get()->pluck('product_id','id')->toArray();

                        if(!empty($booking_variations_ids))
                        {
                            $delted_variations = array_diff($booking_variations_ids, $request->var_sku_id);
                            
                            $delted_variation_ids = array_keys($delted_variations);
                            
                            $variation_has_cases = BookingPOProductCaseDetails::selectRaw('count(*) as cases_count')->whereIn('booking_po_product_id',$delted_variation_ids)->first()->toArray();
                    
                            if($variation_has_cases['cases_count'] > 0)
                            {    
                                $msg = "You cannot remove variant(s), onces you have received its quantity.";

                                return $this->sendValidation(array($msg), 422);
                            }
                        }

                        $mp_insert = array();

                        foreach ($request->var_sku_id as $mp_id) {
                            if (in_array($mp_id, $booking_variations_ids)) {
                                $key = array_search($mp_id, $booking_variations_ids);
                                unset($booking_variations_ids[$key]);
                            }
                            else {
                                $mp_insert[] = array(
                                    'booking_id'        => $booking_product_detail->booking_id,
                                    'po_id'             => $booking_product_detail->po_id,
                                    'parent_id'         => $booking_product_detail->id,
                                    'product_id'        => $mp_id,
                                    'product_parent_id' => $parent_product_id,
                                    'is_photobooth'     => $product_exist_array['is_request_new_photo'],
                                    'scan_by_user_id'   => $request->user()->id,
                                    'scan_date'         => date('Y-m-d H:i:s'),
                                    'created_by'        => $request->user()->id,
                                    'modified_by'       => $request->user()->id,
                                );
                            }
                        }


                        if (!empty($mp_insert)) {
                            BookingPOProducts::insert($mp_insert);
                        }

                        if (!empty($booking_variations_ids)) {
                            $ids =  array_keys($booking_variations_ids);
                            BookingPOProducts::whereIn('id', $ids)->delete();
                        }

                        $mark_is_variant = array(
                            'is_photobooth' => '0',
                            'is_variant'    => '1',
                        );

                        BookingPOProducts::setParentData($booking_product_detail->id, $mark_is_variant, $booking_product_detail);
                    }

                    DB::commit();

                    return $this->sendResponse('Variation product(s) successfully added', 200);
                }
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    public
            function validateVariationData($request, $variation_combinations) {
        $result = array(
            'status' => true,
            'msg'    => '',
        );

        foreach ($request->var_sku as $key => $v_sku) {

            if (empty($request->var_id[$key]) && in_array($v_sku, $request->var_sku_id)
            ) {
                $variation_theme1 = !empty($request->var_size[$key]) ? $request->var_size[$key] : NULL;

                $variation_theme2 = !empty($request->var_color[$key]) ? $request->var_color[$key] : NULL;

                $variation_theme_combination = trim($variation_theme1) . '|||' . trim($variation_theme2);

                if (in_array($variation_theme_combination, $variation_combinations)) {
                    $result = array(
                        'status' => false,
                        'msg'    => 'Variation combinations should be unique',
                    );

                    break(1);
                }
                else
                {
                    $variation_combinations[] = $variation_theme_combination;
                }
            }
        }

        return $result;
    }

    public
            function upload_product_img($img, $id) {
        $path       = "";
        $thumb_path = "";
        $extension  = "";

        // images
        if (!empty($img)) {
            $extension = strtolower($img->getClientOriginalExtension());

            $folder = "product-images/" . $id;

            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0777, true);
            }

            $uploadedFile = $img;

            if ($extension == "mp4") {
                $filename = $uploadedFile->getClientOriginalName();

                $name = md5($filename . time()) . '.' . $uploadedFile->getClientOriginalExtension();

                $path = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
            }
            else {

                $name = time() . 'internalImage.' . $extension;

                $path = Storage::putFileAs(($folder), $uploadedFile, $name);

                $folder = "product-images/" . $id . '/thumbnail/';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }

                $thumbName1 = explode('/', $path);

                $thumbName = $thumbName1[2];

                $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . "product-images/" . $id . '/' . $thumbName;

                $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $folder . $thumbName;

                Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                })->save($thumbPath, 100);

                $thumb_path = $folder . $thumbName;
            }

            $thumb_path = $extension == 'mp4' ? $path : $thumb_path;
        }

        return array(
            'image_path' => $path,
            'thumb_path' => $thumb_path,
            'extension'  => $extension,
        );
    }

    public
            function saveProductForReturnToSupplier(CreateRequest $request) {
        try {
            $db_array['booking_id']         = $request->booking_id;
            $db_array['barcode']            = $request->barcode;
            $db_array['return_to_supplier'] = 1;
            $db_array['scan_by_user_id']    = $request->user()->id;
            $db_array['scan_date']          = date('Y-m-d H:i:s');
            $db_array['created_by']         = $request->user()->id;
            $db_array['modified_by']        = $request->user()->id;

            if (BookingPOProducts::create($db_array)) {
                return $this->sendResponse('Product successfully added', 200);
            }
            else {
                return $this->sendValidation(array('Unable to save record, please try again'), 422);
            }
        }
        catch (Exception $e) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    // get all Location which is on quarntin location
    function getQuarantinLocationProductOnSave(Request $request) {
        if (!empty($request->booking_id)) {
            $productList               = BookingPO::getQuarantinLocationProducts($request->booking_id);
            $selectedProductQc         = \App\BookingQcChecklist::pluck('product_id')->toArray();
            $data                      = array();
            $data['productList']       = $productList;
            $data['selectedProductQc'] = $selectedProductQc;
            return $this->sendResponse('success', 200, $data);
        }
        else {
            return $this->sendResponse('fail', 422);
        }
    }

    // public
    //         function manage_summary($booking_id) {
    //     if (!empty($booking_id)) {
    //         //get other details
    //         $products_wise_data          = BookingPO::getBookingProducts($booking_id);
    //         $total_qty_received          = 0;
    //         $total_diff_po_delivery_note = 0;
    //         $total_is_variant            = 0;
    //         $total_value                 = 0;
    //         $booking_po_product_id_array = array();
    //         $total_shortage              = 0;
    //         $total_over                  = 0;
    //         $total_damag_trand           = 0;
    //         $total_new_product           = 0;
    //         if (!empty($products_wise_data)) {
    //             foreach ($products_wise_data as $row) {
    //                 $total_qty_received            = $total_qty_received + $row->qty_received;
    //                 $new_thing                     = abs($row->booking_total_quantity - $row->delivery_note_qty);
    //                 $total_diff_po_delivery_note   = $total_diff_po_delivery_note + $new_thing;
    //                 $total_is_variant              = $total_is_variant + $row->is_variant;
    //                 $value_receipt                 = $row->qty_received * $row->unit_price;
    //                 $total_value                   = $total_value + $value_receipt;
    //                 $new_thing                     = 0;
    //                 $booking_po_product_id_array[] = $row->booking_po_product_id;
    //                 if (empty($row->is_listed_on_magento)) {
    //                     $total_new_product = $total_new_product + 1;
    //                 }
    //             }
    //         }

    //         if (!empty($booking_po_product_id_array)) {
    //             $po_desc_list = BookingPODiscrepancy::WhereIn('booking_po_products_id', $booking_po_product_id_array)->get();
    //             if (!empty($po_desc_list)) {
    //                 foreach ($po_desc_list as $row) {
    //                     if ($row->discrepancy_type == 1) {
    //                         $total_shortage = $total_shortage + $row->qty;
    //                     }
    //                     else if ($row->discrepancy_type == 2) {
    //                         $total_over = $total_over + $row->qty;
    //                     }
    //                     else if ($row->discrepancy_type == 4 || $row->discrepancy_type == 6) {
    //                         $total_damag_trand = $total_damag_trand + $row->qty;
    //                     }
    //                 }
    //             }
    //         }

    //         $update_data['total_qty_received']     = $total_qty_received;
    //         $update_data['total_value_received']   = $total_value;
    //         $update_data['total_variants']         = $total_is_variant;
    //         $update_data['total_new_products']     = $total_new_product;
    //         $update_data['total_damage_trade_qty'] = $total_damag_trand;
    //         $update_data['total_short_qty']        = $total_shortage;
    //         $update_data['total_over_qty']         = $total_over;
    //         $update_data['total_diff_po_note']     = $total_diff_po_delivery_note;
    //         //update new counter
    //         Booking::where('id', $booking_id)->update($update_data);
    //     }
    // }

    public
            function setParentProductDeliveryNoteQty(CreateRequest $request) {
        try {

            $booking_po_product_id = $request->booking_po_product_id;

            $product_details = BookingPOProducts::find($booking_po_product_id);

            $product_details->consider_parent_delivery_note_qty = $request->consider_parent_delivery_note_qty;

            if ($request->consider_parent_delivery_note_qty == 0) {
                $product_details->delivery_note_qty = "";
            }

            DB::beginTransaction();

            $product_details->save();

            if ($request->consider_parent_delivery_note_qty == 1) {
                $variation_ids = BookingPOProducts::where('parent_id', $booking_po_product_id)->pluck('id')->toArray();

                $update_array = array(
                    'delivery_note_qty' => "",
                );

                BookingPOProducts::whereIn('id', $variation_ids)->update($update_array);
            }

            DB::commit();

            return $this->sendResponse('Product successfully saved', 200);

            return $this->sendValidation(array('Unable to save record, please try again'), 422);
        }
        catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function productList(CreateRequest $request) {
        try {
            

            $case_without_location = array();
            
            $product_case_details = array();

            $bookingProductsIds = array();

            $parent_ids = array();

            $var_result = array();

            $final_desc = array();
            
            $user_ids = array();

            $user_details = array();
            
            $inventory_case_details = array();
            
            $inventory_outer_barcode_id = array();

            $params['booking_id'] = $request->booking_id;

            $params['sort_by'] = !empty($request->sort_by) ? $request->sort_by : 'id';

            $params['sort_direction'] = !empty($request->sort_direction) ? $request->sort_direction : 'desc';

            $params['per_page'] = !empty($request->per_page) ? $request->per_page : '25';

            $params['search'] = !empty($request->search) ? $request->search : '';

            $params['search_type'] = !empty($request->search_type) ? $request->search_type : 'pending_products';

            $params['search'] = !empty($request->search) ? $request->search : '';

            $params['show_discrepancies'] = !empty($request->show_discrepancies) ? $request->show_discrepancies : 0;

            $params['filter_by_po'] = !empty($request->filter_by_po) ? $request->filter_by_po : "";

            $result = BookingPO::bookingProducts($params);
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    if (!empty($row->booking_po_product_id)) {
                        $bookingProductsIds[] = $row->booking_po_product_id;

                        if ($row->is_variant == '1') {
                            $parent_ids[] = $row->booking_po_product_id;
                        }
                    }
                    
                    if(empty($row->booking_po_product_id) && $row->case_barcode_type == 3)
                    {
                        $case_product_id = $row->case_product_id;

                        $inventory_case_details[$case_product_id]['outer'] = array(
                                'barcode' => $row->case_barcode,
                                'case_quantity' => $row->case_quantity,
                            );
                        
                        $inventory_outer_barcode_id[] = $row->product_case_id;
                    }

                    if(empty($user_ids[$row->scan_by_user_id]))
                    {
                        $user_ids[] = $row->scan_by_user_id;
                    }    
                    
                }
            }

            if (!empty($bookingProductsIds)) {
                if (!empty($parent_ids)) {
                    $var_params['parent_ids'] = $parent_ids;

                    $var_booking_products = BookingPOProducts::getVariants($var_params);

                    foreach ($var_booking_products as $var_booking_product) {
                        $var_result[$var_booking_product->parent_id][] = $var_booking_product;

                        $bookingProductsIds[] = $var_booking_product->booking_po_product_id;
                        
                        if(empty($user_ids[$var_booking_product->scan_by_user_id]))
                        {
                            $user_ids[] = $var_booking_product->scan_by_user_id;
                        } 
                    }
                }

                $bookingProductsIds = array_filter($bookingProductsIds);

                if (!empty($bookingProductsIds)) {
                    $temp_product_case_details = BookingPOProductCaseDetails::bookingProductCasedetails($bookingProductsIds);
                    
                    if(!empty($temp_product_case_details))
                    {
                        foreach($temp_product_case_details as $case_details)
                        {
                            $booking_po_product_id = $case_details['booking_po_product_id'];

                            if(empty($case_details['is_without_case_location']))
                            {    
                                $case_type = $case_details['case_type'];
                                
                                if($case_details['put_away_started'] == 1 
                                    || (!empty($case_details['inner_cases']) 
                                        && $case_details['inner_cases'][0]['put_away_started'] == 1
                                    )
                                )
                                {
                                    $case_details['put_away_started'] = 1;

                                    if(!empty($case_details['inner_cases'])) 
                                    {
                                        $case_details['inner_cases'][0]['put_away_started'] = 1;
                                    }    

                                }    

                                $product_case_details[$booking_po_product_id][$case_type][] = $case_details;
                            }
                            else
                            {
                                $location = $case_details['case_locations'][0];
                                
                                $case_without_location[$booking_po_product_id]['location'] = $location['location_details']['location'];

                                $case_without_location[$booking_po_product_id]['type_of_location'] = $location['location_details']['type_of_location'];
                            }
                        }    
                    }    
                    
                    //get discrepancy
                    $discri_array = BookingPODiscrepancy::with('DiscrepancyImages')->whereIn('booking_po_products_id', $bookingProductsIds)->get()->toArray();
                    
                    if (!empty($discri_array)) {
                        foreach ($bookingProductsIds as $row) {
                            foreach ($discri_array as $row1) {
                                if ($row1['booking_po_products_id'] == $row) {
                                    $final_desc[$row][] = $row1;
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($user_ids))
            {
                $user_details = \App\User::select('id','first_name', 'last_name')->whereIn('id', $user_ids)->get()->keyBy('id')->toArray();
            }    
            
            if(!empty($inventory_outer_barcode_id))
            {
                $inventory_inner_barcode = ProductBarcode::select('barcode', 'case_quantity', 'product_id')->whereIn('parent_id', $inventory_outer_barcode_id)->where('barcode_type', 2)->get()->keyBy('product_id')->toArray();
                
                if(!empty($inventory_inner_barcode))
                {
                    foreach($inventory_inner_barcode as $case_product_id => $inner_details)
                    {    
                        $inventory_case_details[$case_product_id]['inner'] = array(
                                'barcode' => $inner_details['barcode'],
                                'case_quantity' => $inner_details['case_quantity'],
                            );
                    }
                }    
                
            }    

            // $var_result = !empty($var_result) ? $var_result : new \stdClass();

            $data = compact(
            'result',
            'params',
            'product_case_details',
            'final_desc',
            'var_result',
            'case_without_location',
            'user_details',
            'inventory_case_details',
            );

            return $this->sendResponse('Product list', 200, $data);
        }
        catch (Exception $e) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function manageVariations(CreateRequest $request) {
        try {

            $selected_variants = array();

            $form_id = !empty($request->form_id) ? $request->form_id : "";

            $booking_id = $request->booking_id;

            $result = Products::with('variation')->find($request->product_id);

            if(!empty($request->booking_po_product_id))
            {    
                $where_array['parent_id'] = $request->booking_po_product_id;
                
                $selected_variants = BookingPOProducts::where($where_array)->pluck('product_id')->toArray();
            }

            if (!empty($result)) {
                $variation_themes = \App\VariationThemes::get();

                $data = compact(
                'result',
                'variation_themes',
                'booking_id',
                'selected_variants',
                'form_id',
                );

                return $this->sendResponse('Product variation list', 200, $data);
            }
            else
            {

                return $this->sendValidation(array('No product found.'), 422);
            }
        }
        catch (Exception $e) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    //sidebar view data for material receipt
    public function sideBarViewData(Request $request)
    {
        /**For Right Side Bar **/
        $palletList=\App\Pallet::select('id','name')->get();
        $booking_details =  Booking::find($request->booking_id);
        $receivedPallets=\App\BookingPallet::select('id','pallet_type','pallet_id','num_of_pallets')->where('pallet_type','1')->where('booking_id',$request->booking_id)->get();

        $returnPallets=\App\BookingPallet::select('id','pallet_type','pallet_id','num_of_pallets')->where('pallet_type','2')->where('booking_id',$request->booking_id)->get();

        $selectedQc=\App\BookingQcChecklist::where('booking_id',$request->booking_id)->pluck('qc_list_id')->toArray();

        $productList=BookingPO::getQuarantinLocationProducts($request->booking_id);
        $qcList=\App\QCChecklist::select('id','name')->get()->toArray();
       
        $qcList=\App\QCChecklist::with(['checklistPoints'=>function($query){
        $query->select('id', 'title','qc_id');}])->select('id','name')->get();
       // $newproductList=array();
         foreach ($productList as $key => $value) {
            //dd($qcList);
            $newproductList=$qcList;
         //  dd( $newproductList);
            foreach ($newproductList as $key1 => $value1) {
                $isselectedQc=\App\BookingQcChecklist::select('id','qc_list_id')->where('booking_id',$request->booking_id)->where('qc_list_id',$value1['id'])->where('product_id',$value['id'])->first();
                $newproductList[$key1]['is_selected']=(!empty($isselectedQc))? 1 : 0;
                if($newproductList[$key1]['is_selected']==1)
                {
                    //dd($newproductList[$key1]['checklist_points']);
                    foreach ($newproductList[$key1]['checklistPoints'] as $key2 => $value2) {
                        $savedCheckListData=$isselectedQc->bookingQCChecklistPoints()->where('qc_option_id',$value2->id)->first();
                        //$newChecklIstData=$value2;
                        if(!empty($savedCheckListData))
                        {

                            $newproductList[$key1]['checklistPoints'][$key2]['comments']=is_null($savedCheckListData->comments) ? '' : $savedCheckListData->comments;
                            //dd($newproductList[$key1]['checklistPoints'][$key2]['comments']);
                            $newproductList[$key1]['checklistPoints'][$key2]['is_checked']=$savedCheckListData->is_checked;
                            $newproductList[$key1]['checklistPoints'][$key2]['image']=$savedCheckListData->image;
                        }
                        else
                        {
                            $newproductList[$key1]['checklistPoints'][$key2]['comments']='';
                            $newproductList[$key1]['checklistPoints'][$key2]['is_checked']=0;
                            $newproductList[$key1]['checklistPoints'][$key2]['image']=url('/img/no-img-black.png');
                        }
                    }
                }
                else
                {
                    foreach ($newproductList[$key1]['checklistPoints'] as $key2 => $value2) {
                        
                        $newproductList[$key1]['checklistPoints'][$key2]['comments']='';
                        $newproductList[$key1]['checklistPoints'][$key2]['is_checked']=0;
                        $newproductList[$key1]['checklistPoints'][$key2]['image']=url('/img/no-img-black.png');
                    }
                }
            }
            //dd($newproductList);
            $productList[$key]['qcchecklists']=$newproductList;
            # code...
        }
        
       // print_r($productList);exit;
        $selectedProductQc=\App\BookingQcChecklist::where('booking_id',$request->booking_id)->distinct('product_id')->pluck('product_id')->toArray();
        /**End For Right Side Bar **/
        $respones_compact = compact('selectedQc','productList','selectedProductQc','palletList','receivedPallets','returnPallets','booking_details','qcList'); 

        return $this->sendResponse('Success', 200, $respones_compact);
    }

    public function removeProduct(CreateRequest $request)
    {
        try {
            $product = BookingPOProducts::find($request->booking_po_product_id);
            if(!empty($product))
            {
                if($product->return_to_supplier == 1)
                {   
                    $product->delete();
                    return $this->sendResponse('Product removed successfully.', 200);
                }
                else
                {
                    return $this->sendValidation(array('Product must be marked as Return to Supplier, before removing it.'), 422);
                }
            }
            else
            {
                return $this->sendValidation(array('No product found with given ID.'), 422);
            }    
        } 
        catch (Exception $e) 
        {
            return $this->sendError($ex->getMessage(), 400);   
        }
    }

    // public function manageWithoutCaseLocation($booking_product_details, $location_array)
    // {

    //     if(!empty($booking_product_details['id']))
    //     {    
    //         if(empty($booking_product_details['is_inner_outer_case']))
    //         {
    //             $booking_product_details['is_inner_outer_case'] = 0;
    //         }    

    //         $where_array['booking_po_product_id'] = $booking_product_details['id'];
    //         $where_array['is_without_case_location'] = 1;

    //         $case_details = BookingPOProductCaseDetails::with('caseLocations')->where($where_array)->first();
            
    //         if(empty($location_array['location_id']) 
    //             || $booking_product_details['is_inner_outer_case'] == 1
    //         )
    //         {
    //             if(!empty($case_details))
    //             {    
    //                 $case_details->delete();
    //             }

    //             return true;
    //         }  
            
    //         $case_db_array['booking_po_product_id'] = $booking_product_details['id'];
    //         $case_db_array['is_without_case_location'] = 1;
    //         $case_db_array['is_include_count'] = 1;
    //         $case_db_array['barcode'] = $location_array['barcode'];
    //         $case_db_array['qty_per_box'] = $location_array['qty'];
    //         $case_db_array['case_type'] = 1;
    //         $case_db_array['total'] = $location_array['qty'];
    //         $case_db_array['created_by']  = $location_array['created_by'];
    //         $case_db_array['modified_by'] = $location_array['created_by'];
            
    //         if(!empty($case_details))
    //         {
    //             BookingPOProductCaseDetails::where('id', $case_details->id)->update($case_db_array);

    //             $case_detail_id = $case_details->id;
    //         }   
    //         else
    //         { 
    //             $case_detail_id = BookingPOProductCaseDetails::create($case_db_array)->id;
    //         }

    //         $location_db_array['case_detail_id'] = $case_detail_id;
    //         $location_db_array['qty']            = $location_array['qty'];
    //         $location_db_array['location_id']    = $location_array['location_id'];
    //         $location_db_array['created_by']     = $location_array['created_by'];
    //         $location_db_array['modified_by']    = $location_array['created_by'];
            
    //         if(!empty($case_details->caseLocations[0]))
    //         {   
    //             BookingPOProductLocation::where('id', $case_details->caseLocations[0]->id)->update($location_db_array);

    //             $location_id = $location_array['location_id'];
                
    //             $barcode = $location_array['barcode'];

    //             $best_before_date = 'no-best-before';
                
    //             $this->LOCATION_PUTAWAY_QTY['location_wise'][$location_id] = $case_details->caseLocations[0]['put_away_qty'];

    //             $this->LOCATION_PUTAWAY_QTY['location_barcode_wise'][$location_id][$barcode][$best_before_date] = $case_details->caseLocations[0]['put_away_qty'];
    //         } 
    //         else
    //         {
    //            BookingPOProductLocation::create($location_db_array);
    //         }

    //         return true;
    //     }      
    // }

    public function checkPutawayStart(CreateRequest $request)
    {
        try {
            
            $putaway_start = false;

            $product_details = BookingPOProducts::find($request->booking_po_product_id);
            
            if(!empty($product_details))
            {    
                $product_type = "normal";

                if(!empty($product_details->product))
                {
                    if(!empty($product_details->product->product_type))
                    {    
                        $product_type = $product_details->product->product_type;
                    }
                }  

                if($product_type == 'parent')
                {
                    $booking_poroduct_ids = BookingPOProducts::where('parent_id', $product_details->id)->pluck('id')->toArray();
                } 
                else
                {
                    $booking_poroduct_ids[] = $product_details->id;
                } 
                
                if(!empty($booking_poroduct_ids))
                {    
                    $where_raw_string = 'booking_po_product_locations.put_away_qty > 0 AND booking_po_product_case_details.is_without_case_location = 0';

                    $has_put_away_records = $product_details->getBookingProductLocations($booking_poroduct_ids, $where_raw_string);
                    
                    if(!empty($has_put_away_records))
                    {
                        $putaway_start = true;
                    }
                }
            }

            return $this->sendResponse('Putaway start response', 200, array('putaway_start' => $putaway_start));

        } catch (Exception $e) {
            return $this->sendError(trans('messages.bad_request '), 400);      
        }
    }

    public function deletDescrepancy(CreateRequest $request)
    {
        try 
        {
            $product_type = "normal";

            $qty_ordered = 0;
            
            $booking_product_details = BookingPOProducts::find($request->booking_po_product_id);

            if($booking_product_details->return_to_supplier == 0)
            {    
                $inventory_details = $booking_product_details->product;

                if(!empty($inventory_details))
                {    
                    if($inventory_details->product_type == 'variation')
                    {
                        return $this->sendValidation(array('You cannot add descripancy on variation product.'), 422);
                    }
                    else
                    {
                        $product_type = $inventory_details->product_type;            
                    }
                }
            }

            //deleted record
            BookingPODiscrepancy::where('id', $request->delete_id)->delete();
            
            // MANAGE AUTO DESCRIPANCY
            $booking_po_product_id = array();

            $photobooth_qty = 0;

            $po_product_details = $booking_product_details->getPOProductDetails;
            
            if(!empty($po_product_details))
            {    
                $qty_ordered = $po_product_details->total_quantity;
            }

            if($product_type == 'normal' || empty($product_type))
            {
                $booking_po_product_id = array($booking_product_details->id);
            }
            else
            {
                $booking_variations_details = BookingPOProducts::where('parent_id', $booking_product_details->id)->get()->toArray();
                
                if(!empty($booking_variations_details))
                {
                    foreach($booking_variations_details as $variation_details)
                    {
                        $booking_po_product_id[] = $variation_details['id'];

                        if($variation_details['is_photobooth'] == 1)
                        {
                            $photobooth_qty = $photobooth_qty + 1;
                        }    
                    }    
                }
            }   

            $auto_discrepancy_array['booking_product_details'] = $booking_product_details->toArray();

            $auto_discrepancy_array['qty_ordered'] = $qty_ordered;
            
            $auto_discrepancy_array['location_array'] = $booking_product_details->bookingProductLocationTypeQty($booking_po_product_id);

            if($photobooth_qty > 0)
            {
                $auto_discrepancy_array['location_array']['photobooth_qty'] = $photobooth_qty;
            }    

            $booking_product_status = BookingPOProducts::manageAutoDiscrepancies($auto_discrepancy_array);

            // SET BOOKING COMPLETE
            if ($booking_product_status == 1) {
                Booking::setComplete($booking_product_details->booking_id, true);
            }    

            return $this->sendResponse('Record deleted successfully', 200);
        }
        catch (Exception $ex) {
            return $this->sendError('Unable to deleted record, please try again', 422);
        }            
    }
}
