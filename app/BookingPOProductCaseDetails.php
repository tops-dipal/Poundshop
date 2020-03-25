<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPOProductCaseDetails extends Model
{
    protected $table = "booking_po_product_case_details";

    protected $guarded=[];

    public function caseLocations()
    {
    	return $this->hasMany(BookingPOProductLocation::class, "case_detail_id")->with('locationDetails');
    }

    public function innerCases()
    {
    	return $this->hasMany(self::class, "parent_outer_id")->where('case_type', '2')->with('caseLocations');
    }

    public static function bookingProductCasedetails($bookingProductsIds)
    {
        $self_object = self::select(
                                        'booking_po_product_case_details.*',
                                    );
        $self_object->whereIn('booking_po_product_id', $bookingProductsIds);
        $self_object->whereIn('case_type', array('1','3'));
        $self_object->with('caseLocations', 'innerCases');
        return $self_object->get()->toArray();
    }
}
