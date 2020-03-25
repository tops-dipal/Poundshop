<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPallet extends Model
{
    //

     public function booking()
    {
        return $this->belongsTo('App\Booking','booking_id');
    }

    public function pallet()
    {
    	return $this->belongsTo('App\Pallet','pallet_id');
    }
}
