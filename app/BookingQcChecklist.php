<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingQcChecklist extends Model
{
    protected $table = "booking_qc_check_lists";

    protected $guarded = [];

    public function products()
    {
        return $this->belongsTo('App\Products','product_id');
    }

    public function bookingQCChecklistPoints()
    {
    	return $this->hasMany('App\BookingQcCheckListPoint','qc_check_list_id');
    }
   
    
}
