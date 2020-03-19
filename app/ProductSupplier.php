<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\SupplierMaster;

class ProductSupplier extends Model
{
    protected	$table = "product_supplier";

    protected $fillable = [
    						'product_id',	
    						'supplier_id',	
    						'supplier_sku',	
    						'price_per_case',	
    						'quantity',	
    						'quantity_per_case',	
    						'min_order_quantity',	
    						'available',	
    						'is_default',	
    						'note',	
    						'created_by',	
    						'modified_by',	
    					  ];

    public function supplier()
    {
    	return $this->belongsTo(SupplierMaster::class,'supplier_id');
    }					  
}
