<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Lang;

use App\BookingPO;

use App\Booking;

use App\Products;

use App\BookingPOProductLocation;

use App\BookingPOProductCaseDetails;

use App\BookingPODiscrepancy;

use App\PurchaseOrder;

class SupplierMaterialReceiptController extends Controller
{
     /**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {

    }

    public function index($booking_id, Request $request)
    {
        try
        {
            if(empty($booking_id))
            {  
                return back()->withInput();
            }    
            
            $booking_details =  Booking::find($booking_id);  
            
            if(empty($booking_details))
            {
                return back()->withInput();
            }                

            $page_title = $prefix_title = Lang::get('messages.material_receipt.material_receipt');

            $booking_pos = $booking_details->bookingPOs()->get();

            //get product level details
            $params['booking_id'] = $booking_id;            
            $params['sort_by'] = 'id';
            $params['sort_direction'] = 'desc';
            $params['per_page'] = '5000';
            $params['search'] = '';
            $params['search_type'] = 'all_products';            
            $params['show_discrepancies'] =  1;            
            $params['filter_by_po'] = !empty($request->filter_by_po) ? $request->filter_by_po : "";                     
            $result = BookingPO::bookingProducts($params);   
            $bookingProductsIds = $result->pluck('booking_po_product_id')->toArray();
            $final_desc=array();
           
            if(!empty($bookingProductsIds))
            {
                $bookingProductsIds = array_filter($bookingProductsIds);
                
                if(!empty($bookingProductsIds))
                {
                    $book_po_desc=new BookingPODiscrepancy;
                    
                    $discri_array=$book_po_desc->get_product_desc_image_data($bookingProductsIds);

                    if(!empty($discri_array))
                    {
                        foreach($bookingProductsIds as $row)
                        {

                            foreach($discri_array as $row1)
                            {
                                if($row1['booking_po_products_id']==$row)
                                {
                                    $final_desc[$row][]=$row1;
                                }
                            }                            
                        }
                    }                        
                }
            }                       
            return view('supplier_material_receipt.index', compact('page_title','booking_details','result','final_desc','booking_pos','params'));
                
        }
        catch (Exception $ex) {
            
        } 
    }

    	
}    	