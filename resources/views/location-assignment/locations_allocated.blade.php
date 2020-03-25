{{--@forelse($object->locationAssign as $key=>$value)
	@php
	$locationData=$value->locations()->first();
	if(in_array($locationData->type_of_location,$showLocationTypeArr) && $locationData->case_pack==1)
	{
		$typeOfLocation="Case - ".LocationType($locationData->type_of_location);
	}
	else
	{
		$typeOfLocation=LocationType($locationData->type_of_location);
	}
	@endphp
	
	<ul>
	
		<li>{{$locationData->location}} - {{ $typeOfLocation }}</li>
		<hr>
	</ul>
	
@empty
@endforelse --}}
{!! $html  !!}