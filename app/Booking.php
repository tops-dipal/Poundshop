<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Booking extends Model
{
 	protected  $table = "bookings";
    public static function getBookings($perPage = '',$params = array())
    {

    	 $object=self::select();
    	 $object->where('start_date', '>=', Carbon::now()->startOfWeek())->where('start_date', '<=', Carbon::now()->endOfWeek());
    	 return $object->paginate($perPage);
/*
		$object=self::select();
		$object->where('start_date', '>', Carbon::now()->startOfWeek())->where('start_date', '<', Carbon::now()->endOfWeek());*/
		return $object->paginate($perPage);
    	
    }

    public static function getBookingsDaywise($perPage = '',$params = array())
    {
		$object=self::select('bookings.*','slots.from as slot_from','slots.to as slot_to','supplier_master.name as supplier_name');		
		$object->selectRaw('GROUP_CONCAT(purchase_order_master.po_number SEPARATOR "<br/>") as po_list');	
		$object->where('book_date',$params['book_date']);	
		$object->LeftJoin('slots', 'slots.id', '=', 'bookings.slot_id');	
		$object->LeftJoin('supplier_master', 'supplier_master.id', '=', 'bookings.supplier_id');
		$object->LeftJoin('booking_purchase_orders', 'booking_purchase_orders.booking_id', '=', 'bookings.id');
		$object->LeftJoin('purchase_order_master', 'purchase_order_master.id', '=', 'booking_purchase_orders.po_id');
			
		return $object->paginate($perPage);
    }
}
