<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Common\CreateRequest;

class ReportController extends Controller
{
    /**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:excess-qty-received-report', ['only' => ['excessQtyReceivedReport']]);
    }

	public function excessQtyReceivedReport(CreateRequest $request)
	{
		try
        {
        
            $columns=[
                    1 => 'start_date',
                    2 => 'completed_date',
                    3 => 'booking_ref_id',
                    4 => 'supplier_name',
                    5 => 'sku_count',
                    6 => 'quantity',
                    7 => 'value',
                    8 => 'confirmed_with_supplier',
            ];

            $adv_search_array = array();

            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }

            $params  = array(
                 'order_column'    => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                 'order_dir'       => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                 'search'          => $request->search['value'],
                 'advanceSearch'   => $adv_search_array
            );

            
            $result = \App\Booking::excessQtyReport($request->length, $params);
            
            $global_result = \App\Booking::excessQtyReport("", array(), true);
            
            $data = [];
            
            // listing data
            if (!empty($result)) {
                    $data = $result->getCollection()->transform(function ($result) use ($data, $request) {
                        $tempArray   = array();
                        $tempArray[] = "";
                        $tempArray[] = !empty($result->start_date) ? date('l d-M-y, h:i A', strtotime($result->start_date)) : '-';
                        
                        $tempArray[] = !empty($result->completed_date) ? date('l d-M-y, h:i A', strtotime($result->completed_date)) : '-';

                        $booking_html = "-";

                        if(!empty($result->booking_ref_id))
                        {
                        	$booking_html = '<a href="'.url('booking-in').'/'.$result->booking_id.'/edit">'.$result->booking_ref_id.'</a>';
                        }	

                        $tempArray[] = $booking_html;

                        $tempArray[] = !empty($result->supplier_name) ? $result->supplier_name : '-';

                        $tempArray[] = !empty($result->sku_count) ? $result->sku_count : '-';
                         
                        $tempArray[] = !empty($result->quantity) ? $result->quantity : '-';
                         
                        $tempArray[] = !empty($result->value) ? '<span class="font-12-dark mr-1">&#163;</span>'.$result->value : '-';
                         
                        $tempArray[] = ($result->confirmed_with_supplier == 1) ? 'Yes' : 'No';
                        
                        
                        return $tempArray;
                    });
            }
            
            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $result->total(), // Total number of records
                "recordsFiltered" => $result->total(),
                "data"            => $data, // Total data array
                "global_result"   => $global_result // Total data array
            ];
           
            return response()->json($jsonData);
        } catch (Exception $ex) {
            
        }   
	}
}

