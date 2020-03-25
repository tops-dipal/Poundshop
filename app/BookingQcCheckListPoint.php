<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingQcCheckListPoint extends Model
{

	 protected $fillable = ['image'];
    public
            function getImageAttribute() {
        if (!empty($this->attributes['image']))
            return url('/storage/uploads') . '/' . $this->attributes['image'];
        else
            return url('/img/no-img-black.png');
    }

    public function bookingQCChecklist()
    {
        return $this->belongsTo('App\BookingQcChecklist','qc_check_list_id');
    }


}
