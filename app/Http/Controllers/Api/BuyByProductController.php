<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BuyByProduct;
use App\SupplierMaster;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use App\Products;
use App\ProductBarcode;
use App\SupplierContact;
use App\Setting;
use Session;
use DB;
use Cookie;
use App\Http\Requests\Api\Common\CreateRequest;

class BuyByProductController extends Controller
{
    function __construct(Request $request) {
        CreateRequest::$roles_array = [];
    }
    
    public function index(Request $request)
    {
    	$barcode=$request->barcode;
    	$productData=BuyByProduct::getProductsByBarcode($barcode);
    	$supplierList=SupplierMaster::select('id','name','country_id')->get();
    	$productStatus=(!empty($productData)) ? 1 : 0;
    	return response()->json(['view' => view('buy-by-product.search_barcode_result',compact('productData','barcode','productStatus','supplierList'))->render()]); 
    }

    //get supplier's existing drafted pos
    public function getExistingPoOfSupplier(Request $request)
    {
        try{
    	   $existingPOs=BuyByProduct::getExistingPos($request->supplier_id);
           $selectedPO=Session::get('selectedPO');
           if ($existingPOs) {
               
                if (!empty($existingPOs) && @count($existingPOs) > 0) {

                    $existingPOs = makeNulltoBlank($existingPOs->toArray());
                    $data['data']=$existingPOs['data'];
                    $data['selectedPO']=$selectedPO;
                    return $this->sendResponse('PO listed', 200, $data);
                }
                else {
                    return $this->sendError("No 'Draft' PO associated with this supplier.", 200);
                }
            }
            else {
                return $this->sendError("No 'Draft' PO associated with this supplier.", 422);
            }
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }


    //add Product to existing po
    public function addProductToExistingPo(CreateRequest $request)
    {
        try{
            if(!empty($request->product_id) && !is_null($request->product_id))
            {
                $checkPoProductExist=PurchaseOrderProduct::where('po_id',$request->po_id)->where('product_id',$request->product_id)->first();

                if(empty($checkPoProductExist))
                {
                    $storeObj=new PurchaseOrderProduct();
                }
                else
                {
                    $storeObj=$checkPoProductExist;
                    return $this->sendError(trans('messages.buy_by_product.product_already_in_po'), 422);
                }
            }
            else
            {
               $storeObj=new PurchaseOrderProduct();
            }
            Session::put('selectedPO',$request->po_id);
            $storeObj->po_id=$request->po_id;
            $storeObj->expected_mros=10;
            $storeObj->mros=20;
            $storeObj->barcode=$request->barcode;
            //check product is alredy exists or not
            if(!empty($request->product_id) && !is_null($request->product_id))
            {
                $storeObj->product_id=$request->product_id;
            }
            else
            {
                $storeObj->product_id=$this->createProduct($request);
            }
            if($storeObj->save())
            {
                $this->calculateTotalsAfterMovePo($request->po_id);
                $productInfo=Products::find($storeObj->product_id);
                $data['productInfo']=$productInfo;
                $data['selectedPO']=$request->po_id;
                return $this->sendResponse('Product successfully added to Po.', 200,$data);
            }
            else
            {
                return $this->sendResponse('Product does not successfully added to Po,please try again', 200);
            }
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }


    //create Po and add product to Po
    public function createPo(CreateRequest $request)
    {
        try{
        
            $storeObjPo=new PurchaseOrder();
            $storeObjPo->supplier_id=$request->supplier_id;
            $supplierContact=SupplierContact::where('supplier_id',$request->supplier_id)->where('is_primary',1)->first();
            $storeObjPo->supplier_contact=(empty($supplierContact))? NULL : $supplierContact->id;
            $storeObjPo->po_number=PurchaseOrder::autoGeneratePO();
            $storeObjPo->country_id=$request->country_id;
            $storeObjPo->po_import_type=$request->po_import_type;
            $storeObjPo->created_by=$request->user()->id;
            $storeObjPo->modified_by=$request->user()->id;

            $settings   = \App\Setting::getData(['vat_rates', 'billing_address']);
            $storeObjPo->billing_street_address1=isset($settings[2]->column_val) ? $settings[2]->column_val : '';
            $storeObjPo->billing_street_address2=isset($settings[3]->column_val) ? $settings[3]->column_val : '';
            $storeObjPo->billing_country=isset($settings[4]->column_val) ? $settings[4]->column_val : '';
            $storeObjPo->billing_state=isset($settings[5]->column_val) ? $settings[5]->column_val : '';
            $storeObjPo->billing_city=isset($settings[6]->column_val) ? $settings[6]->column_val : '';
            $storeObjPo->billing_zipcode=isset($settings[7]->column_val) ? $settings[7]->column_val : '';

            $shipAddress=\App\Warehouse::where('is_default',1)->first();
             $storeObjPo->recev_warehouse=!empty($shipAddress) ? $shipAddress->id : '';
            $storeObjPo->warehouse=!empty($shipAddress) ? $shipAddress->name : '';
            $storeObjPo->street_address1 =!empty($shipAddress) ? $shipAddress->address_line1 : '';
            $storeObjPo->street_address2=!empty($shipAddress) ? $shipAddress->address_line2 : '';
            $storeObjPo->country=!empty($shipAddress) ? \App\Country::find($shipAddress->country)->name : '';
            $storeObjPo->state=!empty($shipAddress) ? \App\State::find($shipAddress->state)->name : '';

            $storeObjPo->city=!empty($shipAddress) ? \App\City::find($shipAddress->city)->name : '';
            $storeObjPo->zipcode=!empty($shipAddress) ? $shipAddress->zipcode : '';
            if($storeObjPo->save())
            {
                $productInfo=$this->createPOProduct($request,$storeObjPo->id);
                $editPoURL=route('purchase-orders.edit',$storeObjPo->id).'#items/';
                $data=array();
                $cookie=Cookie::make('comesFrom', 'Buyer-Enquiry');

                $data['editPoURL']=$editPoURL;
                $data['productInfo']=$productInfo;
                    return $this->sendResponse('PO Created successfully.', 200,$data)->withCookie($cookie);;
            }
            else
            {
                return $this->sendResponse('PO does not created successfully,please try again', 200);
            }
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    //create product
    public function createProduct(CreateRequest $request)
    {
        try{
            $storeObj=new Products();
            $storeObj->sku=get_sku();
            $storeObj->created_by=$request->user()->id;
            $storeObj->modified_by=$request->user()->id;
            if($storeObj->save())
            {
                $this->createProductBarcode($request,$storeObj->id);
                return $storeObj->id;
            }
            else
            {
               return 0;
            }
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    //create product barcode
    public function createProductBarcode(CreateRequest $request,$product_id)
    {
        $productBarcode=ProductBarcode::where('barcode',$request->barcode)->first();
        if(empty($productBarcode))
        {
            $barcodeObj=new ProductBarcode();
            $barcodeObj->product_id=$product_id;
            $barcodeObj->barcode=$request->barcode;
            $barcodeObj->created_by=$request->user()->id;
            $barcodeObj->modified_by=$request->user()->id;
            if($barcodeObj->save())
            {
                return $barcodeObj->id;
            }
        }
        else
        {
            return $productBarcode->id;
        }
        
    }

    //create Po Product
    public function createPOProduct(Request $request,$po_id)
    {
        $poProductObj=new PurchaseOrderProduct();
        $poProductObj->po_id=$po_id;
        $poProductObj->barcode=$request->barcode;
        $poProductObj->expected_mros=10;
        $poProductObj->mros=20;
        if(!empty($request->product_id) && !is_null($request->product_id))
        {
            $poProductObj->product_id=$request->product_id;
        }
        else
        {
            $poProductObj->product_id=$this->createProduct($request);
        }
        if($poProductObj->save())
        {
             $this->calculateTotalsAfterMovePo($po_id);
            $productInfo=Products::find($poProductObj->product_id);
            return $productInfo;
        }
    }


     function calculateTotalsAfterMovePo($po_id)
    {
        $purchaseOrder = PurchaseOrder::find($po_id);
        PurchaseOrderProduct::reCalculateItemsData($purchaseOrder);
        $purchaseOrder->fresh();
        DB::commit();
    }
}
