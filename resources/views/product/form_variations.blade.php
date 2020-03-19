<!-- Variations -->
@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<!-- <label class="col-lg-4 col-form-label">@lang('messages.inventory.variation_theme')</label> -->			
			<select  name="variation_theme" id="variation_theme" class="form-control">
				<option value="">@lang('messages.inventory.select_variation_theme')</option>
				
				@foreach($variation_themes as $variation_theme)
				<option  {{ isset($result->variation_theme_id) && $variation_theme->id==$result->variation_theme_id ? 'selected' : '' }}
					theme_1=" {{$variation_theme->variation_theme_1 }}" theme_2="{{$variation_theme->variation_theme_2}}"
					value="{{$variation_theme->id }} ">
					{{ $variation_theme->variation_theme_name }}
				</option>;
				@endforeach
			</select>			
		</div>
	</div>
	<div class="col-md-4">
		<label class="fancy-checkbox col-lg-12 col-form-label">
			<input type="checkbox" name="all_variants_place_one_location" {{ (@$result->all_variants_place_one_location == 1) ? 'checked="checked"' : ''  }} value="1">
			<span><i></i>
				@lang('messages.inventory.all_variants_place_one_location')
			</span>
		</label>
	</div>
</div>
<div class="row" id="make_variations">
	<div class="col-md-12">
		<div class="card card-body">
			<h4 id="add_variation_title" class="mb-3">@lang('messages.inventory.add_variations')</h4>
			
			<h4 id="edit_variation_title" class="display-none">
				@lang('messages.inventory.edit_variations')
				<button type="button" class="btn btn-blue display-none ml-5 px-4" id="edit_variation_button">
					@lang('messages.common.edit')
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
				<div class="col-lg-1 text-left" id="add_variation_button">
					<button type="button" class="btn btn-blue btn-add-variation px-4 mt-4">
						@lang('messages.common.add')
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
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
						<div class="dropdown bulk-action-dropdown">
							<button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
							<span class="icon-moon icon-Drop-Down-1"/>
								</button>
								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
									<h4 class="title">@lang('messages.modules.bulk_action')</h4>
									<button type="button" class="btn btn-add" onclick="bulk_variation_delete(this)">
									<span class="icon-moon red icon-Delete"></span>
									@lang('messages.inventory.delete_variations')
									</button>
								</div>
							</div>
						</div>
					</th>
					<th>Image</th>
					<th class="variation-size-header display-none" width="200">
						<span>Size</span>
						<input type="text" id="size_required" name="size_required" style="display:none;"  class="display-none"/>
					</th>
					<th class="variation-color-header display-none" width="200"><span>Color</span>
						<input type="text" id="color_required" name="color_required" style="display:none;"  class="display-none"/>
					</th>
					<th width="300">@lang('messages.inventory.title')</th>
					<th>@lang('messages.common.sku')</th>
					<th>@lang('messages.inventory.product_barcode')</th>
					<th>@lang('messages.common.action')</th>
				</tr>
			</thead>
			<tbody>
				<tr class="variation-row-template display-none">
					<td>
						<div class="d-flex">
							<label class="fancy-checkbox">
								<input type="checkbox" disabled class="child-checkbox">
								<span><i></i></span>
							</label>
						</div>
					</td>
					<td>
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

		                <div class="fancy-file">
							<input id="product_upload_file" class="inputfile-custom-normal1" type="file" disabled name="var_img[]" onchange="previewVariationImage(this, $(this).parents('td').find('img'),'',$(this).parents('td').find('video'));" accept="image/x-png,image/gif,image/jpeg,video/mp4">
							<label for="product_upload_file">
								<!-- <strong>Change Image</strong> -->
							</label>
						</div>
					</td>
					<td class="display-none">
						<input type="text" disabled class="form-control w-100" name="var_size[]">
					</td>
					<td class="display-none">
						<input type="text" disabled class="form-control w-100" name="var_color[]">
					</td>
					<td>
						<input type="text" disabled class="form-control" name="var_title[]">
					</td>
					<td>
						<input type="text" disabled readonly="" class="form-control w-100" name="var_sku[]">
					</td>
					<td>
						<input type="text" disabled class="form-control w-100" name="var_barcode[]">
					</td>
					<td>
						<ul class="action-btns">
              				<li> <a class="btn-delete" href="javascript:;" onclick="remove_variation_single(this)"><span class="icon-moon icon-Delete"></span></a></li>
        				</ul>
					</td>
				</tr>
				@if(!empty($result->variation))
				@foreach($result->variation as $variation)
				<tr>
					<td>
						<div class="d-flex">
							<label class="fancy-checkbox">
								<input type="checkbox" class="child-checkbox">
								<span><i></i></span>
								<input type="hidden" name="var_id[]" value="{{$variation->id}}">
							</label>
						</div>
					</td>
					<td>
						<!-- Images -->
						<div class="img-container-tbl">
							@if(!empty($variation->main_image_internal))

								@php
									$ext = pathinfo($variation->main_image_internal, PATHINFO_EXTENSION);
			                    @endphp
			                    @if($ext!="mp4")
									<img style="width:80px; height:80px;" src="{{url('/img/img-loading.gif') }}" data-original="{{$variation->main_image_internal}}" class="img-thumb" >
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
									<button type="button" class="btn-remove-img" onclick="delete_variation_img(this)" attr-original-url="{{ $variation->getOriginal('main_image_internal') }}" >&times</button>
								@endif
							@endif
						</div>
						<div class="fancy-file">
							<input id="product_upload_file" type="file" class="inputfile-custom-normal1" name="var_img[]" onchange="previewVariationImage(this, $(this).parents('td').find('img'),'',$(this).parents('td').find('video'));" accept="image/x-png,image/gif,image/jpeg,video/mp4">
							<label for="product_upload_file">
								<!-- <strong>Change Image</strong> -->
							</label>
						</div>
						
					</td>
					<td class="display-none">
						<input type="text" class="form-control w-100" name="var_size[]" value="{{ $variation->variation_theme_value1 }}" >
					</td>
					<td class="display-none">
						<input type="text" class="form-control w-100" name="var_color[]" value="{{ $variation->variation_theme_value2 }}">
					</td>
					<td>
						<input type="text" class="form-control" name="var_title[]" value="{{ $variation->title }}">
					</td>
					<td>
						<input type="text" readonly="" class="form-control w-100" name="var_sku[]" value=" {{ $variation->sku }}">
					</td>
					<td>
						<!-- Single Barcode -->
						@php
						$var_barcode = $variation->barCodes()->where('barcode_type', '1')->get()->first();
						@endphp
						<input type="text" class="form-control w-100" name="var_barcode[]" value="{{!empty($var_barcode->barcode) ? $var_barcode->barcode : '' }}">
						<input type="hidden" name="var_barcode_id[]" value="{{!empty($var_barcode->id) ? $var_barcode->id : ''}}">
					</td>
					<td>
						<ul class="action-btns">
              				<li> <a class="btn-delete" href="javascript:;" onclick="remove_variation_single(this)"><span class="icon-moon icon-Delete"></span></a></li>
        				</ul>
					</td>
				</tr>
				@endforeach
				@endif
			</table>
		</div>
		</div>
	</div>
	@section('css')
	<style type="text/css">
		.img-thumb{
			width: 40px;
			height: 40px;
		}
	</style>
	@endsection