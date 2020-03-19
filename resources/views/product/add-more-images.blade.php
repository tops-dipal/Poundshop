<div class="col-lg-6" id="imgUpload_{{ $nextCount }}">
    <div class="form-group row">
       
        <div class="imagePreview col-lg-8" >
        	  <img src="{{ asset('storage/uploads/product-images/no-image.jpeg') }}" class=" thumbnail" style="max-width:150px; max-height:150px;" id="imagePreview_{{ $nextCount }}"/>
                 <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" id="videoPreview_{{ $nextCount }}" class="hidden">
                                <source src="" type="video/mp4">
                            </video>

        </div>
        <div class="col-lg-8">
          <input type="file" class="form-control image" id="image_{{ $nextCount }}" placeholder="" name="images[]" >
          
          
        </div>
        <div class="col-lg-4">
            <div class="col-lg-2 addMore_{{ $nextCount }}">
                <button type="button" class="btn btn-primary addMore hidden" id="addMore_{{ $nextCount }}">+</button>
            </div>

            <div class="col-lg-2 remove_{{ $nextCount }}">
                <button type="button" class="btn btn-danger removeImage" id="removeImage_{{ $nextCount }}">X</button>
            </div>
        </div>
    </div>
</div>