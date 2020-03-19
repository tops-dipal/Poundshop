
@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">

@php
    $productImagesCount = $result->productImages->count();
@endphp

<!-- Images -->
<input type="hidden" name="addMoreURL" id="addMoreURL" value="{{ route('add-more-image') }}">
<input type="hidden" name="remove-img-url" id="remove-img-url" value="{{ route('delete-image') }}">
<input type="hidden" name="user_id"  value="{{ Auth::user()->id }}">

<div class="form-group row">
    <label for="inputPassword" class="col-lg-2 col-form-label">@lang('messages.inventory.request_a_new_photo')</label>
    <div class="col-lg-8 pt-2">
        <label class="fancy-radio mr-4">
            <input type="radio" name="is_request_new_photo" value="1"@if(!is_null($result->is_request_new_photo)) {{ ($result->is_request_new_photo==1) ? 'checked':'' }}@endif>
            <span><i></i>Yes</span>        
        </label>
        <label class="fancy-radio">
            <input type="radio" name="is_request_new_photo" value="0" @if(!is_null($result->is_request_new_photo)) {{ ($result->is_request_new_photo==0) ? 'checked':'' }} @else {{ 'checked' }} @endif>
             <span><i></i>No</span>        
        </label>
    </div>
</div>

<div class="productImages row">
    <div class="col-lg-6 ">
        <div class="form-group row">
            <label for="inputPassword" class="col-lg-8 col-form-label">Image For Internal Use </label>
            <div class="col-lg-6 mb-2" >
                    @php
                        $ext = pathinfo($result->main_image_internal, PATHINFO_EXTENSION);

                    @endphp
                    @if($ext!="mp4")

                    <img src="{{url('/img/img-loading.gif') }}" data-original="{{ asset($result->main_image_internal_thumb) }}" class="thumbnail1" style="max-width:100%; height:150px;" id="InternalImgPreview"/>

                     <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" class="InternalVideoPreview hidden">
                        <source src="{{ $result->main_image_internal }}#t=0.5" type="video/mp4">
                    </video>
                    @else
                    <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" class="InternalVideoPreview">
                        <source src="{{ asset($result->main_image_internal) }}#t=0.5" type="video/mp4">
                    </video>
                    <img src="{{url('/storage/uploads/product-images/no-image.jpeg') }}" class="thumbnail hidden" style="max-width:100%; height:150px;" id="InternalImgPreview"/>
                    @endif
              
            </div>
            
            <div class="col-lg-8">
                <div class="fancy-file">
                    <input type="file" class="inputfile-custom" id="main_image_internal" placeholder="" name="main_image_internal">
                    <label for="main_image_internal"><span></span> <strong>Choose file</strong></label>
                </div>
            </div>
            <div class="col-lg-4">
                <button type="button" class="btn btn-danger" id="internal_img_remove">&times;</button>                
            </div>
        </div>
    </div>
    <div class="col-lg-6 ">
        <div class="form-group row">
           <label for="inputPassword" class="col-lg-8 col-form-label">Image For Magento</label>

            <div class="col-lg-8 mb-2" >
                @if(!empty($result->main_image_marketplace)) 
                @php
                $ext = pathinfo($result->main_image_marketplace, PATHINFO_EXTENSION);
                @endphp
                @if($ext!="mp4")
                <img src="{{url('/img/img-loading.gif') }}" data-original="{{ asset($result->main_image_marketplace_thumb) }}" class=" thumbnail" style="max-width:100%; height:150px;" id="magentoimagePreview"/>
                 <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" class="magentoVideoPreview hidden">
                    <source src="" type="video/mp4">
                </video>

                @else
                <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" class="magentoVideoPreview">
                    <source src="{{ asset($result->main_image_marketplace) }}#t=0.5" type="video/mp4">
                </video>
                 <img src="{{url('/storage/uploads/product-images/no-image.jpeg') }}" class=" thumbnail hidden" style="max-width:100%; height:150px;" id="magentoimagePreview"/>
                @endif
            @else
             <img src="{{url('/img/img-loading.gif') }}" data-original="{{ asset($result->main_image_marketplace_url) }}" class=" thumbnail" style="max-width:100%; height:150px;" id="magentoimagePreview"/>
            @endif
            </div>
            <div class="col-lg-8">
                <div class="fancy-file">
                    <input type="file" class="inputfile-custom" id="main_image_marketplace" placeholder="" name="main_image_marketplace" >
                    <label for="main_image_marketplace"><span></span> <strong>Choose file</strong></label>
                </div>
                
            </div>
            <div class="col-lg-4">                
                <button type="button" class="btn btn-danger" id="magento_img_remove">&times;</button>
            </div>
        </div>
    </div>
    @if($productImagesCount == 0)
    <input type="hidden" name="totalUploadedImages" id="totalUploadedImages" value="0">
	<div class="col-lg-6 otherImages">
        <div class="form-group row">
            <label for="inputPassword" class="col-lg-8 col-form-label">Other Magento Image</label>
        </div>
    </div>
    <div class="col-lg-12 input-images"></div>
    @else
        @php $imgCount=1; @endphp
        <input type="hidden" name="totalUploadedImages" id="totalUploadedImages" value="{{ count($result->productImages) }}">
        <div class="col-lg-12 ">
            <div class="form-group row">
            @forelse($result->productImages as $imgKey=>$imgVal)
                @if($imgCount==1)
                    <label for="inputPassword" class="col-lg-12 col-form-label">Other Magento Image</label>
                @endif
                @if(!empty($imgVal->image_thumb))
                <div class="imagePreview col-lg-2" >
                    @if (file_exists(Storage::path($imgVal->image_thumb)))
                        @php
                        $ext = pathinfo($imgVal->image, PATHINFO_EXTENSION);
                        @endphp
                        @if($ext!="mp4")
                        <img src="{{url('/img/img-loading.gif') }}" data-original="{{ asset('storage/uploads/'.$imgVal->image_thumb) }}" class=" thumbnail" style="max-width:150px; max-height:150px;" id="imagePreview_{{ $imgCount }}" />
                        <!--  <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" id="videoPreview_{{ $imgCount }}" class="hidden">
                            <source src="" type="video/mp4">
                        </video> -->
                        @else
                        <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" id="videoPreview_{{ $imgCount }}">
                            <source src="{{ asset('storage/uploads/'.$imgVal->image_thumb) }}#t=0.5" type="video/mp4">
                        </video>
                        <!-- <img src="{{url('/storage/uploads/product-images/no-image.jpeg') }}" class=" thumbnail hidden" style="max-width:150px; max-height:150px;" id="imagePreview_{{ $imgCount }}" /> -->
                        @endif
                    @else
                        <img src="{{url('/storage/uploads/product-images/no-image.jpeg') }}" class=" thumbnail" style="max-width:150px; max-height:150px;" id="imagePreview_{{ $imgCount }}" />
                    @endif
                    <div class="remove_{{ $imgCount }}">
                        <input type="hidden" name="removeImageId_{{ $imgCount }}" id="removeImageId_{{ $imgCount }}" value="{{ $imgVal->id }}">
                        <button type="button" class="btn btn-danger removeImage" id="removeImage_{{ $imgCount }}">X</button>
                    </div>
                </div>
                @endif
                @if(!empty($imgVal->image_url))
                <div class="imagePreview col-lg-2" >
                     <img src="{{url('/img/img-loading.gif') }}" data-original="{{ $imgVal->image_url }}" class=" thumbnail" style="max-width:150px; max-height:150px;" id="imagePreview_{{ $imgCount }}" />
                      <div class="remove_{{ $imgCount }}">
                        <input type="hidden" name="removeImageId_{{ $imgCount }}" id="removeImageId_{{ $imgCount }}" value="{{ $imgVal->id }}">
                        <button type="button" class="btn btn-danger removeImage" id="removeImage_{{ $imgCount }}">X</button>
                    </div>
                </div>
                @endif
                @php $imgCount++;  @endphp

            @empty
            @endforelse
        </div>
   </div>
    <div class="col-lg-12 input-images"></div>
    @endif
    
</div>



