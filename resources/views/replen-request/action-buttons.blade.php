<ul class="action-btns">
	@php
	$btnTitle='Override'
	@endphp
	@if(!empty($object->override_by_users_list))
	<li>
		<a tabindex="0" class="btn-edit" data-placement="left" data-html="true" data-toggle="popover" data-trigger="focus" title="Overide By" data-content="{{$object->override_by_users_list}}"><span class="icon-moon icon-Information"></span></a>
	</li>
		@php
		$btnTitle="Edit Override"
		@endphp
	
	@endif
	<li>
		<a onclick="showOverrideModel('{{ $object }}','{{$stockTotal}}')" class="btn-edit" data-placement="left"  title="{{ $btnTitle }}" ><span class="icon-moon icon-Edit"></span></a>
	</li>
	<input type="hidden" name="stock_total_{{ $object->id }}" class="stock_total_{{ $object->id }}" value="{{$stockTotal}}">
</ul>