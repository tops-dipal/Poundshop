<!-- Stock File -->
@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
<div class="row">
    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.product_id_type') <span class="asterisk">*</span></label>
            <div class="col-lg-8 mt-2">
                <div class="d-flex align-items-center"> 
                    @php
                        $sel_product_id_type = old('product_identifier_type') ? old('product_identifier_type') : @$result->product_identifier_type;

                        if(empty($sel_product_id_type))
                        {
                            $sel_product_id_type = 2;
                        }
                    @endphp
                    
                    @foreach(product_identifier_type() as $product_identifier_type_id => $product_identifier_type_caption )
                        
                        <label class="fancy-radio sm pr-3">
                             <input type="radio" name="product_identifier_type" value="{{$product_identifier_type_id}}" {{ ($sel_product_id_type == $product_identifier_type_id) ? 'checked="checked"' : "" }}>
                            <span class="font-14-dark"><i></i>{{$product_identifier_type_caption}}</span>
                        </label>
                        
                    @endforeach
                </div>    
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.product_id') <span class="asterisk">*</span></label>
            <div class="col-lg-8">
                  <input type="text" class="form-control" only_digit placeholder="" name="product_identifier" value="{{ !empty(old('product_identifier')) ? old('product_identifier') : @$result->product_identifier }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.product_type') <span class="asterisk">*</span></label>
            <div class="col-lg-8">
                <select class="form-control" placeholder="" name="product_type" onchange="set_variation(this)" {{ !empty($result->product_type) ? 'readonly="readonly"' : '' }} >
                    
                    @php
                        $par_var_value = 'parent';

                        $sel_product_type = !empty(old('product_type')) ? old('product_type') : @$result->product_type;
                        
                        if(@$result->product_type == 'variation')
                        {
                            $par_var_value = 'variation';
                        }
                    @endphp

                    <option value="normal" {{($sel_product_type == 'normal') ? 'selected="selected"' : '' }}>@lang('messages.inventory.product_type_normal')</option>

                    <option value="{{$par_var_value}}" {{($sel_product_type == 'parent' || $sel_product_type == 'variation') ? 'selected="selected"' : '' }}>@lang('messages.inventory.product_type_variation')</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.common.sku') <span class="asterisk">*</span></label>
            <div class="col-lg-8">
                <input readonly=""  type="text" class="form-control" name="sku" value="{{ !empty($result->sku) ? $result->sku : get_sku() }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.title') <span class="asterisk">*</span></label>
            <div class="col-lg-8">
                <input type="text" class="form-control" placeholder="" name="title" value="{{ !empty(old('title')) ? old('title') : @$result->title }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.short_title') </label>
            <div class="col-lg-8">
                <input type="text" class="form-control" placeholder="" name="short_title" value="{{ !empty(old('short_title')) ? old('short_title') : @$result->short_title }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.commodity_code') </label>
            <div class="col-lg-8">
                <select class="form-control" placeholder="" name="commodity_code_id">
                    
                    @php
                        $sel_commodity_code_id = !empty(old('commodity_code_id')) ? old('commodity_code_id') : @$result->commodity_code_id;
                    @endphp

                    <option value="">@lang('messages.common.select') @lang('messages.inventory.commodity_code')</option>
                    @foreach($commodity_codes as $commodity_code)
                        <option value="{{$commodity_code->id}}" {{ ($sel_commodity_code_id == $commodity_code->id) ? 'selected="selected"' : '' }}>
                            {{$commodity_code->code}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.country_of_origin') </label>
            <div class="col-lg-8">
                <select class="form-control" placeholder="" name="country_of_origin">
                    @php
                        $sel_country_of_origin = !empty(old('country_of_origin')) ? old('country_of_origin') : @$result->country_of_origin;

                        if(empty($sel_country_of_origin))
                        {
                            // set default country_of_orgin_to UK
                            $sel_country_of_origin = '230';
                        }
                    @endphp

                    @foreach($countries as $country)
                        <option value="{{$country->id}}" {{($sel_country_of_origin == $country->id) ? 'selected="selected"' : '' }}>
                            {{$country->name}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.created_by') </label>
            <div class="col-lg-8">
                <input disabled="" type="text" class="form-control" placeholder="" name="created_by" value="{{ !empty($result->created_by) ? $result->user->first_name : Auth::user()->first_name }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.created_date') </label>
            <div class="col-lg-8">
                <input disabled="" type="text" class="form-control" placeholder="" name="created_at" value="{{ !empty($result->created_at) ? system_date($result->created_at) : system_date() }}">
            </div>
        </div>
    </div>
    
</div>
<fieldset class="custom-field">
  <legend class="page-title-inner w-auto px-2">@lang('messages.inventory.product_tag_section')</legend>
 <div class="row">
    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.essential')</label>
            
            @php
                $sel_is_essential = !empty(old('is_essential')) ? old('is_essential') : @$result->is_essential;
            @endphp

            <div class="col-lg-8 mt-2">
                <label class="fancy-radio sm pr-3">
                    <input type="radio" placeholder="" name="is_essential" value="0" {{ ($sel_is_essential == 0) ? 'checked="checked"' : '' }}><span class="font-14-dark"><i></i>No</span>
                </label>
                <label class="fancy-radio sm pr-3">
                     <input type="radio" placeholder="" name="is_essential" value="1" {{ ($sel_is_essential == 1) ? 'checked="checked"' : '' }}><span class="font-14-dark"><i></i>Yes</span>
                </label>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.product_tag')</label>
            <div class="col-lg-8">
                <select class="select2-tag" multiple="multiple" name="tags[]">
                  @foreach(product_logic_base_tags() as $prodcut_tag_key => $prodcut_tag)
                    @php
                        $db_column_name  = 'is_'.$prodcut_tag_key;
                    @endphp
                    <option value="{{$prodcut_tag_key}}" {{ (@$result->$db_column_name == '1') ? 'selected="selected"' : ''}} >{{$prodcut_tag}}</option>  
                  @endforeach

                  @php
                    $selected_custom_tags = $result->tags()->pluck('name')->toArray();
                  @endphp

                  @foreach($tags as $prodcut_tag)
                    <option value="{{$prodcut_tag->name}}" {{ in_array($prodcut_tag->name, $selected_custom_tags) ? 'selected="selected"' : '' }}>{{$prodcut_tag->name}}</option>  
                  @endforeach
                  
                </select>
                <div id="select_2_dropdown"></div>
            </div>    
        </div>    
    </div>   

    <div class="col-lg-6">
        @php
            $sel_on_hold = !empty(old('on_hold')) ? old('on_hold') : @$result->on_hold;
        @endphp

        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.product_on_hold')</label>
            <div class="col-lg-8 mt-2">
                <label>
                    <label class="fancy-radio sm pr-3">
                     <input type="radio" placeholder="" name="on_hold" value="0" {{ ($sel_on_hold == 0) ? 'checked="checked"' : '' }}><span  class="font-14-dark"><i></i>No</span>
                </label>
                <label>
                    <label class="fancy-radio sm pr-3">
                     <input type="radio"  placeholder="" name="on_hold" value="1" {{ ($sel_on_hold == 1) ? 'checked="checked"' : '' }}><span  class="font-14-dark"><i></i>Yes</span>
                </label>
            </div>
        </div>
    </div>   

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.seasonal')</label>
            <div class="col-lg-8">
                @php
                    $from_to_dates = "";
                    if(!empty($result->seasonal_from_date) && !empty($result->seasonal_to_date))
                    {
                        $from_to_dates = date('d M', strtotime($result->seasonal_from_date)).' to '.date('d M', strtotime($result->seasonal_to_date));
                    }

                @endphp
                <input disabled="disabled" type="text" class="form-control" value="{{ (@$result->is_seasonal == '1') ? 'Yes '.$from_to_dates : 'No' }}">
            </div>
        </div>
    </div> 

</div>
 </fieldset>


 

<div class="row">
    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.brand') </label>
            <div class="col-lg-8">
                <input type="text" class="form-control" placeholder="" name="brand" value="{{ !empty(old('brand')) ? old('brand') : @$result->brand }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.threshold_quantity') </label>
            <div class="col-lg-8">
                <input type="text" only_digit class="form-control" placeholder="" name="threshold_quantity" value="{{ !empty($result->threshold_quantity) ? $result->threshold_quantity : @$result->threshold_quantity }}">
            </div>
        </div>
    </div>


    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.single_selling_price') 
                <span class="asterisk">*</span>
            </label>
            <div class="col-lg-8">
                <div class="position-relative">
                    <span class="pound-sign-form-control">@lang('messages.common.pound_sign')</span>
                    <input type="text" only_numeric class="form-control" placeholder="" name="single_selling_price" value="{{ !empty($result->single_selling_price) ? $result->single_selling_price : @$result->single_selling_price }}">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.last_cost_price') </label>
            <div class="col-lg-8">
                <div class="position-relative">
                    <span class="pound-sign-form-control disabled-control">@lang('messages.common.pound_sign')</span>
                    <input disabled="disabled" type="text" class="form-control" id="last_cost_price" value="{{ !empty($result->last_cost_price) ? $result->last_cost_price : 0.00 }}">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.estimated_margin') </label>
            <div class="col-lg-8">
                <label class="col-form-label" id="estimated_margin"></label>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.vat_type') </label>
            <div class="col-lg-8 mt-2">
                @foreach(product_vat_types() as $vat_type_id => $vat_type)
                    <label class="fancy-radio sm pr-3"><input name="vat_type" type="radio" value="{{$vat_type_id}}" {{ (@$result->vat_type == $vat_type_id)  ? 'checked="checked"' : '' }}><span  class="font-14-dark"><i></i>{{$vat_type}}</span></label>
                @endforeach
            </div>
        </div>
    </div>


     <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.bulk_selling_price') </label>
            <div class="col-lg-8">
                <div class="position-relative">
                    <span class="pound-sign-form-control">@lang('messages.common.pound_sign')</span>
                    <input type="text" only_numeric class="form-control" placeholder="" name="bulk_selling_price" value="{{ !empty(old('bulk_selling_price')) ? old('bulk_selling_price') : @$result->bulk_selling_price }}">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.bulk_selling_quantity') </label>
            <div class="col-lg-8">
                <input type="text" only_digit class="form-control" placeholder="" name="bulk_selling_quantity" value="{{ !empty(old('bulk_selling_quantity')) ? old('bulk_selling_quantity') : @$result->bulk_selling_quantity }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.recom_retail_price') </label>
            <div class="col-lg-8">
                <input type="text" only_numeric class="form-control" placeholder="" name="recom_retail_price" value="{{ !empty(old('recom_retail_price')) ? old('recom_retail_price') : @$result->recom_retail_price }}">
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group row">
            <label class="col-lg-4 col-form-label">@lang('messages.inventory.comment') </label>
            <div class="col-lg-8">
                <textarea class="form-control" name="comment">{{ !empty(old('comment')) ? old('comment') : @$result->comment }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group row">
            <label class="col-lg-2 col-form-label">@lang('messages.inventory.long_description') </label>
            <div class="col-lg-10">
                <textarea class="form-control ckeditor" name="long_description" old-value="{{ !empty(old('long_description')) ? old('long_description') : @$result->long_description }}">{{ !empty(old('long_description')) ? old('long_description') : @$result->long_description }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group row">
            <label class="col-lg-2 col-form-label">@lang('messages.inventory.short_description') </label>
            <div class="col-lg-10">
                <textarea class="form-control" rows="4" name="short_description">{{ !empty(old('short_description')) ? old('short_description') : @$result->short_description }}</textarea>
            </div>
        </div>
    </div>
</div>  

<div class="row">
    <div class="col-lg-3">
        <div class="form-group row">
            <label class="col-lg-6 col-form-label">@lang('messages.inventory.product_length') </label>
            <div class="col-lg-6">
                <input type="text" only_numeric class="form-control" placeholder="" name="product_length" value="{{ !empty(old('product_length')) ? apply_float_value(old('product_length')) : apply_float_value(@$result->product_length) }}">
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="form-group row">
            <label class="col-lg-6 col-form-label">@lang('messages.inventory.product_width') </label>
            <div class="col-lg-6">
                <input type="text" only_numeric class="form-control" placeholder="" name="product_width" value="{{ !empty(old('product_width')) ? apply_float_value(old('product_width')) : apply_float_value(@$result->product_width) }}">
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="form-group row">
            <label class="col-lg-6 col-form-label">@lang('messages.inventory.product_height') </label>
            <div class="col-lg-6">
                <input type="text" only_numeric class="form-control" placeholder="" name="product_height" value="{{ !empty(old('product_height')) ? apply_float_value(old('product_height')) : apply_float_value(@$result->product_height) }}">
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="form-group row">
            <label class="col-lg-6 col-form-label">@lang('messages.table_label.stor_weight') </label>
            <div class="col-lg-6">
                <input type="text" only_numeric class="form-control" placeholder="" name="product_weight" value="{{ !empty(old('product_weight')) ? apply_float_value(old('product_weight')) : apply_float_value(@$result->product_weight) }}">
            </div>
        </div>
    </div>
</div>

