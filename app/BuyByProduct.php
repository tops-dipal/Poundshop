<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Products;

use App\Range;

use App\PurchaseOrder;

use DB;

class BuyByProduct extends Model
{
    public static function getProductsByBarcode($barcode)
    {
    	$select_array = array(
            'products.id',
            'products.title',
            'products.sku',
            'products.buying_category_id',
            'products.main_image_internal_thumb',
            'product_barcodes.barcode',
            'product_barcodes.product_id',
            'po_products.id as po_id'
        );
        $object = Products::select($select_array);
        $object->leftJoin('product_barcodes', function ($join) {
            $join->on('product_barcodes.product_id', '=', 'products.id');
        });
        $object->leftJoin('po_products', function ($join) {
            $join->on('po_products.product_id', '=', 'products.id');
        });
          $object->where(function($q) use ($barcode) {
            $q->where('product_barcodes.barcode','=',$barcode);
            $q->orWhere('products.product_identifier', $barcode);
            
        });
        
        
        return $object->first();
    }

    
    public static function getExistingPos($supplierId,$status='draft')
    {
        $select_array=array("purchase_order_master.id",DB::raw("DATE_FORMAT(purchase_order_master.created_at, '%d-%b-%Y') as date"),"purchase_order_master.exp_deli_date","purchase_order_master.po_number");
        $object=PurchaseOrder::select($select_array);

        $object->where('purchase_order_master.po_status','1');
        $object->where('purchase_order_master.supplier_id',$supplierId);
        $object->selectRaw('SUM(po_products.total_quantity*po_products.unit_price) as total_cost');
        $object->selectRaw('COUNT(po_products.id) as total_num_items');
        $object->leftJoin('po_products', function ($join) {
            $join->on('po_products.po_id','purchase_order_master.id');
        });
        $object->groupBy('purchase_order_master.po_number');
        $object->orderBy('purchase_order_master.id','desc');
        $object->distinct();
        
        return $object->paginate(50);
    }
}
