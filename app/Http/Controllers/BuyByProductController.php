<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BuyByProduct;

use App\Range;

use App\SupplierMaster;

class BuyByProductController extends Controller
{
    public function index()
    {
    	return view('buy-by-product.index');
    }

    public function serachByBarcode(Request $request)
    {
    	$barcode=$request->barcode;
    	$productData=BuyByProduct::getProductsByBarcode($barcode);
    	$supplierList=SupplierMaster::select('id','name','country_id')->get();
        $selectedSupplier=0;

        // get default product supplier
        if(!empty($productData))
        {
            $productInfo=\App\Products::find($productData->id);
            $defaultSupplier=$productInfo->supplier()->where('is_default',1)->first();
            if(!empty($defaultSupplier))
            {       
                $selectedSupplier=$defaultSupplier->supplier_id;
            }
        }

    	$productStatus=(!empty($productData)) ? 1 : 0;
    	return view('buy-by-product.search_barcode_result',compact('productData','barcode','productStatus','supplierList','selectedSupplier'));
    }
}
