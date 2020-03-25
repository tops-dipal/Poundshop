@forelse($productInfo as $productInfoKey=>$productInfoVal)
<div class="form-group product_qc" id="divProduct{{$productInfoVal->id}}" attr-product-id="{{  $productInfoVal ->id }}">
    <h3 class="title mb-3">QC Checklist For Product {{ $productInfoVal->title }}</h3>
    @php
    $selectedQC=$productInfoVal->bookingQCChecklist()->pluck('qc_list_id')->toArray();
    @endphp
	<div class="form-group mb-0">
		<label class="font-12-dark mb-2">Select Checklist</label>
		<div>
			<select class="form-control custom-select-search qc_list_dropdown" multiple="" attr-div="checklist_points{{ $productInfoVal->id}}" attr-product-id="{{  $productInfoVal ->id }}">
				@forelse($qc_list as $qcKey=>$qcVal)
                <option value="{{ $qcVal->id }}" {{ in_array($qcVal->id,$selectedQC) ?  "selected=selected" : ''}}>{{ $qcVal->name }}</option>
                @empty
                @endforelse
			</select>
		</div>
	</div>
	<div class="form-group" id="checklist_points{{$productInfoVal->id}}">
		
	</div>
</div>
@empty
@endforelse