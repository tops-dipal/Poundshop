<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PO\CreateRequest;
use App\Http\Requests\Api\Common\CreateRequest as MoveToNewPORequest;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use Illuminate\Support\Facades\View;
use DB;
use App\SupplierMaster;
use App\ProductBarcode;
use App\Products;
use Carbon\Carbon;
use PDF;
use App\Setting;
use App\BookingPODiscrepancy;
use App\Http\Requests\Api\PO\DeliveryRequest;
use App\BookingPOProducts;
use App\Booking;

class PurchaseOrderController extends Controller {

    /**
     * Display a listing of the resource.
     * @author : Hitesh Tank
     * @return \Illuminate\Http\Response
     */
    public
            function index(Request $request) {



        try {
            $columns = [
                0 => 'id',
                1 => 'po_number',
                2 => 'supplier_order_number',
                3 => 'supplier_master.name',
                4 => '',
                5 => 'po_status',
                6 => 'booking_purchase_orders.created_at',
                7 => 'purchase_order_master.created_at',
            ];
            parse_str($request->advanceSearch, $searchArray);

            $params = [
                'order_column'  => $columns[$request->order[0]['column']],
                'order_dir'     => $request->order[0]['dir'],
                'search'        => $request->search,
                'advanceSearch' => $searchArray
//             'po_status'    => $request->po_status,
//             'supplier_category'=>$request->supplier_cat,
//             'supplier_name'=>$request->supplier_name,
//             'uk_po'=>$request->uk_po,
//             'import_po'=>$request->import_po
            ];

            $purchaseOrders = PurchaseOrder::getAllOrders($request->length, $params);
            $data           = [];

            if (!empty($purchaseOrders)) {
                $data = $purchaseOrders->getCollection()->transform(function ($result) use ($data) {
                    $tempArray        = [];
                    $tempArray[]      = View::make('components._po-list-checkbox', ['object' => $result])->render();
                    $tempArray[]      = View::make('purchase-orders._color-code-listing', ['object' => $result, 'column' => 'title'])->render();
                    $tempArray[]      = !empty($result->supplier_order_number) ? $result->supplier_order_number : '--';
                    $tempArray[]      = $result->name;
                    $tempArray[]      = '<p class="mb-0 pr-3 text-right">' . trans('messages.common.pound_sign') . priceFormate($result->total_cost) . "</p>";
                    $tempArray[]      = View::make('purchase-orders._color-code-listing', ['object' => $result, 'column' => 'po_status'])->render();
                    $tempArray[]      = View::make('purchase-orders._booking-buttons', ['object' => $result])->render();
                    $tempArray[]      = $result->created_at->format('d-M-Y h:i A');
                    $viewActionButton = View::make('purchase-orders.action-buttons', ['object' => $result]);
                    $tempArray[]      = $viewActionButton->render();
                    return $tempArray;
                });
            }

            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $purchaseOrders->total(), // Total number of records
                "recordsFiltered" => $purchaseOrders->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function create() {
//
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public
            function store(CreateRequest $request) {
        try {

            $purchaseOrder                        = new PurchaseOrder;
            $purchaseOrder->supplier_id           = $request->supplier;
            $purchaseOrder->supplier_contact      = $request->supplier_contact;
            $purchaseOrder->po_number             = $request->po_number;
            $purchaseOrder->supplier_order_number = !empty($request->supplier_order_number) ? $request->supplier_order_number : null;
            $purchaseOrder->incoterms             = $request->incoterms;

            if ($request->hidden_country == 230) {
                $purchaseOrder->po_import_type = 1;
                $purchaseOrder->country_id     = 230;
            }
            else {
                $purchaseOrder->po_import_type = 2;
                $purchaseOrder->country_id     = $request->hidden_country;
            }


            $purchaseOrder->mode_of_shipment   = $request->mode_of_shipment;
            $purchaseOrder->po_status          = $request->po_status;
            $purchaseOrder->exp_deli_date      = $request->exp_deli_date;
            $purchaseOrder->standar_rate_value = $request->hidden_standard_rate;
            $purchaseOrder->zero_rate_value    = $request->hidden_zero_rate;
            $purchaseOrder->is_drop_shipping   = $request->is_drop_shipping;
            if ($request->po_status == config('params.po_status.Cancelled'))
                $purchaseOrder->po_cancel_date     = Carbon::now();
            if ($request->po_status == config('params.po_status.Live PO')) {
                if (!empty($request->po_date)) {
                    $purchaseOrder->po_date = $request->po_date;
                }
                else {
                    $purchaseOrder->po_date = Carbon::now();
                }
            }
            else {
                if (!empty($request->po_date)) {
                    $purchaseOrder->po_date = $request->po_date;
                }
                else {
                    $purchaseOrder->po_date = Carbon::now();
                }
            }

            $purchaseOrder->notes            = $request->notes;
            $purchaseOrder->supplier_comment = $request->supplier_comment;
            $purchaseOrder->recev_warehouse  = $request->recev_warehouse;

            $billingInformation                     = Setting::getData(array('billing_address'));
            $purchaseOrder->created_by              = $request->user->id;
            $purchaseOrder->modified_by             = $request->user->id;
            $purchaseOrder->terms_supplier          = $request->user->id;
            $purchaseOrder->billing_street_address1 = $billingInformation[0]->column_val;
            $purchaseOrder->billing_street_address2 = $billingInformation[1]->column_val;
            $purchaseOrder->billing_country         = $billingInformation[2]->column_val;
            $purchaseOrder->billing_state           = $billingInformation[3]->column_val;
            $purchaseOrder->billing_city            = $billingInformation[4]->column_val;
            $purchaseOrder->billing_zipcode         = $billingInformation[5]->column_val;
            $wareHouse                              = \App\WareHouse::find($request->recev_warehouse);
            $purchaseOrder->warehouse               = $wareHouse->name;
            $purchaseOrder->street_address1         = $wareHouse->address_line1;
            $purchaseOrder->street_address2         = $wareHouse->address_line2;
            $purchaseOrder->country                 = $wareHouse->getCountry->name;
            $purchaseOrder->state                   = $wareHouse->getState->name;
            $purchaseOrder->city                    = $wareHouse->getCity->name;
            $purchaseOrder->zipcode                 = $wareHouse->zipcode;
            $terms                                  = \App\Terms::first();
            $purchaseOrder->terms_supplier          = SupplierMaster::find($request->supplier)->term_condition;
            if ($purchaseOrder->po_import_type == 1) {
                $purchaseOrder->terms_poundshop = !empty($terms->terms_pound_uk) ? $terms->terms_pound_uk : null;
            }
            else {
                $purchaseOrder->terms_poundshop = !empty($terms->terms_pound_non_uk) ? $terms->terms_pound_non_uk : null;
            }



            if ($purchaseOrder->save()) {
                $data['edit-url'] = route('api-purchase-orders.update', $purchaseOrder->id);
                $data['data']     = $purchaseOrder;
                return $this->sendResponse(trans('messages.purchase_order_messages.create'), 200, $data);
            }
            else {
                return $this->sendError(trans('messages.purchase_order_messages.create_error'), 422);
            }
        }
        catch (Exception $ex) {
            return $this->sendError(trans('messages.bad_request'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function show($id) {
//
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function edit($id) {
//
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function update(\App\Http\Requests\Api\PO\UpdateRequest $request, $id) {
        try {
            $purchaseOrder = PurchaseOrder::find($id);
            if ($purchaseOrder) {
//$purchaseOrder->supplier_id=$request->supplier;
                $purchaseOrder->supplier_contact = $request->supplier_contact;
// $purchaseOrder->supplier_order_number=$request->supplier_order_number;
                if (isset($request->po_import_type) && !empty($request->po_import_type))
                    $purchaseOrder->po_import_type   = $request->po_import_type;

                if (isset($request->country_id) && !empty($request->country_id))
                    $purchaseOrder->country_id = $request->country_id;


                $purchaseOrder->po_status        = $request->po_status;
                $purchaseOrder->exp_deli_date    = $request->exp_deli_date;
                $purchaseOrder->is_drop_shipping = $request->is_drop_shipping;
                if ($request->po_status == config('params.po_status.Cancelled')) {
                    if (empty($purchaseOrder->po_cancel_date)) {
                        $purchaseOrder->po_cancel_date = Carbon::now();
                    }
                }

                if ($request->po_status == config('params.po_status.Live PO')) {
                    if (!empty($request->po_date)) {
                        $purchaseOrder->po_date = $request->po_date;
                    }
                    else {
                        $purchaseOrder->po_date = Carbon::now();
                    }
                }
                else {
                    if (!empty($request->po_date)) {
                        $purchaseOrder->po_date = $request->po_date;
                    }
                    else {
                        $purchaseOrder->po_date = Carbon::now();
                    }
                }
                $purchaseOrder->incoterms             = $request->incoterms;
                $purchaseOrder->mode_of_shipment      = $request->mode_of_shipment;
                $purchaseOrder->supplier_order_number = !empty($request->supplier_order_number) ? $request->supplier_order_number : null;
                $purchaseOrder->notes                 = $request->notes;
                $purchaseOrder->supplier_comment      = $request->supplier_comment;
                $purchaseOrder->modified_by           = $request->user->id;
                $purchaseOrder->updated_at            = Carbon::now();
                if ($purchaseOrder->save()) {
                    $purchaseOrder = $purchaseOrder->fresh();

                    return $this->sendResponse(trans('messages.purchase_order_messages.update'), 200, $purchaseOrder);
                }
                else {
                    return $this->sendError(trans('messages.purchase_order_messages.update_error'), 422);
                }
            }
            else {
                return $this->sendError(trans('messages.purchase_order_messages.not_found'), 422);
            }
        }
        catch (Exception $ex) {
            return $this->sendError(trans('messages.bad_request'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @autor Hitesh Tank
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function destroy($id) {
        $po = PurchaseOrder::find($id);
        if ($po->po_status == 1) {
            if ($po->delete()) {
                return $this->sendResponse(trans('messages.purchase_order_messages.delete'), 200);
            }
            else {
                return $this->sendError(trans('messages.purchase_order_messages.delete_error'), 422);
            }
        }
        else {
            return $this->sendError(trans('messages.purchase_order_messages.delete_error'), 200);
        }
    }

    /**
     * @author Hitesh tank
     * @param Request $request
     * @return type
     */
    public
            function destroyMany(Request $request) {
        $ids              = $request->ids;
        $purchaseOrders   = PurchaseOrder::whereIn('id', explode(",", $ids))->get();
        $isDeleted        = false;
        $someOfNonDeleted = false;
        foreach ($purchaseOrders as $purchaseOrder) {
            if ($purchaseOrder->po_status == 1) {
                $purchaseOrder->delete();
                $isDeleted = true;
            }
            else {
                $someOfNonDeleted = true;
            }
        }
        if ($isDeleted) {
            if ($someOfNonDeleted) {
                return $this->sendResponse(trans('messages.purchase_order_messages.delete_multi'), 200);
            }
            else {
                return $this->sendResponse(trans('messages.purchase_order_messages.delete'), 200);
            }
        }
        else {
            return $this->sendError(trans('messages.purchase_order_messages.delete_multi_error'), 200);
        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PO\TermsRequest $request
     * @return \Illuminate\Http\Response
     */
    public
            function updateTerms(\App\Http\Requests\Api\PO\TermsRequest $request) {
        try {

            $poDetail = PurchaseOrder::find($request->id);
            if ($poDetail) {
                $poDetail->terms_poundshop = $request->term_condition;
                $poDetail->terms_supplier  = $request->term_supplier_condition;
                $poDetail->modified_by     = $request->user->id;
                if ($poDetail->save()) {
                    return $this->sendResponse(trans('messages.purchase_order_messages.terms'), 200);
                }
                else {
                    return $this->sendError(trans('messages.purchase_order_messages.terms_error'), 422);
                }
            }
            else {
                return $this->sendError(trans('messages.purchase_order_messages.terms_error'), 422);
            }
        }
        catch (Exception $ex) {
            return $this->sendError(trans('messages.bad_request'), 400);
        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PO\PoItemSaveRequest $request
     * @return type
     */
    public
            function poItemSave(\App\Http\Requests\Api\PO\PoItemSaveRequest $request) {

        DB::beginTransaction();
        try {

            if ($request->data) {
                $saveContentIds      = [];
                $datas               = \GuzzleHttp\json_decode($request->data);
                $updateItems         = [];
                $updatedIds          = [];
                $newAddedBarcodes    = [];
                $newAddedSupplierSKU = [];
                $isAddNewBarcode     = false;
                $productIds          = [];
//Unique barcode logic
                foreach ($datas as $item) {

                    if (empty($item->bar_code) && !empty($item->new_barcode)) {
                        $newAddedBarcodes []   = $item->new_barcode;
                        $newAddedSupplierSKU[] = $item->supplier_sku;
                        if ($item->product_id != "") {
                            $productIds[] = $item->product_id;
                        }
                        else {
                            $productIds[] = "";
                        }
                    }
                    else {
                        if ((($item->bar_code !== $item->new_barcode) || empty($item->bar_code)) && !empty($item->new_barcode)) {

                            $newAddedBarcodes []   = $item->new_barcode;
                            $newAddedSupplierSKU[] = $item->supplier_sku;
                            if (!empty($item->product_id)) {
                                $productIds[] = $item->product_id;
                            }
                            else {
                                $productIds[] = "";
                            }
                        }
                    }
                }


//Unique barcode logic
                $obj = new ProductBarcode();
                if (!empty($newAddedBarcodes)) {
                    if ($obj->uniqueBarCode($newAddedBarcodes) == false) {
                        return $this->sendError(trans('messages.purchase_order_messages.barcode_unique'), 200);
                    }
                    else {
                        $isAddNewBarcode = true;
                    }
                }

//update the Purchase Order content
                $purchaseOrder      = new PurchaseOrder();
                $purchaseOrder->updateContent($request);
//check if new barcode added into po items if yes then add it onto product table
                $newAddedProductIds = [];
                if (in_array(null, $productIds)) {
                    foreach ($productIds as $key => $productASNull) {
                        if (empty($productASNull)) {
                            $newId                = Products::addNewProductAsDraft($newAddedBarcodes[$key]);
                            $newAddedProductIds[] = $newId;
                            $productIds[]         = $newId;

//Assign sku to supplier product
                            \App\ProductSupplier::insert(['product_id' => $newId, 'supplier_id' => $request->supplier_id, 'supplier_sku' => $newAddedSupplierSKU[$key], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'created_by' => $request->user->id, 'modified_by' => $request->user->id]);
                        }
                    }
                }

                $productIds = array_values(array_filter($productIds));
//Add new barcode
                if ($isAddNewBarcode == true) {
                    $obj->addBarcodes(['products' => $productIds, 'barcodes' => $newAddedBarcodes, 'requestObj' => $request]);
                }
                $newProductkey = 0;
                foreach ($datas as $key => $item) {

                    //Check the Product ROs value if exist remain same else update the product ros

                    $item          = (array) $item;
                    $item['po_id'] = $request->po_id;
                    if (isset($item['id']) && !empty($item['id'])) { //prepared update po items
                        $updatedIds[]  = $item['id'];
                        $updateItems[] = PurchaseOrderProduct::preparedUpdateItems($item); //save the po items
                    }
                    else { //prepared new content
                        //Check the Product ROs value if exist remain same else update the product ros
                        if (empty($item['product_id'])) {
                            $item['product_id'] = $newAddedProductIds[$newProductkey];
                            $newProductkey++;
                        }

                        $productData      = Products::find($item['product_id']);
                        $productData->ros = $item['expected_mros'];
                        $productData->save();
                        $poItem           = PurchaseOrderProduct::saveItemContent($item); //save the po items
                        $saveContentIds[] = $poItem->product_id;
                    }
                }

//assigned products to supplier
                if (!empty($saveContentIds)) {
                    $supplier         = SupplierMaster::find($request->supplier_id);
                    $supplierProducts = $supplier->supplierProducts()->get(['product_id'])->toArray();
                    $productsIds      = [];
                    foreach ($supplierProducts as $product) {
                        $productsIds[] = $product['product_id'];
                    }
                    foreach ($saveContentIds as $product_id) {
                        if (!in_array($product_id, $productsIds)) { //save
                            $supplier->supplierProducts()->attach($product_id, ['created_by' => $request->user->id, 'modified_by' => $request->user->id]);  //assign product to supplier
                        }
                    }
                }

                if (!empty($updatedIds)) {
                    PurchaseOrderProduct::updateItems($updateItems, $updatedIds);
                }
                DB::commit();
                $purchaseOrder = PurchaseOrder::find($request->po_id);
                if ($purchaseOrder->po_import_type == 1)
                    $data          = view('purchase-orders._uk-po-items', compact('purchaseOrder'))->render();
                else
                    $data          = view('purchase-orders._import-po-items', compact('purchaseOrder'))->render();
                return $this->sendResponse(trans('messages.purchase_order_messages.po_items_update'), 200, $data);
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError(trans('messages.bad_request'), 400);
        }
    }

    /**
     *
     * @param Request $request
     * @return type
     */
    public
            function destroyItemMany(Request $request) {
        DB::beginTransaction();
        try {
            $ids = $request->ids;

            foreach (explode(",", $ids) as $id) {
                PurchaseOrderProduct::find($id)->delete();
            }
            $purchaseOrder = PurchaseOrder::find($request->purchase_order_id);
            PurchaseOrderProduct::reCalculateItemsData($purchaseOrder);
            $purchaseOrder->fresh();
            DB::commit();
            if ($purchaseOrder->po_import_type == 1) {
                $data['data'] = view('purchase-orders._uk-po-items', compact('purchaseOrder'))->render();
            }
            else {
                $data['data']              = view('purchase-orders._import-po-items', compact('purchaseOrder'))->render();
                $data['total_import_duty'] = $purchaseOrder->total_import_duty;
                $data['total_cost']        = $purchaseOrder->total_cost;
                $data['total_no_of_cubes'] = $purchaseOrder->total_number_of_cubes;
                $data['remaining_space']   = $purchaseOrder->remaining_space;
            }
            $data['sub_total']           = $purchaseOrder->sub_total;
            $data['supplier_min_amount'] = floatval($purchaseOrder->supplier->min_po_amt);
            $data['remaining_amount']    = floatval($purchaseOrder->sub_total - $purchaseOrder->supplier->min_po_amt);
            $data['total_margin']        = $purchaseOrder->total_margin;
            return $this->sendResponse(trans('messages.purchase_order_messages.item_delete'), 200, $data);
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError(trans('messages.bad_request'), 400);
        }
    }

    /**
     *
     * @param Request $request
     * @return type
     */
    public
            function destroyItem(Request $request) {

        DB::beginTransaction();
        try {
            $po            = PurchaseOrderProduct::find($request->id);
            $purchaseOrder = $po->purchaseOrder;
            if ($po->delete()) {
                DB::commit();
                $purchaseOrder->refresh();
                PurchaseOrderProduct::reCalculateItemsData($purchaseOrder);

                if ($purchaseOrder->po_import_type == 1) {
                    $data['data'] = view('purchase-orders._uk-po-items', compact('purchaseOrder'))->render();
                }
                else {
                    $data['data']                  = view('purchase-orders._import-po-items', compact('purchaseOrder'))->render();
                    $data['total_import_duty']     = $purchaseOrder->total_import_duty;
                    $data['total_cost']            = $purchaseOrder->total_cost;
                    $data['total_no_of_cubes']     = $purchaseOrder->total_number_of_cubes;
                    $data['remaining_space']       = $purchaseOrder->remaining_space;
                    $data['total_delivery_charge'] = $purchaseOrder->total_delivery_charge;
                    $data['cost_per_cube']         = $purchaseOrder->cost_per_cube;
                    $data['total_space']           = $purchaseOrder->total_space;
                }
                $data['sub_total']           = $purchaseOrder->sub_total;
                $data['supplier_min_amount'] = floatval($purchaseOrder->supplier->min_po_amt);
                $data['remaining_amount']    = floatval($purchaseOrder->sub_total - $purchaseOrder->supplier->min_po_amt);
                $data['total_margin']        = $purchaseOrder->total_margin;

                return $this->sendResponse(trans('messages.purchase_order_messages.item_delete'), 200, $data);
            }
            else {
                return $this->sendError(trans('messages.purchase_order_messages.item_delete_error'), 422);
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError(trans('messages.bad_request'), 400);
        }
    }

    public
            function taxPaymentReport(Request $request) {
        try {

            $columns          = [
                0 => 'po_number',
                1 => 'po_date',
                2 => 'name',
                3 => 'po_type',
                4 => 'amount_before_vat',
                5 => 'import_duty_pound',
                6 => 'vat_import',
                7 => 'vat_uk',
                8 => 'total_zero_rated',
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }

            $params = array(
                'order_column'   => $columns[$request->order[0]['column']],
                'order_dir'      => $request->order[0]['dir'],
                'search'         => $request->search['value'],
                'advance_search' => $adv_search_array,
            );

            $mapping = PurchaseOrder::getTaxReports($request->length, $params);

            $data = [];
            $str  = '';
            if (!empty($mapping)) {

                $total_vat           = 0;
                $total_vat_on_uk     = 0;
                $total_vat_on_import = 0;
                $total_tax           = 0;
                $total_import_duty   = 0;
                foreach ($mapping as $key => $value) {
                    //dd($value);
                    if ($value->po_type == 'UK PO') {
                        $total_vat_on_uk = $total_vat_on_uk + $value->product()->sum('vat_in_amount');
                    }
                    else {
                        $total_vat_on_import = $total_vat_on_import + $value->product()->sum('vat_in_amount');
                    }




                    $total_import_duty = $total_import_duty + $value->import_duty_pound;
                }
// echo gettype($total_vat);exit;

                $data = $mapping->transform(function ($result) use ($data) {
                    $tempArray   = array();
                    /* $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render(); */
                    $tempArray[] = '<div class="min-h-35">' . $result->po_number . '</div>';
                    $tempArray[] = $result->po_date;
                    $tempArray[] = ucwords($result->name);
                    $tempArray[] = $result->po_type;
                    $tempArray[] = !is_null($result->amount_before_vat) ? trans('messages.common.pound_sign') .$result->amount_before_vat : '-';
                    $tempArray[] = ( $result->po_import_type == 2) ? trans('messages.common.pound_sign') .$result->import_duty_pound : '-';
                    $tempArray[] = ( $result->po_import_type == 2) ? trans('messages.common.pound_sign') .$result->vat_import : '-';
                    $tempArray[] = ($result->po_import_type == 1) ? trans('messages.common.pound_sign') .$result->vat_uk : '-';
                    $tempArray[] = (!is_null($result->total_zero_rated)) ? trans('messages.common.pound_sign') .$result->total_zero_rated : '-';
                    return $tempArray;
                });
            }

            $jsonData = [
                "draw"                => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"        => count($mapping), // Total number of records
                "recordsFiltered"     => count($mapping),
                "data"                => $data, // Total data array,
                "total_vat"           => number_format($total_vat_on_import + $total_vat_on_uk, 2),
                "total_tax"           => number_format($total_import_duty + $total_vat_on_import + $total_vat_on_uk, 2),
                "total_import_duty"   => number_format($total_import_duty, 2),
                "total_vat_on_import" => number_format($total_vat_on_import, 2),
                "total_vat_on_uk"     => number_format($total_vat_on_uk, 2),
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {
            return 'error';
        }
    }

    /**
     * @author Hitesh Tank
     * @param Request $request
     * @return type
     * @description download the purchase order detail
     */
    public
            function downloadPO(Request $request) {
        $purchaseOrder = \App\PurchaseOrder::find($request->purchase_order_id);
        if ($purchaseOrder->po_import_type == 1) {
//return view('purchase-orders._download-uk-po', ['purchaseOrder' => $purchaseOrder]);
            $pdf = PDF::loadView('purchase-orders._download-uk-po', ['purchaseOrder' => $purchaseOrder]);
        }
        else {
//return view('purchase-orders._download-import-po', ['purchaseOrder' => $purchaseOrder]);
            $pdf = PDF::loadView('purchase-orders._download-import-po', ['purchaseOrder' => $purchaseOrder]);
        }

        return $pdf->download($purchaseOrder->po_number . '.pdf');
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PO\SendEmailRequest $request
     * @description send po pdf to supplier
     */
    public
            function sendPO(\App\Http\Requests\Api\PO\SendEmailRequest $request) {
        try {
            $purchaseOrder = PurchaseOrder::find($request->po_id);

            if ($purchaseOrder->po_import_type == 1) {
                $pdf = PDF::loadView('purchase-orders._download-uk-po', ['purchaseOrder' => $purchaseOrder]);
            }
            else {
                $pdf = PDF::loadView('purchase-orders._download-import-po', ['purchaseOrder' => $purchaseOrder]);
            }

            $file_name = \Illuminate\Support\Str::random('5') . ".pdf";
            $path      = storage_path('app/public/uploads/temp/') . $file_name;
            $pdf->save($path);
            \Illuminate\Support\Facades\Mail::to($purchaseOrder->supplierContact->email)->send(new \App\Mail\SendPO($purchaseOrder, $path, $file_name));
            return $this->sendResponse(trans('messages.purchase_order_messages.po_sent'), 200);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PO\ReCalculateRequest $request
     * @return type
     */
    public
            function reCalculateItems(\App\Http\Requests\Api\PO\ReCalculateRequest $request) {
        DB::beginTransaction();
        try {
            $totalImportDuty       = 0;
            $totalNetSellingPrice  = 0;
            $totalNetSellingProfit = 0;
            $totalMargin           = 0;
            $country               = \App\Country::find($request->country_id);

            $purchaseOrder = PurchaseOrder::find($request->po_id);

            foreach ($purchaseOrder->product as $item) {
                $importDutyRate                        = \App\ImportDuty::getImportDutyValue($country->commodityCodes(), $item); //in percentage
                $item->import_duty                     = $importDutyRate;
                $item->import_duty_in_amount           = ($item->total_product_cost * $importDutyRate) / 100;
                $item->total_cost                      = $item->total_product_cost + $item->import_duty_in_amount;
                $item->itd_vat                         = $item->total_product_cost + $item->import_duty_in_amount + $item->total_delivery_charge;
                $item->total_vat                       = ($item->itd_vat * $item->vat) / 100;
                $item->landed_product_cost             = !empty($item->total_quantity) && $item->total_quantity != 0 ? ($item->itd_vat / $item->total_quantity) : 0;
                $item->landed_price_in_pound           = !empty($item->currency_exchange_rate) && $item->currency_exchange_rate != 0 ? ($item->landed_product_cost / $item->currency_exchange_rate) : 0;
                $item->net_selling_price_excluding_vat = ($item->sel_price / (100 + $item->vat)) * 100;
                $item->total_net_selling_price         = !empty($item->sel_qty) && $item->sel_qty != 0 ? ($item->net_selling_price_excluding_vat * $item->total_quantity) / $item->sel_qty : 0;
                $item->total_net_profit                = $item->total_net_selling_price - $item->itd_vat;
                $item->total_net_margin                = !empty($item->total_net_selling_price) ? ($item->total_net_profit / $item->total_net_selling_price) * 100 : 0;
//PO level
                $totalImportDuty                       += $item->import_duty_in_amount;
                $totalNetSellingPrice                  += $item->total_net_selling_price;
                $totalNetSellingProfit                 += $item->total_net_profit;
                $item->save();
            }
            $purchaseOrder->total_import_duty = $totalImportDuty;
            $purchaseOrder->total_cost        = $purchaseOrder->total_import_duty + $purchaseOrder->sub_total + $purchaseOrder->total_delivery_charge;
            $purchaseOrder->total_margin      = !empty($totalNetSellingProfit) ? ($totalNetSellingPrice / $totalNetSellingProfit) * 100 : 0;
            $purchaseOrder->country_id        = $request->country_id;
            $purchaseOrder->save();
            DB::commit();
            $purchaseOrder->fresh();
            $data['data']                     = view('purchase-orders._import-po-items', compact('purchaseOrder'))->render();
            $data['total_import_duty']        = $purchaseOrder->total_import_duty;
            $data['total_cost']               = $purchaseOrder->total_cost;
            $data['total_no_of_cubes']        = $purchaseOrder->total_number_of_cubes;
            $data['remaining_space']          = $purchaseOrder->remaining_space;
            $data['total_delivery_charge']    = $purchaseOrder->total_delivery_charge;
            $data['cost_per_cube']            = $purchaseOrder->cost_per_cube;
            $data['total_space']              = $purchaseOrder->total_space;
            $data['sub_total']                = floatval($purchaseOrder->sub_total);
            $data['total_margin']             = floatval($purchaseOrder->total_margin);
            return $this->sendResponse(trans('messages.purchase_order_messages.recal'), 200, $data);
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PO\DeliveryRequest $request
     */
    public
            function debitNote(DeliveryRequest $request) {
        try {
            $model         = new BookingPODiscrepancy();
            $params        = ["discrepancy_ids" => $request->disIds, "status" => 1, 'user' => $request->user]; // debit
            $model->updateDiscrepancy($params);
            $purchaseOrder = PurchaseOrder::find($request->po_id);
            $deliveryData  = $purchaseOrder->deliveryDetail();
            $deliveryHtml  = View::make('purchase-orders._delivery-content', ['deliveryData' => $deliveryData[0]])->render();
            return $this->sendResponse("Debit Note generated", 200, $deliveryHtml);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    /**
     * @author Hitesh Tank
     * @param DeliveryRequest $request
     * @return type
     */
    public
            function returnSupplier(DeliveryRequest $request) {
        try {
            $model         = new BookingPODiscrepancy();
            $params        = ["discrepancy_ids" => $request->disIds, "status" => 4, 'user' => $request->user]; // return
            $model->updateDiscrepancy($params);
            $purchaseOrder = PurchaseOrder::find($request->po_id);
            $deliveryData  = $purchaseOrder->deliveryDetail();
            $deliveryHtml  = View::make('purchase-orders._delivery-content', ['deliveryData' => $deliveryData[0]])->render();
            return $this->sendResponse("Return to supplier generated", 200, $deliveryHtml);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    /**
     * @author Hitesh TAnk
     * @param DeliveryRequest $request
     * @return type
     */
    public
            function keepIt(DeliveryRequest $request) {
        try {
            $model         = new BookingPODiscrepancy();
            $params        = ["discrepancy_ids" => $request->disIds, "status" => 2, 'user' => $request->user]; // keepit
            $model->updateDiscrepancy($params);
            $purchaseOrder = PurchaseOrder::find($request->po_id);
            $deliveryData  = $purchaseOrder->deliveryDetail();
            $deliveryHtml  = View::make('purchase-orders._delivery-content', ['deliveryData' => $deliveryData[0]])->render();
            return $this->sendResponse("Keep it generated", 200, $deliveryHtml);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    /**
     * @author Hitesh TAnk
     * @param DeliveryRequest $request
     * @return type
     */
    public
            function cancelled(DeliveryRequest $request) {
        try {
            $model                   = new BookingPODiscrepancy();
            $params                  = ["discrepancy_id" => $request->discrepancy_id, "status" => 5, 'user' => $request->user]; // cancelled
            $model->updateDiscrepancy($params);
            $keepData                = BookingPODiscrepancy::find($request->discrepancy_id);
            //Shubham code writtern here
            $booking_po_product_id   = array();
            $photobooth_qty          = 0;
            $booking_product_details = $keepData->bookingProduct;

            $po_product_details = $keepData->bookingProduct->getPOProductDetails;

            if (!empty($po_product_details)) {
                $qty_ordered = $po_product_details->total_quantity;
            }

            if ($po_product_details->products == 'normal' || empty($po_product_details->products)) {
                $booking_po_product_id = array($booking_product_details->id);
            }
            else {

                $booking_variations_details = BookingPOProducts::where('parent_id', $booking_product_details->id)->get()->toArray();

                if (!empty($booking_variations_details)) {
                    foreach ($booking_variations_details as $variation_details) {
                        $booking_po_product_id[] = $variation_details['id'];

                        if ($variation_details['is_photobooth'] == 1) {
                            $photobooth_qty = $photobooth_qty + 1;
                        }
                    }
                }
            }

            $auto_discrepancy_array['booking_product_details'] = $booking_product_details->toArray();
            $auto_discrepancy_array['qty_ordered']             = $qty_ordered;
            $auto_discrepancy_array['location_array']          = $booking_product_details->bookingProductLocationTypeQty($booking_po_product_id);
            if ($photobooth_qty > 0) {
                $auto_discrepancy_array['location_array']['photobooth_qty'] = $photobooth_qty;
            }
            $booking_product_status = BookingPOProducts::manageAutoDiscrepancies($auto_discrepancy_array);

            // SET BOOKING COMPLETE
            if ($booking_product_status == 1) {
                Booking::setComplete($booking_product_details->booking_id, true);
            }
            //Shubham code writtern here
            $purchaseOrder = PurchaseOrder::find($request->po_id);
            $deliveryData  = $purchaseOrder->deliveryDetail();
            $deliveryHtml  = View::make('purchase-orders._delivery-content', ['bookingProducts' => $deliveryData->bookingProducts, 'deliveryData' => $deliveryData[0], 'purchaseOrder' => $purchaseOrder])->render();

            return $this->sendResponse("Cancelled has been generated", 200, $deliveryHtml);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    /**
     * @author Hitesh Tank
     * @param DeliveryRequest $request
     */
    public
            function moveNewPO(DeliveryRequest $request) {
        DB::beginTransaction();
        try {
            $purchaseOrder                          = PurchaseOrder::find($request->po_id);
            $newOutStandinPO                        = $purchaseOrder->replicate();
            $PoNumber                               = PurchaseOrder::autoGeneratePO();
            $newOutStandinPO->po_number             = $PoNumber;
            $newOutStandinPO->po_status             = 1;
            $newOutStandinPO->is_outstanding_po     = 1;
            $newOutStandinPO->sub_total             = 0;
            $newOutStandinPO->total_import_duty     = 0;
            $newOutStandinPO->total_delivery_charge = 0;
            $newOutStandinPO->total_cost            = 0;
            $newOutStandinPO->total_margin          = 0;
            $newOutStandinPO->total_space           = 0;
            $newOutStandinPO->cost_per_cube         = 0;
            $newOutStandinPO->remaining_space       = 0;
            $newOutStandinPO->remaining_space       = null;
            $newOutStandinPO->status                = 1;
            $newOutStandinPO->deleted_at            = null;
            $newOutStandinPO->po_date               = Carbon::now();
            $newOutStandinPO->exp_deli_date         = null;
            $newOutStandinPO->po_cancel_date        = null;
            $subTotal                               = 0;
            if ($newOutStandinPO->save()) {
                $items = PurchaseOrder::getOutstandPOItems(['product_id' => $request->product_id, 'purchase_order_id' => $purchaseOrder->id]);
                foreach ($items as $key => $item) {
                    $data['po_id']                           = $newOutStandinPO->id;
                    $data['product_id']                      = $item->product_id;
                    $data['supplier_sku']                    = $item->supplier_sku;
                    $data['new_barcode']                     = $item->barcode;
                    $data['variant']                         = $item->is_variant;
                    $data['qty_per_box']                     = 0;
                    $data['total_box']                       = 0;
                    $data['total_quantity']                  = $request->qty;
                    $data['unit_price']                      = $item->unit_price;
                    $data['total_product_cost']              = ($request->qty * $item->unit_price);
                    $data['vat']                             = 0;
                    $data['vat_type']                        = 0;
                    $data['standard_rate']                   = 0;
                    $data['standard_rate_value']             = 0;
                    $data['zero_rate']                       = 0;
                    $data['zero_rate_value']                 = 0;
                    $data['best_before_date']                = $item->best_before_date;
                    $data['expected_mros']                   = 20;
                    $data['sel_qty']                         = 0;
                    $data['sel_price']                       = 0;
                    $data['landed_product_cost']             = 0;
                    $data['net_selling_price_excluding_vat'] = 0;
                    $data['total_net_selling_price']         = 0;
                    $data['total_net_profit']                = 0;
                    $data['total_net_margin']                = 0;
                    $data['mros']                            = 20;
                    $data['po_import_type']                  = $newOutStandinPO->po_import_type;
                    $subTotal                                += $request->qty * $item->unit_price;
                    if ($newOutStandinPO->po_import_type == 2) {
                        $data['cube_per_box']           = 0;
                        $data['total_num_cubes']        = 0;
                        $data['vat_in_amount']          = 0;
                        $data['import_duty_in_cost']    = 0;
                        $data['delivery_charge']        = 0;
                        $data['landed_price_in_pound']  = 0;
                        $data['itd_vat']                = 0;
                        $data['total_vat']              = 0;
                        $data['currency_exchange_rate'] = 0;
                        $data['import_duty']            = 0;
                    }
                    $poItem = PurchaseOrderProduct::saveItemContent($data); //save the po items
                }
                $model                   = new BookingPODiscrepancy();
                $params                  = ["discrepancy_id" => $request->discrepancy_id, "status" => 6, 'user' => $request->user]; // move to new po
                $model->updateDiscrepancy($params);
                $keepData                = BookingPODiscrepancy::find($request->discrepancy_id);
                //Shubham code writtern here
                $booking_po_product_id   = array();
                $photobooth_qty          = 0;
                $booking_product_details = $keepData->bookingProduct;

                $po_product_details = $keepData->bookingProduct->getPOProductDetails;

                if (!empty($po_product_details)) {
                    $qty_ordered = $po_product_details->total_quantity;
                }

                if ($po_product_details->products == 'normal' || empty($po_product_details->products)) {
                    $booking_po_product_id = array($booking_product_details->id);
                }
                else {

                    $booking_variations_details = BookingPOProducts::where('parent_id', $booking_product_details->id)->get()->toArray();

                    if (!empty($booking_variations_details)) {
                        foreach ($booking_variations_details as $variation_details) {
                            $booking_po_product_id[] = $variation_details['id'];

                            if ($variation_details['is_photobooth'] == 1) {
                                $photobooth_qty = $photobooth_qty + 1;
                            }
                        }
                    }
                }

                $auto_discrepancy_array['booking_product_details'] = $booking_product_details->toArray();
                $auto_discrepancy_array['qty_ordered']             = $qty_ordered;
                $auto_discrepancy_array['location_array']          = $booking_product_details->bookingProductLocationTypeQty($booking_po_product_id);
                if ($photobooth_qty > 0) {
                    $auto_discrepancy_array['location_array']['photobooth_qty'] = $photobooth_qty;
                }
                $booking_product_status = BookingPOProducts::manageAutoDiscrepancies($auto_discrepancy_array);

                // SET BOOKING COMPLETE
                if ($booking_product_status == 1) {
                    Booking::setComplete($booking_product_details->booking_id, true);
                }
                //Shubham code writtern here





                $purchaseOrder               = PurchaseOrder::find($request->po_id);
                $deliveryData                = $purchaseOrder->deliveryDetail();
                $newOutStandinPO->sub_total  = $subTotal;
                $newOutStandinPO->total_cost = $subTotal;
                $newOutStandinPO->save();
                $deliveryHtml                = View::make('purchase-orders._delivery-content', ['bookingProducts' => $deliveryData->bookingProducts, 'deliveryData' => $deliveryData[0], 'purchaseOrder' => $purchaseOrder])->render();
                DB::commit();
                return $this->sendResponse("Move to new PO created.", 200, $deliveryHtml);
            }
            else { //return exception error
                return $this->sendError("Please try again.", 200, $deliveryHtml);
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError($ex->getMessage(), 400);
        }
    }

//calculate after move po product from one po to another po
    function calculateTotalsAfterMovePo($po_id) {
        $purchaseOrder = PurchaseOrder::find($po_id);
        PurchaseOrderProduct::reCalculateItemsData($purchaseOrder);
        $purchaseOrder->fresh();
        DB::commit();
        if ($purchaseOrder->po_import_type == 1) {
            $data['data'] = view('purchase-orders._uk-po-items', compact('purchaseOrder'))->render();
        }
        else {
            $data['data']              = view('purchase-orders._import-po-items', compact('purchaseOrder'))->render();
            $data['total_import_duty'] = $purchaseOrder->total_import_duty;
            $data['total_cost']        = $purchaseOrder->total_cost;
            $data['total_no_of_cubes'] = $purchaseOrder->total_number_of_cubes;
            $data['remaining_space']   = $purchaseOrder->remaining_space;
        }
        $data['sub_total']           = $purchaseOrder->sub_total;
        $data['supplier_min_amount'] = floatval($purchaseOrder->supplier->min_po_amt);
        $data['remaining_amount']    = floatval($purchaseOrder->sub_total - $purchaseOrder->supplier->min_po_amt);
        $data['total_margin']        = $purchaseOrder->total_margin;
        return $data;
    }

    //move Product to existing po
    public
            function moveProductToExistingPo(Request $request) {
        try {
            $poProducts    = explode(",", $request->po_products);
            $successStatus = 0;
            $data          = array();
            foreach ($poProducts as $poProductId) {
                $checkPoProductExist        = PurchaseOrderProduct::find($poProductId);
                // return $checkPoProductExist;
                $checkPoProductExist->po_id = $request->po_id;

                $data['selectedPO'] = $request->po_id;

                $currentPo = $request->current_po;

                if ($checkPoProductExist->save()) {
                    //current po update
                    $this->calculateTotalsAfterMovePo($request->po_id);

                    //po in product moved
                    $data                       = $this->calculateTotalsAfterMovePo($request->current_po);
                    $data['selectedExistingPO'] = $request->po_id;
                    $successStatus++;
                }
            }
            if ($successStatus) {
                return $this->sendResponse('Product Moved Successfully.', 200, $data);
            }
            else {
                return $this->sendResponse('Something went wrong.', 422);
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    //move product to new po
    public
            function moveProductToNewPO(MoveToNewPORequest $request) {
        try {
            $currentPo                    = $request->current_po;
            $storeObjPo                   = new PurchaseOrder();
            $storeObjPo->supplier_id      = $request->supplier_id;
            $supplierContact              = \App\SupplierContact::where('supplier_id', $request->supplier_id)->where('is_primary', 1)->first();
            $storeObjPo->supplier_contact = (empty($supplierContact)) ? NULL : $supplierContact->id;
            $storeObjPo->po_number        = PurchaseOrder::autoGeneratePO();
            $storeObjPo->country_id       = $request->supplier_country_id;
            $storeObjPo->po_import_type   = $request->supplier_po_import_type;
            $storeObjPo->created_by       = $request->user()->id;
            $storeObjPo->modified_by      = $request->user()->id;

            $settings                            = \App\Setting::getData(['vat_rates', 'billing_address']);
            $storeObjPo->billing_street_address1 = isset($settings[2]->column_val) ? $settings[2]->column_val : '';
            $storeObjPo->billing_street_address2 = isset($settings[3]->column_val) ? $settings[3]->column_val : '';
            $storeObjPo->billing_country         = isset($settings[4]->column_val) ? $settings[4]->column_val : '';
            $storeObjPo->billing_state           = isset($settings[5]->column_val) ? $settings[5]->column_val : '';
            $storeObjPo->billing_city            = isset($settings[6]->column_val) ? $settings[6]->column_val : '';
            $storeObjPo->billing_zipcode         = isset($settings[7]->column_val) ? $settings[7]->column_val : '';

            $shipAddress                 = \App\Warehouse::first();
            $storeObjPo->recev_warehouse = !empty($shipAddress) ? $shipAddress->id : '';
            $storeObjPo->warehouse       = !empty($shipAddress) ? $shipAddress->name : '';
            $storeObjPo->street_address1 = !empty($shipAddress) ? $shipAddress->address_line1 : '';
            $storeObjPo->street_address2 = !empty($shipAddress) ? $shipAddress->address_line2 : '';
            $storeObjPo->country         = !empty($shipAddress) ? \App\Country::find($shipAddress->country)->name : '';
            $storeObjPo->state           = !empty($shipAddress) ? \App\State::find($shipAddress->state)->name : '';

            $storeObjPo->city    = !empty($shipAddress) ? \App\City::find($shipAddress->city)->name : '';
            $storeObjPo->zipcode = !empty($shipAddress) ? $shipAddress->zipcode : '';
            if ($storeObjPo->save()) {

                $poProducts = explode(",", $request->po_products);
                foreach ($poProducts as $poProductId) {
                    $this->updatePOProduct($poProductId, $storeObjPo->id);
                    $data = $this->calculateTotalsAfterMovePo($currentPo);
                    return $this->sendResponse('PO Created successfully.', 200, $data);
                }
            }
            else {
                return $this->sendResponse('PO does not created successfully,please try again', 200);
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    //create Po Product
    public
            function updatePOProduct($po_product_id, $po_id) {
        $poProductObj        = PurchaseOrderProduct::find($po_product_id);
        $poProductObj->po_id = $po_id;

        if ($poProductObj->save()) {
            $this->calculateTotalsAfterMovePo($po_id);
        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PO\DeliveryProductRequest $request
     * @return type
     */
    public
            function productLocationDetail(\App\Http\Requests\Api\PO\DeliveryProductRequest $request) {
        try {

            $product      = BookingPOProducts::find($request->po_product_id);
            $deliveryHtml = View::make('purchase-orders._delivery-popup', ['product' => $product, 'po_id' => $request->po_id, 'po_product_id' => $request->po_product_id, 'dis_id' => $request->discrepancy_id])->render();
            return $this->sendResponse("popup detauil found", 200, $deliveryHtml);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function updateProductDiscrepancy(\App\Http\Requests\Api\PO\UpdateProductDisRequest $request) {
        DB::beginTransaction();
        try {
            $keepData = BookingPODiscrepancy::find($request->discrepancy_id);

            if ($request->keep == 0) {

                //Return to supplier
                $previousQty           = $keepData->qty;
                $keepData->qty         = $request->return_to_supplier;
                $keepData->status      = 4;
                $keepData->modified_by = $request->user->id;

                if ($keepData->save()) {
                    $keepData->bookingProduct()->update(['lock_discrepancy' => 1]);

                    //Shubham code writtern here
                    $booking_po_product_id   = array();
                    $photobooth_qty          = 0;
                    $booking_product_details = $keepData->bookingProduct;
                    $po_product_details      = $keepData->bookingProduct->getPOProductDetails;
                    if (!empty($po_product_details)) {
                        $qty_ordered = $po_product_details->total_quantity;
                    }

                    if ($po_product_details->products == 'normal' || empty($po_product_details->products)) {
                        $booking_po_product_id = array($booking_product_details->id);
                    }
                    else {
                        $booking_variations_details = BookingPOProducts::where('parent_id', $booking_product_details->id)->get()->toArray();

                        if (!empty($booking_variations_details)) {
                            foreach ($booking_variations_details as $variation_details) {
                                $booking_po_product_id[] = $variation_details['id'];

                                if ($variation_details['is_photobooth'] == 1) {
                                    $photobooth_qty = $photobooth_qty + 1;
                                }
                            }
                        }
                    }

                    $auto_discrepancy_array['booking_product_details'] = $booking_product_details->toArray();
                    $auto_discrepancy_array['qty_ordered']             = $qty_ordered;
                    $auto_discrepancy_array['location_array']          = $booking_product_details->bookingProductLocationTypeQty($booking_po_product_id);
                    if ($photobooth_qty > 0) {
                        $auto_discrepancy_array['location_array']['photobooth_qty'] = $photobooth_qty;
                    }
                    $booking_product_status = BookingPOProducts::manageAutoDiscrepancies($auto_discrepancy_array);

                    // SET BOOKING COMPLETE
                    if ($booking_product_status == 1) {
                        Booking::setComplete($booking_product_details->booking_id, true);
                    }
                    //Shubham code writtern here
                    DB::commit();
                    $purchaseOrder = PurchaseOrder::find($request->po_id);
                    $deliveryData  = $purchaseOrder->deliveryDetail();
                    $deliveryHtml  = View::make('purchase-orders._delivery-content', ['bookingProducts' => $deliveryData->bookingProducts, 'deliveryData' => $deliveryData[0], 'purchaseOrder' => $purchaseOrder])->render();
                    return $this->sendResponse('Discrepancy added successfully', 200, $deliveryHtml);
                }
                else {
                    DB::rollback();
                    return $this->sendError('Discrepancy not added, please try again', 422);
                }
            }
            else {

                //Keep
                $previousQty           = $keepData->qty;
                $keepData->qty         = $request->keep;
                $keepData->status      = 2;
                $keepData->modified_by = $request->user->id;

                if ($keepData->save()) { // Return to Supplier and insert one more raw
                    if ($keepData->qty !== $previousQty) {
                        $returnToSupplierRaw                     = $keepData->replicate();
                        $returnToSupplierRaw->qty                = $previousQty - $request->keep;
                        $returnToSupplierRaw->status             = 4;
                        $returnToSupplierRaw->created_by         = $request->user->id;
                        $returnToSupplierRaw->modified_by        = $request->user->id;
                        $returnToSupplierRaw->is_added_by_system = 1;
                        $returnToSupplierRaw->save();
                    }
                    $keepData->bookingProduct()->update(['lock_discrepancy' => 1]);
                    //Shubham code writtern here
                    $booking_po_product_id   = array();
                    $photobooth_qty          = 0;
                    $booking_product_details = $keepData->bookingProduct;

                    $po_product_details = $keepData->bookingProduct->getPOProductDetails;

                    if (!empty($po_product_details)) {
                        $qty_ordered = $po_product_details->total_quantity;
                    }

                    if ($po_product_details->products == 'normal' || empty($po_product_details->products)) {
                        $booking_po_product_id = array($booking_product_details->id);
                    }
                    else {

                        $booking_variations_details = BookingPOProducts::where('parent_id', $booking_product_details->id)->get()->toArray();

                        if (!empty($booking_variations_details)) {
                            foreach ($booking_variations_details as $variation_details) {
                                $booking_po_product_id[] = $variation_details['id'];

                                if ($variation_details['is_photobooth'] == 1) {
                                    $photobooth_qty = $photobooth_qty + 1;
                                }
                            }
                        }
                    }

                    $auto_discrepancy_array['booking_product_details'] = $booking_product_details->toArray();
                    $auto_discrepancy_array['qty_ordered']             = $qty_ordered;
                    $auto_discrepancy_array['location_array']          = $booking_product_details->bookingProductLocationTypeQty($booking_po_product_id);
                    if ($photobooth_qty > 0) {
                        $auto_discrepancy_array['location_array']['photobooth_qty'] = $photobooth_qty;
                    }
                    $booking_product_status = BookingPOProducts::manageAutoDiscrepancies($auto_discrepancy_array);

                    // SET BOOKING COMPLETE
                    if ($booking_product_status == 1) {
                        Booking::setComplete($booking_product_details->booking_id, true);
                    }
                    //Shubham code writtern here
                    DB::commit();
                    $purchaseOrder = PurchaseOrder::find($request->po_id);
                    $deliveryData  = $purchaseOrder->deliveryDetail();
                    $deliveryHtml  = View::make('purchase-orders._delivery-content', ['bookingProducts' => $deliveryData->bookingProducts, 'deliveryData' => $deliveryData[0], 'purchaseOrder' => $purchaseOrder])->render();
                    return $this->sendResponse('Discrepancy added successfully', 200, $deliveryHtml);
                }
                else {
                    DB::rollback();
                    return $this->sendError('Discrepancy not added, please try again', 422);
                }
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function deliveryFilters(Request $request) {

        try {
            $purchaseOrder = PurchaseOrder::find($request->po_id);
            $deliveryData  = $purchaseOrder->deliveryDetail($request->filters, $request->dis);
            if ($deliveryData->bookingProducts) {
                $deliveryHtml = View::make('purchase-orders._delivery-content', ['bookingProducts' => $deliveryData->bookingProducts, 'deliveryData' => $deliveryData[0], 'purchaseOrder' => $purchaseOrder])->render();
                return $this->sendResponse('Records Found', 200, $deliveryHtml);
            }
            else {
                return $this->sendError('No Records found', 200);
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

}
