<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PurchaseOrder;
use App\PurchaseOrderRevises;
use Illuminate\Support\Facades\View;

class PurchaseOrderRevisesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function index(Request $request) {
        try {

            $columns = [
                0 => 'id',
                1 => 'created_at',
            ];
            $params  = array(
                'order_column'    => $columns[$request->order[0]['column']],
                'order_dir'       => $request->order[0]['dir'],
                'purchaseOrderId' => $request->purchase_order_id,
            );

            $revisions = PurchaseOrderRevises::getAllContent($request->length, $params);

            $data = [];
            if (!empty($revisions)) {
                foreach ($revisions as $result) {
                    $tempArray        = array();
                    $tempArray[]      = $result->purchase_order_number_sequence . '-R' . $result->sequence_number;
                    $tempArray[]      = $result->created_at;
                    $viewActionButton = View::make('purchase-orders.view-buttons', ['object' => $result]);
                    $tempArray[]      = $viewActionButton->render();
                    $data[]           = $tempArray;
                }
            }

            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $revisions->total(), // Total number of records
                "recordsFiltered" => $revisions->total(),
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
            function store(\App\Http\Requests\Api\PO\StoreReviseRequest $request) {
        try {

            //Here Trigger will execute after completion
            $purchaseOrder = PurchaseOrder::find($request->po_id);
            if (!empty($purchaseOrder)) {
                $purchaseOrder->po_cancel_date = null;
                $purchaseOrder->po_updated_at  = \Carbon\Carbon::now();
                $purchaseOrder->save();
                $purchaseOrder->purchaseOrderCountry->toArray();
                $purchaseOrder->supplier->toArray();
                $purchaseOrder->supplierContact->toArray();
                foreach ($purchaseOrder->product as $item) {
                    $item->products->toArray();
                }
                $revisesObj = new PurchaseOrderRevises;
                $data       = [
                    'purchase_order_id'              => $purchaseOrder['id'],
                    'purchase_order_number_sequence' => $purchaseOrder['po_number'],
                    'purchase_order_content'         => $purchaseOrder->toArray(),
                    'created_by'                     => $request->user()->id,
                    'modified_by'                    => $request->user()->id,
                ];
                if ($revisesObj->saveRevises($data)) {

                    return $this->sendResponse(trans('messages.purchase_order_messages.po_revision_success'), 200, $data);
                }
                else {
                    return $this->sendError(trans('messages.purchase_order_messages.revision_error'), 200);
                }
            }
            else {
                return $this->sendError(trans('messages.purchase_order_messages.not_found'), 200);
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
            function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function destroy($id) {
        //
    }

}
