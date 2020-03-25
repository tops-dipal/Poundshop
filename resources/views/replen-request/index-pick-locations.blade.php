@if(!is_null($object->total_in_pick))
@php
$finalArray = array();

$asArr = explode( ',', $object->total_in_pick );

foreach( $asArr as $val ){
  $tmp = explode( '-', $val );
  $finalArray[ $tmp[0] ] = $tmp[1];
}
$sumPick=array_sum($finalArray);
@endphp
{{$sumPick }}({{$object->total_in_pick_count}}) 
@php
$str = implode('<br/>',array_unique(explode('<br/>', $object->pick_location_list)));
@endphp
<ul class="action-btns">
	<li>
		<a tabindex="0" class="btn-edit" data-placement="left" data-html="true" data-toggle="popover" data-trigger="focus" title="Pick Locations" data-content="{{$str}}"><span class="icon-moon icon-Information"></span></a>
	</li>
</ul>
@else
-
@endif