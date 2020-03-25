  <style type="text/css">
    .max-img-width{
      max-width: 100px;
    }
  </style>

  <div class="custom-modal modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header align-items-center">
        <h5 class="modal-title">@lang('messages.material_receipt.add_variations')</h5>
        <div>
          <button type="button" class="btn btn-gray font-12 px-4" data-dismiss="modal" aria-label="Close">@lang('messages.common.cancel')</button>
          <button type="submit" class="btn btn-green font-12 px-4 ml-3" form="{{$form_id}}">@lang('messages.common.save')</button>
        </div>

      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <input type="hidden" id="manageVariationTitle" value="{{ $result->title }}">
            <input type="hidden" name="product_id" value="{{ $result->id }}">
            <input type="hidden" name="booking_id" value="{{ $booking_id }}">
            <select class="form-control" name="variation_theme" form="{{$form_id}}">
              <option value="" {{ !empty($result->variation_theme_id) && $result->product_type == "parent" ? "disabled" : "" }}>@lang('messages.material_receipt.select_variations')</option>
              @foreach($variation_themes as $variation_theme)
                <option  {{ !empty($result->variation_theme_id) && $result->product_type == "parent" && $variation_theme->id==$result->variation_theme_id ? 'selected' : '' }}
                  {{ !empty($result->variation_theme_id) && $result->product_type == "parent" && $variation_theme->id != $result->variation_theme_id ? 'disabled' : '' }}
                  theme_1=" {{$variation_theme->variation_theme_1 }}" theme_2="{{$variation_theme->variation_theme_2}}"
                  value="{{$variation_theme->id }} ">
                  {{ $variation_theme->variation_theme_name }}
                </option>;
              @endforeach
            </select>
          </div>  

          <div class="col-md-6">
            <label class="fancy-checkbox col-lg-12 col-form-label">
              <input type="checkbox" name="all_variants_place_one_location" {{ ($result->all_variants_place_one_location == 1 && $result->product_type == "parent") ? 'checked="checked"' : ''  }} value="1" form="{{$form_id}}">
              <span><i></i>
                @lang('messages.inventory.all_variants_place_one_location')
              </span>
            </label>
          </div>
        </div>  
        
        <div class="row mt-3 mb-3 {{ !empty($result->variation_theme_id) && $result->product_type == "parent" ? ""  : "display-none" }}" id="make_variations">
          <div class="col-md-12">
            <div class="card card-body">
              <h4 id="add_variation_title" class="mb-3">@lang('messages.inventory.add_variations')</h4>
              
              <h4 id="edit_variation_title" class="display-none">
                @lang('messages.inventory.edit_variations')
                <button type="button" class="btn btn-blue display-none ml-5 px-4" id="edit_variation_button">
                  @lang('messages.common.add')
                </button>
              </h4>
              <div class="row" id="theme_values">
                <div class="col-lg-6 display-none" id="theme_1_div">
                  <div class="form-group row">
                    <label class="col-sm-12 theme_1_label font-12">Size</label><br>
                    <div class="col-sm-3">
                      <input type="text" class="form-control mb-1 size-init-input-box" id="size_init[]" placeholder="">
                    </div>
                  </div>
                </div>
                <div class="col-lg-5 display-none" id="theme_2_div">
                  <div class="form-group row">
                    <label for="" class="col-sm-12 theme_2_label font-12">Color</label><br>
                    <div class="col-sm-3">
                      <input type="text" class="form-control mb-1 color-init-input-box" id="color_init[]" placeholder="">
                    </div>
                  </div>
                </div>
                <div class="col-lg-2 text-left" id="add_variation_button">
                  <button type="button" class="btn btn-blue btn-add-variation px-4 mt-4">
                    @lang('messages.common.add')
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row {{ !empty($result->variation_theme_id) && $result->product_type == "parent" ? ""  : "display-none2" }}">
          <div class="col-lg-12">
            <div class="table-responsive">
              <table class="table display border-less variation-table">
                <thead>
                  <tr>
                    <th>
                      <div class="d-flex">
                        <label class="fancy-checkbox">
                          <input type="checkbox" class="master-checkbox">
                          <span><i></i></span>
                        </label>
                      </div>
                    </th>
                    <th class="max-img-width">Image</th>
                    <th class="variation-size-header display-none" width="200">
                      <span>Size</span>
                      <input type="text" id="size_required" name="size_required" style="display:none;"  class="display-none"/>
                    </th>
                    <th class="variation-color-header display-none" width="200"><span>Color</span>
                      <input type="text" id="color_required" name="color_required" style="display:none;"  class="display-none"/>
                    </th>
                    <th width="300">Product Title</th>
                    <th>SKU</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="variation-row-template display-none">
                    <td>
                      <div class="d-flex">
                        <label class="fancy-checkbox">
                          <input type="checkbox" class="child-checkbox" name="var_sku_id[]" value="" disabled="" form="{{$form_id}}">
                          <span><i></i></span>
                        </label>
                      </div>
                    </td>
                    <td class="max-img-width">
                      <div class="img-container-tbl">
                        <!-- Images -->
                        @php
                          $default_img = asset('storage/uploads/product-images/no-image.jpeg');
                        @endphp
                                  
                        <img style="width:80px; height:80px;" src="{{$default_img}}" class="img-thumb">
                        
                        <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" class="magentoVideoPreview hidden">
                                      <source src="" type="video/mp4">
                                  </video>
                              </div>

                        
                        <input type="file" class="inputfile-custom-normal1" disabled name="var_img[]" onchange="previewVariationImage(this, $(this).parents('td').find('img'),'',$(this).parents('td').find('video'))" form="{{$form_id}}">
                      
                    </td>
                    <td class="display-none">
                      <input type="text" disabled class="form-control w-100" name="var_size[]" form="{{$form_id}}">
                    </td>
                    <td class="display-none">
                      <input type="text" disabled class="form-control w-100" name="var_color[]" form="{{$form_id}}"> 
                    </td>
                    <td>
                      <input type="text" disabled class="form-control" name="var_title[]" form="{{$form_id}}">
                    </td>
                    <td>
                      <input type="text" disabled readonly="" class="form-control w-100" name="var_sku[]" form="{{$form_id}}">
                    </td>
                  </tr>
                  @if(!$result->variation->isEmpty())
                  
                  @foreach($result->variation as $variation)
                  <tr>
                    <td>
                      <input type="hidden" name="var_id[]" value="{{ $variation->id }}" form="{{$form_id}}">
                      <div class="d-flex">
                        <label class="fancy-checkbox">
                          <input type="checkbox" class="child-checkbox" name="var_sku_id[]" value="{{$variation->id}}" {{ in_array($variation->id, $selected_variants) ? "checked='checked'" : "" }} form="{{$form_id}}">
                          <span><i></i></span>
                        </label>
                      </div>
                    </td>
                    <td class="max-img-width">
                      <!-- Images -->
                      <div class="img-container-tbl">
                        @if(!empty($variation->main_image_internal))

                          @php
                            $ext = pathinfo($variation->main_image_internal, PATHINFO_EXTENSION);
                                    @endphp
                                    @if($ext!="mp4")
                            <img style="width:80px; height:80px;" src="{{ $variation->main_image_internal }}" class="img-thumb" >
                            <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" class="magentoVideoPreview hidden">
                                <source src="" type="video/mp4" >
                            </video>
                          @else
                            <video controls="controls" preload="metadata" class="magentoVideoPreview" style="max-width:150px; max-height:150px;">
                                          <source src="{{ asset($variation->main_image_internal) }}#t=0.5" type="video/mp4">
                                      </video>
                            <img src="" class=" thumbnail hidden" id="magentoimagePreview" style="width:80px; height:80px;"/>
                          @endif

                          @if(!empty($variation->getOriginal('main_image_internal')))
                            <button type="button" class="btn-remove-img" onclick="delete_variation_img(this)" attr-original-url="{{ $variation->getOriginal('main_image_internal') }}" form="{{$form_id}}">&times</button>
                          @endif
                        @endif
                      </div>
                      <input id="product_upload_file" type="file" class="inputfile-custom-normal1" name="var_img[]" onchange="previewVariationImage(this, $(this).parents('td').find('img'),'',$(this).parents('td').find('video'));" form="{{$form_id}}">

                    </td>
                    <td class="display-none">
                      <input type="text" class="form-control w-100" name="var_size[]" readonly="" value="{{ $variation->variation_theme_value1 }}" form="{{$form_id}}">
                    </td>
                    <td class="display-none">
                      <input type="text" class="form-control w-100" name="var_color[]" readonly="" value="{{ $variation->variation_theme_value2 }}" form="{{$form_id}}">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="var_title[]" readonly="" value="{{ $variation->title }}" form="{{$form_id}}">
                    </td>
                    <td>
                      <input type="text" readonly="" class="form-control w-100" name="var_sku[]" value=" {{ $variation->sku }}" form="{{$form_id}}">
                    </td>
                  </tr>
                  @endforeach
                  @endif
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
        </div>
    </div>
  </div>
<!-- </form> -->