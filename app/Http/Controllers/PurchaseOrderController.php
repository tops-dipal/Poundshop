<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupplierMaster,App\WareHouse;
use Carbon\Carbon;
use Route;

class PurchaseOrderController extends Controller
{
    function __construct()
    {
//        $this->middleware('permission:po-list|po-create|po-edit|po-delete', ['only' => ['index','store']]);
//
//        $this->middleware('permission:po-create', ['only' => ['create','store']]);
//
//        $this->middleware('permission:po-edit', ['only' => ['edit','update']]);
//
//        $this->middleware('permission:po-delete', ['only' => ['destroy']]);
        
         //$this->middleware('signed',['only'=>['edit']]);
       
    }
    
    /**
     * Display a listing of the resource.
     * @author Hitesh Tank
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return view('purchase-orders.index');
    }

    /**
     * Show the form for creating a new resource.
     * @author : Hitesh Tank
     * @date : 23 Nov
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers= SupplierMaster::getAllSupplierList();
        $poNumber=\App\PurchaseOrder::autoGeneratePO();
        $wareHouses= WareHouse::getWareHouse();
        $countries = \App\Country::all();
        $settings=\App\Setting::getData(['vat_rates','billing_address']);
        
        return view('purchase-orders.create',compact('suppliers','poNumber','wareHouses','countries','settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @author : Hitesh Tank
     * @date : 23 Nov
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $purchaseOrder=\App\PurchaseOrder::find($id);
            $suppliers= SupplierMaster::getAllSupplierList();
            $wareHouses= WareHouse::getWareHouse();
            $countries = \App\Country::all();
            $settings=\App\Setting::getData(['vat_rates','billing_address']);
            if($purchaseOrder){
                return view('purchase-orders.edit',compact('purchaseOrder','suppliers','wareHouses','countries','settings'));
            }else{
                abort('404');
            }
        } catch (Exception $ex) {
                abort('404');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @author Hitesh Tank
     * @param type $id
     * @return type
     */
    
    public function viewRevision($id){
        try{
            if(!empty($id))
            {
                $revisionData=\App\PurchaseOrderRevises::find($id);   
                if($revisionData){
//                    dd($revisionData->purchase_order_content);
                    return view('purchase-orders.revision-view',compact('revisionData'));
                }else{
                    abort(403);
                }
            }else{
                abort(403);
            }
        } catch (Exception $ex) {
                abort(403);
        }
    }

    public function taxPaymentReport(Request $request)
   {
        $suppliers=\App\SupplierMaster::get();
        $countries=\App\Country::get();
        return view('purchase-orders.tax-payment-report',compact('countries','suppliers'));
   }



}
