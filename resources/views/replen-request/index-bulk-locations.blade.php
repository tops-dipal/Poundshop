@if(!is_null($object->total_in_bulk))
@php
$finalArrayBulk = array();

$asArr = explode( ',', $object->total_in_bulk );

foreach( $asArr as $val ){
  $tmp = explode( '-', $val );
  $finalArrayBulk[ $tmp[0] ] = $tmp[1];
}
$sumBulk=array_sum($finalArrayBulk);
@endphp
{{$sumBulk }}({{$object->total_in_bulk_count}}) 
@php
$str1 = implode('<br/>',array_unique(explode('<br/>', $object->bulk_location_list)));
@endphp
<ul class="action-btns">
	<li>
		<a tabindex="0" class="btn-edit" data-placement="left" data-html="true" data-toggle="popover" data-trigger="focus" title="Bulk Locations" data-content="{{$str1}}"><span class="icon-moon icon-Information"></span></a>
	</li>
</ul>
@else
-
@endif