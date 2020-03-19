<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    protected $table = 'product_locations';

    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }

    public function location_details(){
        return $this->belongsTo(Locations::class,'location_id');
    }
}
