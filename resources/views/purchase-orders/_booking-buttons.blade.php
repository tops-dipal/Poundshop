@if(!empty($object->booking_date))
<a title="Edit Booking" href="{{route('booking-in.edit',$object->booking_id)}}">{{date('d-M-Y', strtotime($object->booking_date))}}</a>
@else
{{'--'}}
@endif