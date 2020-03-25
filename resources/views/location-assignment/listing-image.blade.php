
<!-- <a href="{{ $object->main_image_internal }}" data-rel="lightcase">
	<img src="{{url('/img/img-loading.gif') }}" data-original="{{$object->main_image_internal}}" width="75" height="75" />
</a>
  -->
<p class="p-name">{{$object->title}}</p>
<div class="d-flex"> 
	<div>
		<a href="{{ $object->main_image_internal }}" data-rel="lightcase">
			<img src="{{url('/img/img-loading.gif') }}" data-original="{{$object->main_image_internal}}" width="75" height="75" />
		</a>
	</div>
	<div class="pl-2">
		<p class="mb-2 mt-4">SKU: <span class="bold">{{$object->sku}}</span></p>
		<p>Barcode: <span class="bold">{{$object->product_identifier}}</span></p>
	</div>
</div>

 
 