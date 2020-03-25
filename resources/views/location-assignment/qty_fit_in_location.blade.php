{{--@forelse($object->locationAssign as $key=>$value)
@php
	$locationData=$value->locations()->first();
@endphp
@if(in_array($locationData->type_of_location,$showLocationTypeArr))
<ul>
	<li><input type='text' value='{{ $value->qty_fit_in_location }}' class='form-control qty_fit_in_location' attr-location-assign-id="{{ $value->id }}" id="location_assign_{{ $value->id }}" data-action-url="{{ route('api-location-assignment.update',$value->id) }}" old-data-val="{{ $value->qty_fit_in_location }}"></li>
	<hr>
</ul>
@endif
@empty
@endforelse--}}
{!! $html !!}