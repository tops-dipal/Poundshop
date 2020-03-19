<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PO\CreateRequest;
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
                6 => 'purchase_order_master.created_at',
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
                    $tempArray[]      = View::make('components.list-checkbox', ['object' => $result])->render();
                    $tempArray[]      = View::make('purchase-orders._color-code-listing', ['object' => $result, 'column' => 'title'])->render();
                    $tempArray[]      = $result->supplier_order_number;
                    $tempArray[]      = ucwords($result->name);
                    $tempArray[]      = '<span style="float:right;">' . trans('messages.common.pound_sign') . priceFormate(PurchaseOrderProduct::calculateSubTotal($result->product)) . "</span>";
                    $tempArray[]      = View::make('purchase-orders._color-code-listing', ['object' => $result, 'column' => 'po_status'])->render();
                    $tempArray[]      = $result->created_at->format('d-M-Y h:i A');
                    $viewActionButton = View::make('purchase-orders.action-buttons', ['object' => $result]);
                    $tempArray[]      = $viewActionButton->render();
                    return $tempArray;
                });
            }

            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => @count($purchaseOrders), // Total number of records
                "recordsFiltered" => @count($purchaseOrders),
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
            if ($purchaseOrder->hidden_country == 230) {
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


                $purchaseOrder->po_status     = $request->po_status;
                $purchaseOrder->exp_deli_date = $request->exp_deli_date;

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
        if ($po->po_status !== 10) {
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
            if ($purchaseOrder->po_status !== 10) {
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
                    $item          = (array) $item;
                    $item['po_id'] = $request->po_id;
                    if (isset($item['id']) && !empty($item['id'])) { //prepared update po items
                        $updatedIds[]  = $item['id'];
                        $updateItems[] = PurchaseOrderProduct::preparedUpdateItems($item); //save the po items
                    }
                    else { //prepared new content
                        if (empty($item['product_id'])) {
                            $item['product_id'] = $newAddedProductIds[$newProductkey];
                            $newProductkey++;
                        }


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
                0 => 'purchase_order_master.po_number',
                1 => 'purchase_order_master.po_date',
                2 => 'purchase_order_master.supplier_id',
                3 => 'purchase_order_master.po_import_type',
                4 => 'po_products.itd_vat',
                5 => 'po_products.import_duty_in_amount',
                6 => 'po_products.vat',
                7 => 'po_products.vat',
                8 => 'po_products.zero_rate_value',
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
                foreach ($mapping->getCollection() as $key => $value) {

                    $total_vat_on_uk = $total_vat_on_uk + $value->product()->sum('vat_in_amount');

                    $total_vat_on_import = $total_vat_on_import + $value->product()->sum('import_duty_in_amount');

                    $total_vat         = $total_vat + $value->product()->sum('vat');
                    $total_tax         = $total_tax + $value->total_import_duty + $value->product()->sum('vat');
                    $total_import_duty = $total_import_duty + $value->total_import_duty;
                }
                // echo gettype($total_vat);exit;
                $data = $mapping->getCollection()->transform(function ($result) use ($data) {
                    $tempArray   = array();
                    /* $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render(); */
                    $tempArray[] = '<div class="min-h-35">' . $result->po_number . '</div>';
                    $tempArray[] = $result->po_date;
                    $tempArray[] = ucwords($result->name);
                    $tempArray[] = ($result->po_import_type == 1) ? "UK PO" : "Import PO";
                    $tempArray[] = $result->product()->sum('itd_vat');
                    $tempArray[] = ( $result->po_import_type == 2) ? $result->product()->sum('import_duty_in_amount') : '-';
                    $tempArray[] = ( $result->po_import_type == 2) ? $result->product()->sum('vat') : '-';
                    $tempArray[] = ($result->po_import_type == 1) ? $result->product()->sum('vat') : '-';
                    $tempArray[] = $result->product->sum('zero_rate_value');


                    return $tempArray;
                });
            }

            $jsonData = [
                "draw"                => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"        => $mapping->total(), // Total number of records
                "recordsFiltered"     => $mapping->total(),
                "data"                => $data, // Total data array,
                "total_vat"           => $total_vat,
                "total_tax"           => $total_tax,
                "total_import_duty"   => $total_import_duty,
                "total_vat_on_import" => $total_vat_on_import,
                "total_vat_on_uk"     => $total_vat_on_uk
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
                $item->landed_product_cost             = !empty($item->total_quantity) ? ($item->itd_vat / $item->total_quantity) : 0;
                $item->landed_price_in_pound           = !empty($item->currency_exchange_rate) && $item->currency_exchange_rate != 0 ? ($item->landed_product_cost / $item->currency_exchange_rate) : 0;
                $item->net_selling_price_excluding_vat = ($item->sel_price / (100 + $item->vat)) * 100;
                $item->total_net_selling_price         = ($item->net_selling_price_excluding_vat * $item->total_quantity) / $item->sel_qty;
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

}
