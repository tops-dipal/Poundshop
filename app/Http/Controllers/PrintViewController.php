<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrintViewController extends Controller
{
	function __construct()
    {

    }

    public function barcode(Request $request)
    {
    	$barcode = !empty($request->barcode) ? $request->barcode : '';
    	$count = !empty($request->count) ? $request->count : 1;
    	$barcode_size = !empty($request->barcode_size) ? $request->barcode_size : 25;
    	return view('material_receipt.barcode_print', compact('barcode', 'count', 'barcode_size'));
    }


     //Print Booking QC Checklist data
    public function productQCChecklistPDF(Request $request)
    {

        $bookingPOObj=\App\BookingPO::selectRaw('products.id,
                                            po_products.product_id as product_id,
                                            booking_po_products.id as booking_po_product_id,
                                            booking_po_product_locations.location_id as case_location,
                                            booking_po_product_case_details.id as booking_case_id,
                                            booking_po_product_case_details.booking_po_product_id
                                        ');
        $bookingPOObj->join('po_products', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'po_products.po_id');
        });

        $bookingPOObj->join('products', function($join) {
            $join->on('products.id', '=', 'po_products.product_id');
        });

        $bookingPOObj->leftJoin('booking_po_products', function($join) {
            $join->on('booking_purchase_orders.booking_id', '=', 'booking_po_products.booking_id');
            $join->on('po_products.product_id', '=', 'booking_po_products.product_id');
        });

        $bookingPOObj->leftJoin('booking_po_product_case_details', function($join) {
            $join->on('booking_po_product_case_details.booking_po_product_id', '=', 'booking_po_products.id');
        });

        $bookingPOObj->leftJoin('booking_po_product_locations', function($join) {
            $join->on('booking_po_product_locations.case_detail_id', '=', 'booking_po_product_case_details.id');
         
        });

        $bookingPOObj->join('locations_master', function($join) {
            $join->on(function($query){
                $query->orOn('locations_master.id','=','booking_po_products.location_id');
                $query->orOn('locations_master.id','=','booking_po_product_locations.location_id');
            });
            
            $join->where('locations_master.type_of_location', '=', 8);
        });

        $bookingPOObj->where('booking_purchase_orders.booking_id', (int) $request->booking_id);

        $bookingPOObj->groupBy('po_products.product_id');
        $productIds = $bookingPOObj->pluck('products.id')->toArray();
        
        $selectArr=array('booking_qc_check_lists.id','booking_qc_check_lists.product_id','booking_qc_check_lists.qc_list_id','booking_qc_check_lists.booking_id','bookings.booking_ref_id','bookings.book_date','bookings.status','products.title','qc_checklists.name','qc_checklists.id');
        $objSql=\App\BookingQcChecklist::with(['bookingQCChecklistPoints']);
        $objSql->leftJoin('bookings','bookings.id','booking_qc_check_lists.booking_id');
        $objSql->leftJoin('products','products.id','booking_qc_check_lists.product_id');
        $objSql->leftJoin('qc_checklists','qc_checklists.id','booking_qc_check_lists.qc_list_id');
        $objSql->where('booking_qc_check_lists.booking_id',$request->booking_id);
        $objSql->select($selectArr);
        $objSql->whereIn('booking_qc_check_lists.product_id',$productIds);
        $result=$objSql->get();
        $bookingDetail=\App\Booking::find($request->booking_id);
        $data=$result;
        return view('material_receipt.booking_product_qc_pdf', compact('bookingDetail', 'data'));
    }
}	