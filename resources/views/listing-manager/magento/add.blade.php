@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>
    	
    	<div class="right-items">
	        
            @if(empty($magento_posting_details) || $magento_posting_details->is_posted == '0')
                <a href="{{route('magento-to-be-listed')}}" class="btn btn-gray btn-header px-4">@lang('messages.common.cancel')</a>
            @else
                <a href="{{route('magento-in-progress')}}" class="btn btn-gray btn-header px-4">@lang('messages.common.cancel')</a>    
            @endif

	        <button type="submit" form="add_form" class="btn btn-blue btn-header px-4">@lang('messages.common.save')</button>   
            @if(@$magento_posting_details->is_posted != 1)
                <button type="button" class="btn btn-blue btn-header px-4" id="save_and_post">@lang('messages.magento_listing.save_and_post')</button>            
            @endif
	    </div>
    </div>

    <div class="card-flex-container">
        <form action="{{route('api-listing-manager-magento.store')}}" method="POST" class="form-horizontal form-flex" id="add_form" role="form">
        	@csrf
        	<input type="hidden" name="product_master_id" value="{{ $product_master_id }}">
        	<input type="hidden" name="is_posted" value="{{ (@$magento_posting_details->is_posted != 1) ? 0 : 1 }}">
            <input type="hidden" name="store_id" value="{{ $store_id }}">
        	<input type="hidden" name="product_type" value="{{ $product_master_details->product_type }}">
        	<div class="form-fields">
                <div class="container-fluid">
                	<div class="row">

                		<div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.prouduct_id_type') <span class="asterisk">*</span></label>
                                <div class="col-lg-8 mt-2">
                                    <div class="d-flex align-items-center">
                                    	@php
                                    		$sel_product_id_type = $select_data($magento_posting_details, $product_master_details, 'magento_product_id_type', 'product_identifier_type');

    					                    if(empty($sel_product_id_type))
    					                    {
    					                        $sel_product_id_type = 1;
    					                    }
    					                @endphp
    					                @foreach(product_identifier_type() as $product_identifier_type_id => $product_identifier_type_caption )
    					                    <label class="fancy-radio pr-3">
                                                <input type="radio" name="magento_product_id_type" value="{{$product_identifier_type_id}}" {{ ($sel_product_id_type == $product_identifier_type_id) ? 'checked="checked"' : "" }}>
                                                <span class="bold category_radio_label"><i></i>{{$product_identifier_type_caption}} </span>
                                            </label>
                                        @endforeach
                                    </div>    
                                </div>
                            </div>
                        </div>

                		<div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.prouduct_id') <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" only_digit placeholder="" name="magento_product_id" value="{{ $select_data($magento_posting_details, $product_master_details, 'magento_product_id', 'product_identifier') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.common.sku') <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" readonly="" placeholder="" name="sku" value="{{ $product_master_details->sku }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.product_title') <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="" name="product_title" value="{{ $select_data($magento_posting_details, $product_master_details, 'product_title', 'title') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.date_to_go_live') <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control date_to_go_live" placeholder="" name="date_to_go_live" readonly="" value="{{ !empty($magento_posting_details->date_to_go_live) ? date('d-M-Y H:i', strtotime($magento_posting_details->date_to_go_live)) :  date('d-M-Y H:i') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.single_selling_price') <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                    <div class="position-relative">
                                        <span class="pound-sign-form-control disabled-control">£</span>
                                        <input type="text" class="form-control" only_numeric placeholder="" name="selling_price" value="{{ $select_data($magento_posting_details, $product_master_details, 'selling_price', 'single_selling_price') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.bulk_selling_price') </label>
                                <div class="col-lg-8">
                                    <div class="position-relative">
                                        <span class="pound-sign-form-control disabled-control">£</span>
                                        <input type="text" class="form-control" only_numeric placeholder="" name="bulk_selling_price" value="{{ $select_data($magento_posting_details, $product_master_details, 'bulk_selling_price', 'bulk_selling_price') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.quantity') <span class="asterisk">*</span></label>
                                @php
                                $quantity=@$select_data($magento_posting_details, $product_master_details, 'quantity', 'quantity');
                                if($quantity=='' || $quantity==0)
                                {
                                    $quantity=$defaultStock;
                                }
                                
                                @endphp
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" only_digit placeholder="" name="quantity" value="{{ $quantity }}" disabled="">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.country_of_origin') <span class="asterisk">*</span></label>
                                @php
                                	$sel_country_of_origin = $select_data($magento_posting_details, $product_master_details, 'country_of_origin', 'country_of_origin');
                                @endphp

                                <div class="col-lg-8">
                                   	<select class="form-control" placeholder="" name="country_of_origin">
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
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.brand')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="" name="brand" value="{{ @$select_data($magento_posting_details, $product_master_details, 'brand', 'brand') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.category') <span class="asterisk">*</span></label>
                                <div class="col-lg-8">
                                	@php
                                		$sel_cat_array = array();

                                		if(!empty($magento_posting_details->category_ids))
                                		{
                                			$sel_cat_array = explode(',', $magento_posting_details->category_ids);
                                		}
                                		elseif($product_master_details->buying_category_id)
                                		{
                                			$sel_cat_array = $product_master_details->buying_range->magentoCategories->pluck('id')->toArray();
                                		}
									@endphp  

									<select class="form-control custom-select-search" name="category_ids[]" multiple="multiple">
										@foreach($magentoCategories as $cat_id =>  $cat_name)
											<option value="{{$cat_id}}" {{ in_array($cat_id, $sel_cat_array) ? 'selected = "selected"' : '' }}>{{$cat_name}}</option>
										@endforeach
									</select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.short_description') </label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="" name="magento_short_description" value="{{ @$select_data($magento_posting_details, $product_master_details, 'short_description', 'short_description') }}">
                                </div>
                            </div>
                        </div>
					</div>    

					<div class="row">
						<!-- Main Image -->
						<div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">
                                    @lang('messages.magento_listing.main_image')
                                </label>
                                <div class="col-lg-8">
                                    @php

                                        $img_url = asset('storage/uploads/product-images/no-image.jpeg');

                                        if(isset($magento_posting_details->main_image_url))
                                        {
                                            $img_url = url($magento_posting_details->main_image_url);
                                        }
                                        elseif(!empty($product_master_details->main_image_marketplace))
                                        {
                                            $img_url = $product_master_details->main_image_marketplace;

                                        }
                                        
                                        $ext = pathinfo($img_url, PATHINFO_EXTENSION);
                                    @endphp
                                    
                                    <input type="hidden" name="main_image_url" value="{{$img_url}}">

                                    @if($ext == "mp4")
                                        <img src="" class="hidden"style="width:120px;height:120px;" id="magentoimagePreview">
                                        <video style="width:120px;height:120px;" controls="controls" preload="metadata" class="InternalVideoPreview">
                                            <source src="{{ $img_url }}#t=0.5" type="video/mp4">
                                        </video>
                                    @else
                                        <img src="{{url('/img/img-loading.gif') }}" data-original="{{$img_url}}" style="width:120px;height:120px;" id="magentoimagePreview">
                                        <video style="width:120px;height:120px;" controls="controls" preload="metadata" class="magentoVideoPreview hidden">
                                            <source src="" type="video/mp4">
                                        </video>
                                    @endif  

                                    <div class="fancy-file mt-2">
                                        <input type="file" name="main_image_url" id="main_image_url" class="inputfile-custom" data-multiple-caption="{count} files selected" accept="image/x-png,image/gif,image/jpeg,video/mp4" >
                                        <label for="main_image_url"><span></span> <strong>Change Image</strong></label>
                                    </div>  
                                </div>
                            </div>
		                </div>

		                <!-- Other Images -->
		                <div class="col-md-12">

                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label"> 
                                    @lang('messages.magento_listing.other_images')
                                </label>
                                <div class="col-lg-10">
                                   @php
                                        $other_images = array();
                                        
                                        $product_master_imgs = false;

                                        if(isset($magento_posting_details->image_details))
                                        {
                                            $other_images = unserialize($magento_posting_details->image_details);
                                        }
                                        elseif(!empty($product_master_details->main_image_marketplace))
                                        {
                                            $product_master_imgs = true;
                                            $other_images = $product_master_details->productImages;
                                        }   
                                    @endphp

                                    @if(!empty($other_images))
                                        @foreach($other_images as $other_image)
                                            @php
                                                if($product_master_imgs == true)
                                                {
                                                    if(!empty($other_image->image))
                                                    {
                                                        $other_image = url('storage/uploads/'.$other_image->image);
                                                    }
                                                    else{
                                                        $other_image = $other_image->image_url;
                                                    }
                                                }
                                            @endphp
                                            <div class="other_image_div">
                                                
                                                    <img src="{{url('/img/img-loading.gif') }}" data-original="{{ $other_image }}" style="width: 120px; height: 120px;">
                                                    <button type="button" class="btn-remove-img" onclick="delete_other_img(this)">&times;</button>
                                                    <input type="hidden" name="image_details[]" value="{{$other_image}}">
                                                
                                            </div>
                                        @endforeach
                                    @endif
                                    

                                    <div class="input-images mt-2"></div>
                                </div>
                            </div>							
						</div>	

					</div>	

					<div class="row">
						<div class="col-md-12">
							<div class="form-group row">
                                <label class="col-lg-2 col-form-label">@lang('messages.magento_listing.product_description') <span class="asterisk">*</span></label>
                                <div class="col-lg-10">
                                    <textarea class="form-control ckeditor" name="product_description">{{ @$select_data($magento_posting_details, $product_master_details, 'product_description', 'long_description') }}</textarea>
                                </div>
                            </div>
						</div>	
					</div>	

					<div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.meta_title') </label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="" name="meta_title" value="{{ !empty($magento_posting_details->meta_title) ? $magento_posting_details->meta_title : '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.meta_keyword') </label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="" name="meta_keyword" value="{{ !empty($magento_posting_details->meta_keyword) ? $magento_posting_details->meta_keyword : '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.meta_description') </label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="" name="meta_description" value="{{ !empty($magento_posting_details->meta_description) ? $magento_posting_details->meta_description : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>	

                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group row">
                                <label class="col-lg-6 col-form-label">@lang('messages.magento_listing.product_length') </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" only_numeric placeholder="" name="magento_product_length" value="{{ apply_float_value(@$select_data($magento_posting_details, $product_master_details, 'magento_product_length', 'product_length')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group row">
                                <label class="col-lg-6 col-form-label">@lang('messages.magento_listing.product_height') </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" only_numeric placeholder="" name="magento_product_height" value="{{ apply_float_value(@$select_data($magento_posting_details, $product_master_details, 'magento_product_height', 'product_height')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group row">
                                <label class="col-lg-6 col-form-label">@lang('messages.magento_listing.product_width') </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control"  only_numeric placeholder="" name="magento_product_width" value="{{ apply_float_value(@$select_data($magento_posting_details, $product_master_details, 'magento_product_width', 'product_width')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group row">
                                <label class="col-lg-6 col-form-label">@lang('messages.magento_listing.product_weight') </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control"  only_numeric placeholder="" name="magento_product_weight" value="{{ apply_float_value(@$select_data($magento_posting_details, $product_master_details, 'magento_product_weight', 'product_weight')) }}">
                                </div>
                            </div>
                        </div>
                    </div>

					@php
						
						$product_type = $select_data($magento_posting_details, $product_master_details, 'product_type', 'product_type');
					@endphp
					
					@if($product_type == 'parent' && false)
							@include('listing-manager.magento.add_variations')
					@endif
				</div>
            </div>    	
        </form>
    </div>    	
</div>        
@endsection

@section('script')
	<script type="text/javascript" src="{{asset('js/listing-manager/magento/add.js')}}"></script>
@endsection
