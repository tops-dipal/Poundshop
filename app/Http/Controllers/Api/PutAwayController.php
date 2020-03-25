<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking;
use Illuminate\Support\Facades\View;
use App\BookingPOProductLocation;
use App\Products;
use App\LocationAssign;
use App\Locations;
use App\LocationAssignTrans;
use App\ProductBarcode;
use App\BookingPOProducts;
use App\BookingPOProductCaseDetails;
use DB;

class PutAwayController extends Controller {

//
    public
            function dashboard(Request $request) {
        try {
            $columns          = [
                0  => 'booking_ref_id',
                1  => 'completed',
                2  => 'total_pick_put_away',
                3  => 'total_bulk_put_away',
                4  => 'total_pallet_pick_skus',
                5  => 'total_pallet_bulk_skus',
                6  => 'sku_with_no_pick_location',
                7  => 'total_dropshipping_products',
                8  => 'total_seasonal',
                9  => 'total_promotion',
                10 => 'short_dated',
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }

            $params = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                'search'         => $request->search['value'],
                'advance_search' => $adv_search_array
            );

            $totalColArr                                    = array();
            $totalColArr['total_pick_put_away']             = 0;
            $totalColArr['total_bulk_put_away']             = 0;
            $totalColArr['total_pallet_pick_sku']           = 0;
            $totalColArr['total_pallet_bulk_sku']           = 0;
            $totalColArr['total_sku_with_no_pick_location'] = 0;
            $totalColArr['total_drop_shipping_product']     = 0;
            $totalColArr['total_total_seasonal']            = 0;
            $totalColArr['total_total_promotion']           = 0;
            $totalColArr['total_short_dated']               = 0;
            $request->length                                = 1000; //as of now static to avoid pagination
            $booking                                        = Booking::getPutAway($request->length, $params);
            $data                                           = [];
            $delete_allowed_status                          = array('1', '2', '3');
            if (!empty($booking)) {
                $data = $booking->transform(function ($result) use ($data, $delete_allowed_status) {
                    $tempArray = array();
                    if (!empty($result->status) && in_array($result->status, $delete_allowed_status)) {
                        $name = '<a href="' . url('booking-in/' . $result->id . '/edit') . '">' . $result->booking_ref_id . '</a>' . '<br/>' . ucfirst($result->supplier_name);
                    }
                    else {
                        $name = $result->booking_ref_id . '<br/>' . ucfirst($result->supplier_name);
                    }
                    $tempArray[] = $name;
                    $tempArray[] = !empty($result->completed) ? $result->completed . '%' : 0;
                    $tempArray[] = !empty($result->total_pick_put_away) ? $result->total_pick_put_away : 0;
                    $tempArray[] = !empty($result->total_bulk_put_away) ? $result->total_bulk_put_away : 0;
                    $tempArray[] = !empty($result->total_pallet_pick_skus) ? $result->total_pallet_pick_skus : 0;
                    $tempArray[] = !empty($result->total_pallet_bulk_skus) ? $result->total_pallet_bulk_skus : 0;
                    $tempArray[] = !empty($result->total_without_pick_products) ? $result->total_without_pick_products : 0;
                    $tempArray[] = !empty($result->total_dropshipping_products) ? $result->total_dropshipping_products : 0;
                    $tempArray[] = !empty($result->total_seasonal) ? $result->total_seasonal : 0;
                    $tempArray[] = !empty($result->total_promotion) ? $result->total_promotion : 0;
                    $tempArray[] = !empty($result->short_dated) ? $result->short_dated : 0;
                    return $tempArray;
                });
            }

            if ($booking->total() != 0) {
                $dataBook = $booking->toArray();
                foreach ($dataBook['data'] as $key => $value) {
                    $totalColArr['total_pick_put_away']             += $value[2];
                    $totalColArr['total_bulk_put_away']             += $value[3];
                    $totalColArr['total_pallet_pick_sku']           += $value[4];
                    $totalColArr['total_pallet_bulk_sku']           += $value[5];
                    $totalColArr['total_sku_with_no_pick_location'] += $value[6];
                    $totalColArr['total_drop_shipping_product']     += $value[7];
                    $totalColArr['total_total_seasonal']            += $value[8];
                    $totalColArr['total_total_promotion']           += $value[9];
                    $totalColArr['total_short_dated']               += $value[10];
                }
                $totalArr   = array();
                $totalArr[] = "";
                $totalArr[] = "<span class='bold'>Total:</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_pick_put_away'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_bulk_put_away'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_pallet_pick_sku'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_pallet_bulk_sku'] . "</span>";

                $min_text = ($totalColArr['total_sku_with_no_pick_location'] > 0) ? '  Minimum' : "";

                $totalArr[] = "<span class='bold'>" . $totalColArr['total_sku_with_no_pick_location'] . $min_text . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_drop_shipping_product'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_total_seasonal'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_total_promotion'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_short_dated'] . "</span>";
                $data->add($totalArr);
            }

            $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => $booking->total(), // Total number of records
                "recordsFiltered" => $booking->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {

        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PutAway\PutAwayRequest $request
     */
//    public
//            function putAwayProducts(\App\Http\Requests\Api\PutAway\PutAwayRequest $request) {
//
//        try {
//            $params          = ['location'      => $request->pallet_location,
//                'sortBy'        => $request->sort_by,
//                'sortDirection' => $request->sort_direction
//                , 'productSearch' => $request->product_search];
//            $bookLocationObj = new BookingPOProductLocation();
//            $productData     = $bookLocationObj->getPutAway($params);
//            if (isset($productData) && !empty($productData) && @count($productData) > 0) {
//                if ($request->search_by == 'product') { //Product Scan
//                    if (!empty($request->product_search)) {
//                        $productObj = new Products;
//                        $product    = $productObj->getPutAwayProductDetail(['booking_id' => $productData[0]->booking_id, 'product_id' => $productData[0]->product_id]);
//                        if ($product) {
//
//// Location Assignment Data
//                            $product->locationAssign = $product->getLocationAssignedProduct();
//                            foreach ($product->locationAssign as $best_before_date) {
//                                $best_before_date->bestBeforeDate = $best_before_date->bestBeforeDateProducts($best_before_date->id);
//                            }
//
////Case details pending/moving qty
//                            $product->putAwayCase = $product->putAwayCaseDetail(['booking_id' => $productData[0]->booking_id, 'location' => $request->pallet_location, 'po_id' => $productData[0]->po_id]);
//                            $html                 = View::make('put-away._put-away-product-detail', ['productData' => $product]);
//                            $data                 = $html->render();
//                            return $this->sendResponse('Product Detail', 200, ['data' => $data]);
//                        }
//                    }
//                    else {
//                        return $this->sendError('No Records Found.', 200, []);
//                    }
//                }
//                else { //Pallet Scan
//                    $html             = View::make('put-away._put-away-products', ['productData' => $productData, 'params' => $params]);
//                    $data             = $html->render();
//                    $pendingQunaities = 0;
//                    foreach ($productData as $product) {
//                        $pendingQunaities += $product->pending_qty;
//                    }
//                    return $this->sendResponse('Product listing', 200, ['data' => $data, 'total_pending_qty' => $pendingQunaities, 'total_pending_products' => @count($productData)]);
//                }
//            }
//            else {
//                return $this->sendError('No Records Found.', 200, []);
//            }
//        }
//        catch (Exception $ex) {
//            return $this->sendError('Bad Request', 400);
//        }
//    }

    public
            function putAwayProducts(\App\Http\Requests\Api\PutAway\PutAwayRequest $request) {

        try {
            $params = [
                'location'      => $request->pallet_location,
                'sortBy'        => $request->sort_by,
                'sortDirection' => $request->sort_direction
                , 'productSearch' => $request->product_search];

            $locationAssignObj = new LocationAssign;
            $putAwayPalletType = $locationAssignObj->getPalletType($params);
            if (isset($putAwayPalletType) && !empty($putAwayPalletType)) {
                $productData = [];

                if ($putAwayPalletType->putaway_type == 1) { //material receipt
                    $productData = $putAwayPalletType->getBookingPalletProducts($params);
                }
                else if ($putAwayPalletType->putaway_type == 2) { //replen
                    $productData = $putAwayPalletType->getReplenPalletProducts($params);
                }
                else {
                    return $this->sendError('Scanned pallet is not for replen and booking, please try again.', 200, []);
                }
                if (isset($productData) && !empty($productData) && @count($productData) > 0) {
                    if ($request->search_by == 'product') {
                        if (!empty($request->product_search)) {
                            $productObj = new Products;
                            if ($putAwayPalletType->putaway_type == 1) { // material receipt
                                $product = $productObj->getPutAwayProductDetail(['booking_id' => $productData[0]->booking_id, 'product_id' => $productData[0]->product_id]);
                                if ($product) {
// Get Existing Product Location with total num of qty physically
                                    $product->locationAssign = $product->getLocationAssignedProduct();
                                    foreach ($product->locationAssign as $best_before_date) {
                                        $best_before_date->bestBeforeDate = $best_before_date->bestBeforeDateProducts($best_before_date->id);
                                    }

//get detail of pallet location boxes and combination

                                    $product->putAwayCase = $product->putAwayCaseDetail(['booking_id' => $productData[0]->booking_id, 'location' => $request->pallet_location, 'po_id' => $productData[0]->po_id]);
                                    if (getRequestAgent($request->header('User-Agent')) == 'M') {
                                        $product->putAwayCase = $product->putAwayCase->toArray();
                                        $data                 = makeNulltoBlank($product->toArray());
                                    }
                                    else {
                                        $html = View::make('put-away._put-away-product-detail', ['product_search' => $request->product_search, 'productData' => $product, 'request_putaway_type' => $putAwayPalletType->putaway_type]);
                                        $data = $html->render();
                                    }

                                    return $this->sendResponse('Product Detail', 200, ['data' => $data]);
                                }
                            }
                            else { //replen
                                $product = $productObj->getPutAwayReplenProductDetail(['booking_id' => $productData[0]->booking_id, 'product_id' => $productData[0]->product_id]);

                                if ($product) {
// Get Existing Product Location with total num of qty physically
                                    $product->locationAssign = $product->getLocationAssignedProduct();
//dd($product->locationAssign);
                                    foreach ($product->locationAssign as $best_before_date) {
                                        $best_before_date->bestBeforeDate = $best_before_date->bestBeforeDateProducts($best_before_date->id);
                                    }

//get detail of pallet location boxes and combination
                                    $product->putAwayCase = $product->getPutAwayReplenCaseDetail(['booking_id' => $productData[0]->booking_id, 'location' => $request->pallet_location, 'po_id' => $productData[0]->po_id]);

                                    if (getRequestAgent($request->header('User-Agent')) == 'M') {
                                        $product->putAwayCase = $product->putAwayCase->toArray();
                                        $data                 = makeNulltoBlank($product);
                                    }
                                    else {
                                        $html = View::make('put-away._put-away-product-detail', ['product_search' => $request->product_search, 'productData' => $product, 'request_putaway_type' => $putAwayPalletType->putaway_type]);
                                        $data = $html->render();
                                    }

                                    return $this->sendResponse('Product Detail', 200, ['data' => $data]);
                                }
                            }
                        }
                        else {
                            return $this->sendError('No Records Found.', 200, []);
                        }
                    }
                    else { // Pallet Scan.
                        if (getRequestAgent($request->header('User-Agent')) == 'M') {
                            $data = makeNulltoBlank($productData->toArray());
                        }
                        else {
                            $html = View::make('put-away._put-away-products', ['productData' => $productData, 'params' => $params, 'putaway_type' => $putAwayPalletType->putaway_type]);
                            $data = $html->render();
                        }

                        $pendingQunaities = 0;
                        foreach ($productData as $product) {
                            $pendingQunaities += $product->total_pending_qty;
                        }
                        return $this->sendResponse('Product listing', 200, ['data' => $data, 'total_pending_qty' => $pendingQunaities, 'total_pending_products' => @count($productData), 'putaway_type' => $putAwayPalletType->putaway_type]);
                    }
                }
                else {
                    return $this->sendError('Products does not found.', 200, []);
                }
            }
            else {
                return $this->sendError('Pallet does not found.', 200, []);
            }
        }
        catch (Exception $ex) {
            return $this->sendError('Bad Request', 400);
        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PutAway\PutAwayDetailRequest $request
     * @return type
     */
    public
            function putAwayProductsDetail(\App\Http\Requests\Api\PutAway\PutAwayDetailRequest $request) {

        try {
            $productObj = new Products;
            if ($request->putaway_type == 1) { // material receipt
                $product = $productObj->getPutAwayProductDetail(['booking_id' => $request->booking_id, 'product_id' => $request->product_id]);
                if ($product) {
// Get Existing Product Location with total num of qty physically
                    $product->locationAssign = $product->getLocationAssignedProduct();
                    foreach ($product->locationAssign as $best_before_date) {
                        $best_before_date->bestBeforeDate = $best_before_date->bestBeforeDateProducts($best_before_date->id);
                    }
//get detail of pallet location boxes and combination
                    $product->putAwayCase = $product->putAwayCaseDetail(['booking_id' => $request->booking_id, 'location' => $request->pallet_location, 'po_id' => $request->po_id]);

                    $html = View::make('put-away._put-away-product-detail', ['product_search' => $request->product_search, 'productData' => $product, 'request_putaway_type' => $request->putaway_type]);
                    $data = $html->render();
                    return $this->sendResponse('Product Detail', 200, ['data' => $data]);
                }
            }
            else { //replen
                $product = $productObj->getPutAwayReplenProductDetail(['product_id' => $request->product_id]);

                if ($product) {
// Get Existing Product Location with total num of qty physically
                    $product->locationAssign = $product->getLocationAssignedProduct();
//dd($product->locationAssign);
                    foreach ($product->locationAssign as $best_before_date) {
                        $best_before_date->bestBeforeDate = $best_before_date->bestBeforeDateProducts($best_before_date->id);
                    }

//get detail of pallet location boxes and combination
                    $product->putAwayCase = $product->getPutAwayReplenCaseDetail(['location' => $request->pallet_location]);

                    $html = View::make('put-away._put-away-product-detail', ['product_search' => $request->product_search, 'productData' => $product, 'request_putaway_type' => $request->putaway_type]);
                    $data = $html->render();
                    return $this->sendResponse('Product Detail', 200, ['data' => $data]);
                }
            }
        }
        catch (Exception $ex) {
            return $this->sendError('Bad Request', 400);
        }
    }

//    public
//            function putAwayProductsDetail(\App\Http\Requests\Api\PutAway\PutAwayDetailRequest $request) {
//
//        try {
//            $productObj = new Products;
//            $product    = $productObj->getPutAwayProductDetail(['booking_id' => $request->booking_id, 'product_id' => $request->product_id]);
//            if ($product) {
//
//// Location Assignment Data
//                $product->locationAssign = $product->getLocationAssignedProduct();
//                foreach ($product->locationAssign as $best_before_date) {
//                    $best_before_date->bestBeforeDate = $best_before_date->bestBeforeDateProducts($best_before_date->id);
//                }
//
////Case details pending/moving qty
//                $product->putAwayCase = $product->putAwayCaseDetail(['booking_id' => $request->booking_id, 'location' => $request->pallet_location, 'po_id' => $request->po_id]);
//                $html                 = View::make('put-away._put-away-product-detail', ['productData' => $product]);
//                $data                 = $html->render();
//                return $this->sendResponse('Product detail', 200, ['data' => $data]);
//            }
//            else {
//                return $this->sendError('Product not found', 422);
//            }
//        }
//        catch (Exception $ex) {
//            return $this->sendError('Bad Request', 400);
//        }
//    }

    public
            function storePutAway(\App\Http\Requests\Api\PutAway\StorePutAwayRequest $request) {
        DB::beginTransaction();

        try {
//            if (!empty($request->put_away_barcode_textbox)) {
//                $request->put_away_barcode = $request->put_away_barcode_textbox;
//            }
            $params                                          = [
                'location'                => $request->scanned_pallet_location,
                'booking_id'              => $request->put_away_booking_id,
                'po_id'                   => $request->put_away_po_id,
                'product_id'              => $request->put_away_product_id,
                'case_type'               => $request->scanned_case_type,
                'best_before_date'        => $request->put_away_best_before_date,
                'barcode'                 => $request->put_away_barcode,
                'warehouse_id'            => $request->warehouse_id,
                'put_away_warehouse_id'   => $request->put_away_warehouse_id,
                'request_putaway_type'    => $request->request_putaway_type,
                'location_transaction_id' => $request->location_transaction_id];
//get scanned location product detail
            $materialReplenPalletProductLocationAssignDetail = Locations::getScannedPalletLocationAssignDetail($params);
            if (isset($materialReplenPalletProductLocationAssignDetail) && @count($materialReplenPalletProductLocationAssignDetail) > 0) {
                $params['loc_ass_id']          = $materialReplenPalletProductLocationAssignDetail->id;
                $scannedProductTransactionData = Locations::getScannedPalletProductLocationTransactionData($params); // scanned the existing pallet product assign
                if (!empty($scannedProductTransactionData)) {
//Get Scanned Location Exist or Not
                    $moveLocation      = LocationAssign::where('location_id', $request->put_away_location_id)->where('putaway_type', 0)->where('warehouse_id', $request->put_away_warehouse_id)->first();
// If moving location does not found and it's empty location then first assign it as a 0.
                    $canPutAwayProduct = [];
                    if (empty($moveLocation)) {
                        $obj                      = new LocationAssign();
                        $obj->warehouse_id        = $request->put_away_warehouse_id;
                        $obj->product_id          = $request->put_away_product_id;
                        $obj->location_id         = $request->put_away_location_id;
                        $obj->qty_fit_in_location = 0;
                        $obj->putaway_type        = 0;
                        $obj->is_mannual          = 1;
                        $obj->total_qty           = 0;
                        $obj->created_by          = $request->user->id;
                        $obj->modified_by         = $request->user->id;
                        $obj->save();
                        $moveLocation             = $obj->fresh();
                    }
                    else {

                        /*
                         * Pick/Bulk Location Physical (
                         * Step 1 : If(Enter (putaway case,best before date,qty per box,
                         *          barcode is as per inventory product_barcode cases )
                         */

//Check the Location is empty or products exist?
                        if (@count($moveLocation->locationAssignTransaction) > 0) {
                            $whereParams       = [
                                'put_away_barcode_textbox' => $request->put_away_barcode_textbox,
                                'selected_barcode'         => $request->put_away_barcode,
                                'putaway_case'             => $request->store_as_case,
                                'scanned_case_type'        => $request->scanned_case_type,
                                'best_before_date'         => $request->put_away_best_before_date,
                                'loc_ass_id'               => $moveLocation->id,
                                'qty_per_box'              => $request->qty_per_box,
                                'outer_qty_per_box'        => $request->outer_qty_per_box,
                                'inner_qty_per_box'        => $request->inner_qty_per_box,
                                'product_id'               => $moveLocation->product_id
                            ];
                            //   dd($whereParams);
                            $canPutAwayProduct = LocationAssignTrans::putAwayProductCombinationExist($whereParams);
                            if (empty($canPutAwayProduct)) {
                                DB::rollback();
                                return $this->sendError('Putaway not possible, you are try to put product at wrong place.', 200);
                            }
                        }
                    }

//check the existing putaway combination of product/bestbefore/case/barcode is  available or not

                    if ($request->scanned_case_type == 3) {
                        if (in_array($request->store_as_case, [2])) { //inner
//If entered barcode inner case is as per product_barcode [qty per box] ?
                            $caseParams       = [
                                'product_id'    => $request->put_away_product_id,
                                'case_quantity' => $request->qty / $request->total_box,
                                'barcode'       => $request->put_away_barcode_textbox,
                                'outer_barcode' => $request->put_away_barcode
                            ];
                            $innerCaseProduct = ProductBarcode::checkInnerCaseExist($caseParams); // If inner case and barcode does not exist

                            if ($innerCaseProduct['status'] == false && $innerCaseProduct['exist'] == true) { // barcode exist but qty as per barcode not matched
                                DB::rollback();
                                return $this->sendError('Qty per box not matched with selected barcode case.', 200);
                            }
                            else { //barcode not exist create a new barcode and add qty per box according to putaway new
//add new barcode with no of qty case detail into product barcode table
                                $newBarcodeObj                = new ProductBarcode;
                                $newBarcodeObj->barcode       = $caseParams['barcode'];
                                $newBarcodeObj->parent_id     = $innerCaseProduct['outCase']->id;
                                $newBarcodeObj->barcode_type  = 2;
                                $newBarcodeObj->product_id    = $caseParams['product_id'];
                                $newBarcodeObj->case_quantity = $caseParams['case_quantity'];
                                $newBarcodeObj->created_by    = $request->user()->id;
                                $newBarcodeObj->save();
                                $newBarcodeObj->fresh();
                            }

                            if (@count($canPutAwayProduct) > 0 && !empty($canPutAwayProduct)) { //location exist and combination also correct
                            }
                            else { //create a new location assign data
                            }
                        }
                        else if (in_array($request->store_as_case, [1])) {
                            $caseParams    = [
                                'product_id' => $request->put_away_product_id,
                                'barcode'    => $request->put_away_barcode_textbox,
                            ];
                            $looseBarcode  = ProductBarcode::checkLooseBarcodeExist($caseParams); // If inner case and barcode does not exist
                            $newBarcodeObj = new ProductBarcode;
                            if ($looseBarcode['status'] == false) { //not exist create a new one
                                $newBarcodeObj->barcode      = $caseParams['barcode'];
                                $newBarcodeObj->barcode_type = 1;
                                $newBarcodeObj->product_id   = $caseParams['product_id'];
                                $newBarcodeObj->created_by   = $request->user()->id;
                                $newBarcodeObj->save();
                                $newBarcodeObj->fresh();
                            }
                            else {
                                $newBarcodeObj = $looseBarcode;
                            }
                        }
                    }
                    else if ($request->scanned_case_type == 2) { //inner
                        if (in_array($request->store_as_case, [2])) { //inner
                            $caseParams = [
                                'product_id'    => $request->put_away_product_id,
                                'case_quantity' => $request->qty / $request->total_box,
                                'barcode'       => $request->put_away_barcode,
                            ];

                            $innerCaseProduct = ProductBarcode::normalInnerExist($caseParams); // If inner case and barcode does not exist
                            $newBarcodeObj    = new ProductBarcode;

                            if ($innerCaseProduct['status'] == false) { //not exist create a new one
                                $newBarcodeObj->barcode      = $caseParams['barcode'];
                                $newBarcodeObj->barcode_type = 2;
                                $newBarcodeObj->product_id   = $caseParams['product_id'];
                                $newBarcodeObj->created_by   = $request->user()->id;
                                $newBarcodeObj->save();
                                $newBarcodeObj->fresh();
                            }
                            else {
                                $newBarcodeObj = $innerCaseProduct['barcodeData'];
                            }
                        }
                        else { //loose
                            $caseParams    = [
                                'product_id' => $request->put_away_product_id,
                                'barcode'    => $request->put_away_barcode_textbox,
                            ];
                            $looseBarcode  = ProductBarcode::checkLooseBarcodeExist($caseParams); // If inner case and barcode does not exist
                            $newBarcodeObj = new ProductBarcode;
                            if ($looseBarcode['status'] == false) { //not exist create a new one
                                $newBarcodeObj->barcode      = $caseParams['barcode'];
                                $newBarcodeObj->barcode_type = 1;
                                $newBarcodeObj->product_id   = $caseParams['product_id'];
                                $newBarcodeObj->created_by   = $request->user()->id;
                                $newBarcodeObj->save();
                                $newBarcodeObj->fresh();
                            }
                            else {
                                $newBarcodeObj = $looseBarcode['barcodeData'];
                            }
                        }
                    }
                    else { //loose
                        $caseParams    = [
                            'product_id' => $request->put_away_product_id,
                            'barcode'    => $request->put_away_barcode,
                        ];
                        $looseBarcode  = ProductBarcode::checkLooseBarcodeExist($caseParams); // If inner case and barcode does not exist
                        $newBarcodeObj = new ProductBarcode;
                        if ($looseBarcode['status'] == false) { //not exist create a new one
                            $newBarcodeObj->barcode      = $caseParams['barcode'];
                            $newBarcodeObj->barcode_type = 1;
                            $newBarcodeObj->product_id   = $caseParams['product_id'];
                            $newBarcodeObj->created_by   = $request->user()->id;
                            $newBarcodeObj->save();
                            $newBarcodeObj->fresh();
                        }
                        else {
                            $newBarcodeObj = $looseBarcode['barcodeData'];
                        }
                    }
//Now here add the logic for break outer inner case logic
                    if ($request->scanned_case_type == 3) {
                        if (in_array($request->store_as_case, [2])) { //inner box
//fold outer box each
                            $outerBoxFoldArray       = LocationAssignTrans::foldOuterBoxInnerCombination($scannedProductTransactionData, $newBarcodeObj->case_quantity);
                            $remainingQuantitiesData = LocationAssignTrans::remainingInnerCasePallets([
                                        'existingData'      => $outerBoxFoldArray,
                                        'qty'               => $request->qty,
                                        'storeAsQty'        => $request->put_away_store_as,
                                        'innerCaseDetail'   => $newBarcodeObj,
                                        'scannedPalletData' => $scannedProductTransactionData,
                            ]);

//update and remove data from remaining Quantity
                        }
                        else if (in_array($request->store_as_case, [1])) { //loose
                            $InnerCaseDetail = ProductBarcode::getInnerBarcodeData($scannedProductTransactionData->barcode_id);
                            if (isset($InnerCaseDetail) && !empty($InnerCaseDetail)) { //if inner case available
                                $outerBoxFoldArray       = LocationAssignTrans::foldOuterInnerSingleCombination($scannedProductTransactionData, $InnerCaseDetail);
                                $remainingQuantitiesData = LocationAssignTrans::remainingQuantiesPallet([
                                            'existingData'      => $outerBoxFoldArray,
                                            'qty'               => $request->qty,
                                            'storeAsQty'        => $request->put_away_store_as,
                                            'innerCaseDetail'   => $InnerCaseDetail,
                                            'scannedPalletData' => $scannedProductTransactionData,
                                            'singleBarcode'     => $request->put_away_barcode_textbox
                                ]);
                            }
                            else { //else outer case will be a loose directly
                                $outerBoxFoldArray[]     = 1;
                                $remainingQuantitiesData = [
                                    'qty_per_box'                    => $scannedProductTransactionData->qty - $request->qty,
                                    'total_boxes'                    => 1,
                                    'qty'                            => $scannedProductTransactionData->qty - $request->qty,
                                    'case_type'                      => 1,
                                    'barcode_id'                     => $newBarcodeObj->id,
                                    'loc_ass_id'                     => $scannedProductTransactionData->loc_ass_id,
                                    'booking_po_product_id'          => !empty($scannedProductTransactionData->booking_po_product_id) ? $scannedProductTransactionData->booking_po_product_id : NULL,
                                    'booking_po_case_detail_id'      => !empty($scannedProductTransactionData->booking_po_case_detail_id) ? $scannedProductTransactionData->booking_po_case_detail_id : NULL,
                                    'booking_po_product_location_id' => !empty($scannedProductTransactionData->booking_po_product_location_id) ? $scannedProductTransactionData->booking_po_product_location_id : NULL,
                                    'best_before_date'               => !empty($scannedProductTransactionData->best_before_date) ? $scannedProductTransactionData->best_before_date : NULL,
                                    'created_at'                     => \Carbon\Carbon::now(),
                                    'updated_at'                     => \Carbon\Carbon::now(),
                                ];
                            }
                        }
                        else if (in_array($request->store_as_case, [3])) {
                            $outerMovedData = LocationAssignTrans::outerBoxDeduction($scannedProductTransactionData, ['qty' => $request->qty, 'total_box' => $request->total_box]);
                        }
                    }
                    else if ($request->scanned_case_type == 2) { //inner to inner or single
                        if (in_array($request->store_as_case, [2])) { //fold into inner
                            $remainingInnerBoxData = [
                                'qty_per_box'                    => $scannedProductTransactionData->qty_per_box,
                                'total_boxes'                    => $scannedProductTransactionData->total_boxes - $request->total_box,
                                'qty'                            => $scannedProductTransactionData->qty - $request->qty,
                                'case_type'                      => 2,
                                'barcode_id'                     => $newBarcodeObj->id,
                                'loc_ass_id'                     => $scannedProductTransactionData->loc_ass_id,
                                'booking_po_product_id'          => !empty($scannedProductTransactionData->booking_po_product_id) ? $scannedProductTransactionData->booking_po_product_id : NULL,
                                'booking_po_case_detail_id'      => !empty($scannedProductTransactionData->booking_po_case_detail_id) ? $scannedProductTransactionData->booking_po_case_detail_id : NULL,
                                'booking_po_product_location_id' => !empty($scannedProductTransactionData->booking_po_product_location_id) ? $scannedProductTransactionData->booking_po_product_location_id : NULL,
                                'best_before_date'               => !empty($scannedProductTransactionData->best_before_date) ? $scannedProductTransactionData->best_before_date : NULL,
                                'created_at'                     => \Carbon\Carbon::now(),
                                'updated_at'                     => \Carbon\Carbon::now(),
                            ];
                        }
                        else { //fold into single
//Working
                            $innerBoxesFoldArray   = LocationAssignTrans::foldInnerBoxes($scannedProductTransactionData, $request->inner_qty_per_box);
                            $remainingInnerBoxData = LocationAssignTrans::remainingInnersQuantityPallets([
                                        'existingData'      => $innerBoxesFoldArray,
                                        'qty'               => $request->qty,
                                        'storeAsQty'        => $request->put_away_store_as,
                                        'scannedPalletData' => $scannedProductTransactionData,
                                        'innerCaseDetail'   => $newBarcodeObj,
                                        'singleBarcode'     => $request->put_away_barcode_textbox
                            ]);
                        }
                    }
                    else { //loose to loose
                        $remainingInnerQuantitiesCaseData = [
                            'qty_per_box'                    => $scannedProductTransactionData->qty - $request->qty,
                            'total_boxes'                    => 1,
                            'qty'                            => $scannedProductTransactionData->qty - $request->qty,
                            'case_type'                      => 1,
                            'barcode_id'                     => $newBarcodeObj->id,
                            'loc_ass_id'                     => $scannedProductTransactionData->loc_ass_id,
                            'booking_po_product_id'          => !empty($scannedProductTransactionData->booking_po_product_id) ? $scannedProductTransactionData->booking_po_product_id : NULL,
                            'booking_po_case_detail_id'      => !empty($scannedProductTransactionData->booking_po_case_detail_id) ? $scannedProductTransactionData->booking_po_case_detail_id : NULL,
                            'booking_po_product_location_id' => !empty($scannedProductTransactionData->booking_po_product_location_id) ? $scannedProductTransactionData->booking_po_product_location_id : NULL,
                            'best_before_date'               => !empty($scannedProductTransactionData->best_before_date) ? $scannedProductTransactionData->best_before_date : NULL,
                            'created_at'                     => \Carbon\Carbon::now(),
                            'updated_at'                     => \Carbon\Carbon::now(),
                        ];
//update and remove data from remaining Quantity if data is exist
//$remainingInnerQuantitiesCaseData = LocationAssignTrans::updateExistingPutAwayData($remainingInnerQuantitiesCaseData);
                    }




                    $params['locationData']             = $moveLocation;
                    $params['qty']                      = $request->qty;
                    $params['total_boxes']              = $request->total_box;
                    $params['qty_box']                  = $request->qty_per_box;
                    $params['case_type']                = $request->store_as_case;
                    $params['put_away_barcode_textbox'] = $request->put_away_barcode_textbox;



                    if ($request->store_as_case == 3) {
                        $params['barcode_product_detail_id'] = $scannedProductTransactionData->barcode_id;
                    }
                    else {
                        $params['barcode_product_detail_id'] = $newBarcodeObj->id;
                    }

                    $putAwayExistingProductCaseDetail = Locations::getPutAwayProductCaseDetail($params);

                    if (!empty($putAwayExistingProductCaseDetail)) { //If putaway location is found then update it otherwise create a new one
                        if ($request->store_as_case == 3) { //outer
                            $putAwayExistingProductCaseDetail->total_boxes += $request->total_box;
                            $putAwayExistingProductCaseDetail->qty         += $request->qty;
                            $putAwayExistingProductCaseDetail->save();
                            $putAwayExistingProductCaseDetail->fresh();
                        }
                        else if ($request->store_as_case == 2) { //inner
                            $putAwayExistingProductCaseDetail->total_boxes += $request->total_box;
                            $putAwayExistingProductCaseDetail->qty         += $request->qty;
                            $putAwayExistingProductCaseDetail->save();
                            $putAwayExistingProductCaseDetail->fresh();
                        }
                        else { //Single
                            $putAwayExistingProductCaseDetail->qty += $request->qty;
                            $putAwayExistingProductCaseDetail->save();
                            $putAwayExistingProductCaseDetail->fresh();
                        }
                    }
                    else {
//insert new records
//$newBarcodeObj
                        $putAwayExistingProductCaseDetail             = new LocationAssignTrans;
                        $putAwayExistingProductCaseDetail->loc_ass_id = $moveLocation->id;
                        $putAwayExistingProductCaseDetail->qty        = $request->qty;
                        $putAwayExistingProductCaseDetail->case_type  = $request->store_as_case;

                        if (isset($request->put_away_best_before_date) && !empty($request->put_away_best_before_date)) {
                            $putAwayExistingProductCaseDetail->best_before_date = date('Y-m-d', strtotime($request->put_away_best_before_date));
                        }
                        if ($request->store_as_case == 3) { //outer
                            $putAwayExistingProductCaseDetail->barcode_id  = $scannedProductTransactionData->barcode_id;
                            $putAwayExistingProductCaseDetail->qty_per_box = $request->outer_qty_per_box;
                            $putAwayExistingProductCaseDetail->total_boxes = $request->total_box;
                        }
                        else if ($request->store_as_case == 2) { //inner
                            $putAwayExistingProductCaseDetail->barcode_id  = $newBarcodeObj->id;
                            $putAwayExistingProductCaseDetail->qty_per_box = $request->qty_per_box;
                            $putAwayExistingProductCaseDetail->total_boxes = $request->total_box;
                        }
                        else { //Single
                            $putAwayExistingProductCaseDetail->barcode_id  = $newBarcodeObj->id;
                            $putAwayExistingProductCaseDetail->total_boxes = 1;
                            $putAwayExistingProductCaseDetail->qty_per_box = $request->qty;
                        }
                        $putAwayExistingProductCaseDetail->created_at = \Carbon\Carbon::now();
                        $putAwayExistingProductCaseDetail->updated_at = \Carbon\Carbon::now();
                        $putAwayExistingProductCaseDetail->save();
                        $putAwayExistingProductCaseDetail->fresh();
                    }


                    /**
                     *  Save and dedudt qty in transaction table and update tran_assign table
                     * Remove/update scanned pallet transaction table
                     * for outer and single
                     */
                    if (!empty($outerBoxFoldArray)) { //insert remaining pallet combination data and remove pallet row
                        /**
                         * Insert after break up data
                         * remove existing case row from transaction table
                         */
                        if (isset($remainingQuantitiesData) && isset($remainingQuantitiesData['qty']) && $remainingQuantitiesData['qty'] == 0) {//remove remaining box content because there is 0 qty remaining
                            if ($remainingQuantitiesData['qty'] == 0) {
                                LocationAssignTrans::insert($remainingQuantitiesData);
                            }
                        }
                        else {
                            LocationAssignTrans::insert($remainingQuantitiesData);
                        }
                    }
//for inner to inner/single

                    if (!empty($remainingInnerBoxData)) {
                        if (isset($remainingInnerBoxData) && isset($remainingInnerBoxData['qty']) && $remainingInnerBoxData['qty'] == 0) { //remove remaining box content because there is 0 qty remaining
                            if ($remainingInnerBoxData['qty'] != 0) {
                                LocationAssignTrans::insert($remainingInnerBoxData);
                            }
                        }
                        else {
                            LocationAssignTrans::insert($remainingInnerBoxData);
                        }
//$scannedProductTransactionData->delete();
                    }

                    if (!empty($remainingInnerQuantitiesCaseData)) {
                        if ($remainingInnerQuantitiesCaseData['qty'] != 0) {
                            LocationAssignTrans::insert($remainingInnerQuantitiesCaseData);
                        }
//$scannedProductTransactionData->delete();
                    }

                    if (!empty($outerMovedData)) { //outer moved content
                        if ($outerMovedData->qty == 0) {
                            $scannedProductTransactionData->delete();
                        }
                    }
                    else {
                        if (isset($scannedProductTransactionData)) {
                            $scannedProductTransactionData->delete();
                        }
                    }
                    //  dd($scannedProductTransactionData);
//update the actual location qty update based on the put away new product qty
                    $moveLocation->total_qty     = $moveLocation->total_qty + $request->qty;
                    $moveLocation->available_qty = $moveLocation->available_qty + $request->qty;
                    if (!empty($request->qty_fit_location)) {
                        $moveLocation->qty_fit_in_location = $request->qty_fit_location;
                    }

                    if ($moveLocation->save()) {
//update the master pallet material receipt product overall qty update based on the child data in Location Assign Record
                        $totalUpdatedQty                 = LocationAssignTrans::where('loc_ass_id', $materialReplenPalletProductLocationAssignDetail->id)->sum('qty');
                        $palletLocationAssign            = LocationAssign::find($materialReplenPalletProductLocationAssignDetail->id);
                        $palletLocationAssign->total_qty = $totalUpdatedQty;
                        if ($palletLocationAssign->save()) {
//Check if pallet location assignment total qty is 0 then remove it from location table
                            $isPalletEmpty = false;
                            if ($palletLocationAssign->total_qty == 0) {
                                $isPalletEmpty = true;
                                $palletLocationAssign->delete();
                            }
//Booking Po Products and Booking Po Location PutAway qty added
//update BookingPOProducts
                            if (!empty($request->put_away_booking_po_product_id)) {
                                $bookingPOProducatDetail = BookingPOProducts::find($request->put_away_booking_po_product_id);
                                if (isset($bookingPOProducatDetail) && @count($bookingPOProducatDetail) > 0) {
                                    $bookingPOProducatDetail->put_away_quantity = $bookingPOProducatDetail->put_away_quantity + $request->qty;
                                    if ($bookingPOProducatDetail->save()) {
                                        if (!empty($request->put_away_booking_po_product_case_details_id)) {
                                            $bookingCaseDetail                   = BookingPOProductCaseDetails::find($request->put_away_booking_po_product_case_details_id);
                                            $bookingCaseDetail->put_away_started = 1;
                                            if ($bookingCaseDetail->save()) {
                                                if ($bookingCaseDetail->parent_outer_id == NULL) { //update child records
                                                    BookingPOProductCaseDetails::where('parent_outer_id', $bookingCaseDetail->id)->update(['put_away_started' => 1]);
                                                }
                                                else { //update parent records
                                                    BookingPOProductCaseDetails::where('id', $bookingCaseDetail->parent_outer_id)->update(['put_away_started' => 1]);
                                                }
                                            }
                                        }

                                        $bookingPOProductLocationDetail = BookingPOProductLocation::find($request->put_away_booking_po_product_location_id);
                                        if (isset($bookingPOProductLocationDetail) && @count($bookingPOProductLocationDetail) > 0) {
                                            $bookingPOProductLocationDetail->put_away_qty = $bookingPOProductLocationDetail->put_away_qty + $request->qty;
                                            if ($bookingPOProductLocationDetail->save()) {
                                                DB::commit();
                                                return $this->sendResponse('Putaway successfully.', 200, ['is_empty_pallet' => $isPalletEmpty]);
                                            }
                                        }
                                    }
                                }
                            }
                            else {
                                DB::commit();
                                return $this->sendResponse('Putaway successfully.', 200, ['is_empty_pallet' => $isPalletEmpty]);
                            }
                        }
                        else {
                            DB::rollback();
                            return $this->sendError('Pallet location assign not update, please try again.', 422);
                        }
                    }
                }
                else {
                    DB::rollback();
                    return $this->sendError('Booking product case detail not found.', 422);
                }
            }
            else {
                DB::rollback();
                return $this->sendError('Pallet product location does not found', 422);
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError('Bad Request', 400);
        }
    }

    public
            function putAwayJobsProducts(\App\Http\Requests\Api\PutAway\PutAwayJoblistRequest $request) {
        try {
            $params            = [
                'pickbulkjobs'  => $request->pickbulkjobs,
                'job_type'      => $request->job_type,
                'sortBy'        => $request->sort_by,
                'sortDirection' => $request->sort_direction
                , 'productSearch' => $request->product_search];
            $locationAssignObj = new LocationAssign;
            if ($request->job_type == 1) { //material receipt
                $productData = $locationAssignObj->getBookingPalletProducts($params);
            }
            else if ($request->job_type == 2) { //replan job
                $productData = $locationAssignObj->getReplenPalletProducts($params);
            }
            else if ($request->job_type == 3) { //location assign/manual job
                //$productData = $putAwayPalletType->getReplenPalletProducts($params);
            }

            if (isset($productData) && !empty($productData) && @count($productData) > 0) {
                if (getRequestAgent($request->header('User-Agent')) == 'M') {
                    $data = makeNulltoBlank($productData->toArray());
                }
                else {
                    $html = View::make('put-away-joblist._put-away-products', ['productData' => $productData, 'params' => $params, 'putaway_type' => $request->job_type]);
                    $data = $html->render();
                }

                $pendingQunaities = 0;
                foreach ($productData as $product) {
                    $pendingQunaities += $product->total_pending_qty;
                }
                return $this->sendResponse('Product listing', 200, ['data' => $data, 'total_pending_qty' => $pendingQunaities, 'total_pending_products' => @count($productData), 'putaway_type' => $request->job_type]);
            }
            else {
                return $this->sendError('No Records Found.', 200, []);
            }
        }
        catch (Exception $ex) {
            return $this->sendError('Bad Request', 400);
        }
    }

}
