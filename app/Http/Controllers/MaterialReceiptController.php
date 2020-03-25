<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Api\Common\CreateRequest;

use Lang;

use App\BookingPO;

use App\Booking;

use App\Products;

use App\BookingPOProductLocation;

use App\BookingPOProductCaseDetails;

use App\BookingPODiscrepancy;

use App\PurchaseOrder;

use App\BookingPOProducts;

use App\Http\Controllers\Api\MaterialReceiptController as MaterialReceiptApi;
use App\Http\Controllers\Api\CommonApiController;

class MaterialReceiptController extends Controller
{
     /**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    
    protected $MaterialReceiptApi;

    function __construct(Request $request)
    {
        $this->MaterialReceiptApi = new MaterialReceiptApi($request);
        $this->MaterialReceiptApi->set_web_response = true;

        $this->CommonApiController = new CommonApiController($request);
        $this->CommonApiController->set_web_response = true;
    }

    public function index(Request $request, $booking_id)
    {
        try
        {
            if(empty($booking_id))
            {  
                return back()->withInput();
            }    
            
            $custom_req  = new CreateRequest(['model' => 'Booking', 'p_id' => $booking_id]);

            $booking_details = $this->CommonApiController->find($custom_req);  
    	    
            $booking_details = $booking_details['data'];

            if(empty($booking_details) || empty($booking_details->warehouse_id))
            {
                return back()->withInput();
            } 
            
            $booking_pos = $booking_details->bookingPOs()->get();
            
            $page_title = $prefix_title = Lang::get('messages.material_receipt.material_receipt');

            $pagination_url = url('material-receipt/list-ajax-table');

            $pagination_page = !empty($request->page) ? $request->page  : '1';
            
            $per_page_value = !empty($request->per_page_value) ? $request->per_page_value  : '10';

            $search = !empty($request->search) ? $request->search  : '';
            
            $search_type = !empty($request->search_type) ? $request->search_type  : 'pending_products';

            $pagination_sort_by = !empty($request->sort_by) ? $request->sort_by : '';
            
            $pagination_sort_direction = !empty($request->sort_direction) ? $request->sort_direction : '';
            
            $show_discrepancies = !empty($request->show_discrepancies) ? $request->show_discrepancies : 0;
            
            

            return view('material_receipt.index', compact(
                                                            'page_title',
                                                            'prefix_title',
                                                            'pagination_url',
                                                            'pagination_page',
                                                            'pagination_sort_by',
                                                            'pagination_sort_direction',
                                                            'booking_details',
                                                            'per_page_value',
                                                            'show_discrepancies',
                                                            'search',
                                                            'search_type',
                                                            'booking_pos'
                                                        )
                    );
                
        }
        catch (Exception $ex) {
            
        } 
    }

    public function listAjaxTable(CreateRequest $request)
    {
        $render_html = "";

        if(!empty($request->booking_id))
        {
            $response = $this->MaterialReceiptApi->productList($request);
            
            if(!empty($response['data']))
            {
                extract($response['data']);

                $compact_keys = array_keys($response['data']);
            }    

            $render_html = view('material_receipt.list-ajax', compact($compact_keys));            
        }   
        
        echo $render_html;
    }	

    public function manageVariations(CreateRequest $request)
    {
        try 
        {
            $render_html = "";

            $response = $this->MaterialReceiptApi->manageVariations($request);

            if(!empty($response['data']))
            {
                extract($response['data']);

                $compact_keys = array_keys($response['data']);
            }    

            $render_html = view('material_receipt.variation_modal', compact($compact_keys));
            
            echo $render_html;
        }
        catch (Exception $e) 
        {
            
        }
    }        


    // public function manageVariations(Request $request)
    // {
    //     try {
            
    //         $render_html = "";
            
    //         $booking_id = $request->booking_id;
            
    //         $form_id = $request->form_id;

    //         $custom_create_req = new CreateRequest(['model' => 'Products', 'p_id'=>$request->product_id]);
            
    //         $result = $this->CommonApiController->find($custom_create_req);
            
    //         $result = $result['data'];

    //         $custom_create_array['booking_id'] = $booking_id;
            
    //         $custom_create_array['product_parent_id'] = $request->product_id;

    //         $custom_create_req = new CreateRequest($custom_create_array);

    //         $selected_variants = $this->MaterialReceiptApi->productVariantes($custom_create_req);

    //         $selected_variants = $selected_variants['data'];
            
    //         if(!empty($result))
    //         {    
    //             $custom_create_req = new CreateRequest(['model' => 'VariationThemes']);
                
    //             $variation_themes = $this->CommonApiController->get($custom_create_req);
                
    //             $variation_themes = $variation_themes['data'];
                
    //             $render_html = view('material_receipt.variation_modal', compact(
    //                                                                     'result',
    //                                                                     'variation_themes',
    //                                                                     'booking_id',
    //                                                                     'selected_variants',
    //                                                                     'form_id',
    //                                                                 )
    //                             );
    //         }

    //         echo $render_html;

    //     } catch (Exception $e) {
            
    //     }
    // }

    public function htmlVersion()
    {
        return view('material_receipt.index_html_version');
    }

    public function getSideBarView(Request $request)
    {
        $render_html = "";
        if(!empty($request->booking_id))
        {
            $response = $this->MaterialReceiptApi->sideBarViewData($request);
            
            if(!empty($response['data']))
            {
                extract($response['data']);

                $compact_keys = array_keys($response['data']);
            }    

            $render_html = view('material_receipt.booking-qc-list', compact($compact_keys));            
        }   
        
        echo $render_html;
    }

    public function htmlBookingPallet(Request $request)
    {
        $render_html = "";
        if(!empty($request->booking_id))
        {
            $response = $this->MaterialReceiptApi->sideBarViewData($request);
            
            if(!empty($response['data']))
            {
                extract($response['data']);

                $compact_keys = array_keys($response['data']);
            }    

            $render_html = view('material_receipt.booking-pallet-form', compact($compact_keys));            
        }   
        
        echo $render_html;
    }
}    	