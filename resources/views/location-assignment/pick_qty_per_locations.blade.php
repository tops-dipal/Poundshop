{{--@forelse($object->locationAssign as $key=>$value)
@php
	$locationData=$value->locations()->first();
@endphp
@if(in_array($locationData->type_of_location,$showLocationTypeArr))
<ul>
	<li>{{  $value->current_qty }}</li>
	<hr>
</ul>
@endif
@empty
@endforelse --}}
{!! $html  !!}