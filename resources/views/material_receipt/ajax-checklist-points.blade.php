@forelse($qc as $qcKey=>$qcVal)
<label><b class="bold mb-2 mt-4 d-block">{{ $qcVal->name }}</b></label><br>

@forelse($qcVal->checklistPoints as $pointKey=>$pointVal)
@php
$pointDetail=\App\BookingQcCheckListPoint::where("qc_option_id",$pointVal->id)->Join('booking_qc_check_lists','booking_qc_check_lists.id','booking_qc_check_list_points.qc_check_list_id')->where('booking_qc_check_lists.product_id',$product_id)->where('booking_qc_check_lists.booking_id',$booking_id)->first();
@endphp
<form class="qc-form" enctype="multipart/form-data" id="qc-form-{{ $product_id }}_{{$pointVal->id}}" method="post" action="{{ route('api-booking-qc.store') }}">
	<div class="d-flex align-items-center">
		<label class="fancy-checkbox">
			<input type="checkbox" name="is_checked" attr-text="title{{ $product_id }}_{{ $pointVal->id }}" class="is_checked" id="is_checked{{ $product_id }}_{{ $pointVal->id }}" value="1" {{ (!empty($pointDetail) ? (($pointDetail->is_checked=="1") ? "checked" : '' ) : '')}} >
			<span class="font-14-dark" id="title{{ $product_id }}_{{ $pointVal->id }}" @if(!empty($pointDetail)) {{ ($pointDetail->is_checked==1) ? 'STYLE=text-decoration:line-through' : '' }}  @endif><i></i>{{ $pointVal->title}}</span>
		</label>
		<a class="btn-view-qc btn-edit expand" style="margin-left: auto;" href="javascript:void(0);" attr-id="{{ $product_id }}_{{$pointVal->id}}" attr-parent-qc="{{ $product_id }}_{{ $pointVal->qc_id }}"><span class="icon-moon size-sm icon-Active"></span></a>
	</div>

	<input type="hidden" name="book_id" class="book_id" value="{{ $booking_id }}">
	<input type="hidden" name="product_id" class="product_id" value="{{ $product_id }}">
	<input type="hidden" name="qc_id" value="{{ $pointVal->qc_id }}">
	<input type="hidden" name="qc_option_id" value="{{ $pointVal->id }}">
	<input type="hidden" name="option_caption" value="{{ $pointVal->title }}">
	@if(!empty($pointDetail))
	<input type="hidden" name="qc_check_list_id" value="{{ $pointDetail->id }}">
	@endif
	
	<div class="form-group" id="detailDiv_{{ $product_id }}_{{$pointVal->id}}" style="display: none">
		<label class="font-14-dark bold mb-2">Comments</label>
		<textarea rows="4" class="form-control" placeholder="comment here" name="comments">{{ (!empty($pointDetail) ? $pointDetail->comments : '') }}</textarea>
	</div>
	<div class="upload-img-container mb-2" id="detailImgDiv_{{ $product_id }}_{{$pointVal->id}}" style="display: none">
		<figure>
			<img src="{{ (!empty($pointDetail) ? $pointDetail->image  :asset('img/no-img-black.png')) }}"  width="100" id="image_qc_{{ $product_id }}_{{$pointVal->id}}" height="100" style="object-fit: contain;">
			<div class="dn-file">
				<input type="file" id="dn_file{{ $product_id }}_{{$pointVal->id}}" name="image{{$pointVal->id}}" class="image_qc" attr-id="image_qc_{{ $product_id }}_{{$pointVal->id}}" accept="image/*" attr-delete-btn="deleteImage{{ $product_id }}_{{$pointVal->id}}" attr-delete-btn-null="deleteImagenull{{ $product_id }}_{{$pointVal->id}}">
				<label for="dn_file{{ $product_id }}_{{$pointVal->id}}">Choose File</label>
			</div>
		</figure>
		@if(!empty($pointDetail))
		@if(!is_null($pointDetail->getOriginal('image')))
		<a class="btn-delete 12" href="javascript:void(0);" id="deleteImage{{ $product_id }}_{{$pointVal->id}}" attr-val="2" onclick="removeImageQc('{{ $pointDetail->id }}','image_qc_{{ $product_id }}_{{$pointVal->id}}','deleteImage{{ $product_id }}_{{$pointVal->id}}')"><span class="icon-moon icon-Delete" title="Delete Image"></span></a>
		@else
		<a class="btn-delete" href="javascript:void(0);" id="deleteImagenull{{ $product_id }}_{{$pointVal->id}}" attr-val="2" onclick="removeImageQcNull('image_qc_{{ $product_id }}_{{$pointVal->id}}','deleteImagenull{{ $product_id }}_{{$pointVal->id}}')" style="display: none"><span class="icon-moon icon-Delete" title="Delete Image" ></span></a>
		@endif
		@else
		<a class="btn-delete" href="javascript:void(0);" id="deleteImage{{ $product_id }}_{{$pointVal->id}}" attr-val="2" onclick="removeImageQcNull('image_qc_{{ $product_id }}_{{$pointVal->id}}','deleteImage{{ $product_id }}_{{$pointVal->id}}')"  ><span class="icon-moon icon-Delete" title="Delete Image"></span></a>
		@endif
		<a class="btn-delete" href="javascript:void(0);" id="deleteImage{{ $product_id }}_{{$pointVal->id}}" attr-val="2" onclick="removeImageQcNull('image_qc_{{ $product_id }}_{{$pointVal->id}}','deleteImage{{ $product_id }}_{{$pointVal->id}}')" style="display: none"  ><span class="icon-moon icon-Delete" title="Delete Image"></span></a>
		<div>
			<button class="btn btn-blue btn-header px-4 submit_btn"  title="@lang('messages.modules.button_save')" form="qc-form-{{ $product_id }}_{{$pointVal->id}}">@lang('messages.modules.button_save')</button>
		</div>
	</div>
	
</form>
@empty
@endforelse
@empty
@endforelse