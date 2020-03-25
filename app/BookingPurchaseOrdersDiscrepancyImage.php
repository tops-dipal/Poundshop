<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPurchaseOrdersDiscrepancyImage extends Model
{
    protected $table ="booking_purchase_orders_discrepancy_image";

    protected $guarded = [];

    protected $appends = array('fullimageurl');

    public function getFullimageurlAttribute($value) {
    	return $this->attributes['full_image_url'] = !empty($this->attributes['image']) ? url('/storage/uploads/'.$this->attributes['image']) : null;
    }
}
