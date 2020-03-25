@php
	$product_id = $row->product_id;
	
	$booking_po_product_id = !empty($row->booking_po_product_id) ? $row->booking_po_product_id : '';

	$open_cases = false;
	
	if(empty($booking_po_product_id) 
		&& (!empty($row->po_best_before_date) 
			|| !empty($inventory_case_details[$product_id])
			|| $row->po_is_variant == 1
		)
	)
	{
		$open_cases = true;
	}	
	elseif(!empty($booking_po_product_id) && ($row->is_inner_outer_case == '1' || $row->is_variant == 1))
	{
		$open_cases = true;
	}
		
	$set_photobooth = $row->is_request_new_photo;
		
	if(!empty($booking_po_product_id))
	{
		$set_photobooth = $row->is_photobooth;
	}

	if($row->product_type == 'parent')
	{
		$set_photobooth = 0;	
	}

	if($row->return_to_supplier == 1)
	{
		$form_id = 'form_detail_return_to_supplier_'.$booking_po_product_id;	
	}
	elseif($row->product_type == 'variation' && !empty($parent_row))
	{
		$form_id = 'form_detail_var_'.$booking_po_product_id.'_'.$parent_row->po_product_id;
	}
	else
	{
		$form_id = 'form_detail_'.$row->po_product_id;	
	}

	$sel_variants = 0;

	$sel_best_before = '0';
	
	$sel_is_inner_outer_case = '0';
@endphp

<form id="{{$form_id}}" action="{{url('api/api-material-receipt-save-web-product')}}" role="form" class="material-receipt-save-product"></form>
<tr>
	<input type="hidden" form="{{$form_id}}" name="booking_id" value="{{$params['booking_id']}}">

	<input type="hidden" form="{{$form_id}}" name="product_id" value="{{$product_id}}" class="product_id_class">

	<input type="hidden" form="{{$form_id}}" name="booking_po_product_id" value="{{$booking_po_product_id}}" class="">
	
	<input type="hidden" form="{{$form_id}}" name="is_photobooth" value="{{ $set_photobooth }}">

	<input type="hidden" form="{{$form_id}}" name="total_quantity" value="{{ !empty($row->total_quantity) ? $row->total_quantity : 0 }}">

	<!-- <input type="hidden" form="{{$form_id}}" name="po_id" value="{{ $row->po_id }}"> -->

	<input type="hidden" form="{{$form_id}}" name="po_product_id" value="{{ $row->po_product_id }}">

	<input type="hidden" form="{{$form_id}}" name="product_type" value="{{ !empty($row->product_type) ? $row->product_type : 'normal' }}">

	@if($row->is_variant == '1')
		<input type="hidden" form="{{$form_id}}" name="variation_selected" value="{{ !empty($var_result[$row->booking_po_product_id]) ? 1 : 0 }}">
	@endif

	@if($open_cases == true && empty($booking_po_product_id))
		
		<input type="hidden" class="open_case" form="{{$form_id}}">

		@if($row->po_is_variant == 0)
			@if(!empty($row->po_best_before_date))
				<input type="hidden" class="po_best_before_date" form="{{$form_id}}" value="{{ system_date($row->po_best_before_date) }}">
			@endif
			
			@if(!empty($inventory_case_details[$product_id]))
				<input type="hidden" class="inventory_case_details" form="{{$form_id}}"
					attr-outer-barcode="{{ @$inventory_case_details[$product_id]['outer']['barcode'] }}"
					attr-outer-qty="{{ @$inventory_case_details[$product_id]['outer']['case_quantity'] }}"
					attr-inner-barcode="{{ @$inventory_case_details[$product_id]['inner']['barcode'] }}"
					attr-inner-qty="{{ @$inventory_case_details[$product_id]['inner']['case_quantity'] }}"
				>
			@endif
		@endif
			
	@endif

	<td class="border-none">
		<p class="product-title font-14-dark bold mb-3">{{ $row->title }}</p>
		<div class="d-flex mr-product-detail">
			<div class="product-tags">
				@if($row->is_new_product == '0')
					<p class="tag new">@lang('messages.material_receipt.new_products')</p>
				@endif
				@if($row->product_type == 'parent')
					<p class="tag master">@lang('messages.material_receipt.master_products')</p>
				@endif
				@if($row->product_type == 'variation')
					<p class="tag variation">@lang('messages.material_receipt.variation_products')</p>
				@endif
				@if($row->return_to_supplier == '1')
					<p class="tag">
						@lang('messages.material_receipt.return_to_supplier_product')
					</p>
				@endif
			</div>
			
			<a href="{{$row->main_image_internal}}" data-rel="lightcase">
				<img src="{{ url('/img/img-loading.gif') }}" data-original="{{ $row->main_image_internal_thumb }}" width="80" height="80" alt="">
			</a>
			<div class="ml-2">										
				<div class="group-item mt-4">
			        <p class="title font-14-dark mb-2">Barcode</p>
			        <div class="d-flex align-items-center">
				        <span class="desc mr-3">
				        	@php
				        		$barcode = $row->product_identifier;

				        		if(!empty($row->booking_barcode))
				        		{
				        			$barcode = $row->booking_barcode;
				        		}
				        		elseif(!empty($row->barcode))
				        		{
				        			$barcode = $row->barcode;
				        		}
				        	@endphp
				            <input  type="text" name="barcode" form="{{$form_id}}" class="font-14-dark bold input_barcode" value="{{ $barcode }}">
				        </span>
				        <span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
				    </div>
			    </div>
			</div>
		</div>								
	</td>

	<td class="border-none">
		<p class="font-14-dark bold mt-2">{{ !empty($row->total_quantity) ? $row->total_quantity : '-' }}</p>
		@if($set_photobooth == 1)
			<p class="font-14-dark color-purple mt-5 mr-5 photobooth_js_labels" style="{{ ($open_cases == false && empty($row->is_inner_outer_case) || true) ? '' : "display:none" }}">Photo Required: <span class="font-14-dark color-purple bold">{{ ($set_photobooth == 1) ? "Yes" : "No" }}</span></p>
		@endif
	</td>
	
	@php
		$delivery_note_qty = "";

		if(!empty($row->delivery_note_qty))
		{
			$delivery_note_qty = $row->delivery_note_qty;
		}
		elseif(!empty($row->total_quantity) && empty($booking_po_product_id))
		{
			$delivery_note_qty = $row->total_quantity;
		}
	@endphp
	
	<td class="border-none">				
		<input  type="text" name="delivery_note_qty" only_digit class="font-14-dark bold w-60" form="{{$form_id}}" value="{{ $delivery_note_qty }}" {{ ($row->consider_parent_delivery_note_qty == 0 && !empty($booking_po_product_id) && $row->product_type == "parent" && (@$row->is_variant == '1') ) || (@$parent_row->consider_parent_delivery_note_qty == 1 && $row->product_type == 'variation') ? 'readonly="readonly"' : ""  }}>								
		@if($row->is_variant == 1)				
			<label class="fancy-checkbox sm" title="Consider Parent Delivery Note Quantity">
				<input type="checkbox" name="consider_parent_delivery_note_qty" value="1" form="{{$form_id}}" {{ ($row->consider_parent_delivery_note_qty == 1 && $row->product_type == 'parent') ? 'checked="checked"' : "" }} onchange="setDNQtyParent(this)">
				<span><i></i></span>
			</label>
		@endif
	</td>
	<td class="border-none">	
		@php
			$qty_received = "";

			if(!empty($row->qty_received))
			{
				$qty_received = $row->qty_received;
			}
			
			if(!empty($qty_received) && @$row->is_photobooth == '1')
			{
				$qty_received = $qty_received - 1;
			}
		@endphp														
		<input  type="text" name="qty_received" only_digit class="font-14-dark bold w-60" value="{{ $qty_received }}" form="{{$form_id}}" {{ ($row->is_inner_outer_case == '1' && !empty($product_case_details[$booking_po_product_id])) || ($row->product_type == 'parent') ? 'readonly="readonly"' : '' }} disabled="disabled">	
		@if($set_photobooth == 1)
			<p class="mt-2"><span class="font-14-dark bold mt-4 photobooth_js_labels" style="{{ ($open_cases == false && empty($row->is_inner_outer_case) || true) ? '' : "display:none" }}">Photobooth: </span>
			<span class="font-14-dark bold mt-4 photobooth_js_labels" style="{{ ($open_cases == false && empty($row->is_inner_outer_case) || true) ? '' : "display:none" }}" >1</span></p>
		@endif	
	</td>
	@php
		$location = "";
		
		$type_of_location = "";
		
		if(!empty($case_without_location[$booking_po_product_id]) 
			&& !empty($booking_po_product_id)
		)
		{
			$location = $case_without_location[$booking_po_product_id]['location'];
			
			$type_of_location = $case_without_location[$booking_po_product_id]['type_of_location'];
		}

	@endphp
	<td class="border-none location_hide">								
		<input  type="text" name="location" form="{{$form_id}}" class="font-14-dark bold set_location_details" value="{{ $location }}" {{ ($row->is_inner_outer_case == '1' && !empty($product_case_details[$booking_po_product_id])) || ($row->product_type == 'parent') ? 'readonly="readonly"' : '' }} />

		@if(!empty($type_of_location))
			<span class="location_type font-10-dark bold d-block mt-1">{{ LocationType($type_of_location) }}</span>	
		@endif

		<!-- @if($set_photobooth == 1)
			<p class="font-14-dark bold mt-4 photobooth_js_labels" style="{{ ($open_cases == false && empty($row->is_inner_outer_case) || true) ? '' : "display:none" }}">Photobooth</p>
		@endif -->
	</td>
	<td class="border-none">				
		@php
			$diff_class = "";

			if(!empty($row->difference))
			{
				if($row->difference > 0)
				{
					$diff_class = "diff-plus";
				}

				if($row->difference < 0)
				{
					$diff_class = "diff-minus";
				}
			}
		@endphp				
		<div class="d-flex align-items-center group-item {{$diff_class}} difference_label_dev">			
			@if($row->product_type != 'variation')	 						
				<span class="desc mt-2">
					<span class="font-14-dark bold difference_label">{{ !empty($row->difference) ? $row->difference : 0 }}</span>
					<input type="hidden" class="qty_diff_class_{{$product_id}}" name="difference" form="{{$form_id}}" value="{{ !empty($row->difference) ? $row->difference : 0 }}">
				</span>
			@else
				<span class="desc">
					<span class="font-14-dark bold">
						- 
					</span>
				</span>		
			@endif
		</div>
		<!-- @if($set_photobooth == 1)
			<p class="font-14-dark bold mt-4 photobooth_js_labels" style="{{ ($open_cases == false && empty($row->is_inner_outer_case) || true) ? '' : "display:none" }}">Photobooth</p>
		@endif -->
	</td>			
	<td class="border-none">
		@if($row->product_type != "variation")

			@if(!empty($booking_po_product_id) && $row->lock_discrepancy == 0)
				<button class="btn btn-blue font-12" onclick="show_discrepancy('{{ $booking_po_product_id }}','{{ $product_id }}');" >@lang('messages.discrepancy.add_view_discri')</button>
			@else
				<button class="btn btn-blue font-12" disabled="disabled">@lang('messages.discrepancy.add_view_discri')</button>
			@endif
		@endif
		<button type="submit" class="btn btn-blue font-12" form="{{$form_id}}" title="@lang('messages.common.save')"> @lang('messages.common.save') </button>
		@if($row->return_to_supplier == '1')
			<button type="button" class="btn btn-red font-12" title="@lang('messages.material_receipt.remove_product_title')" onclick="removeReturnToSupplierProduct(this)" form="{{$form_id}}"> 
				<span class="icon-moon icon-Delete text-white"></span> 
			</button>
		@endif
	</td>
</tr>

<tr>
	<td colspan="2" style="vertical-align: middle;">
		<div class="d-flex align-items-center my-3">
			<span class="font-12-dark mr-5">
				SKU<span class="font-12-dark bold color-blue ml-2">{{ !empty($row->sku) ? $row->sku : '-' }}</span>
			</span>
			<span class="font-14-dark">
				Supplier SKU<span class="bold color-blue ml-2">{{!empty($row->supplier_sku) ? $row->supplier_sku : '-'}}</span>
			</span>
		</div>
		@php
			$po_number = "";

			if($row->product_type == 'variation')
			{
				$po_number = !empty($parent_row->po_number) ? $parent_row->po_number : "";
			}
			else
			{
				$po_number = !empty($row->po_number) ? $row->po_number : "";
			}
		@endphp
		@if(!empty($po_number))
			<div class="d-flex align-items-center my-3">
				<span class="font-14-dark">
					@lang('messages.material_receipt.po_number')
					<span class="font-16-dark bold color-blue ml-2">
						{{ $po_number }}
					</span>
				</span>
			</div>	
		@endif
	</td>
	<td colspan="3" class="border-none v-align-middle">
		<div class="d-flex align-items-center mr-5">
			
			<button class="btn btn-more-detail mr-3" type="button" data-toggle="collapse" data-target="#collapse_{{$form_id}}" aria-expanded="{{ ($open_cases == true) ? 'true' : 'false' }}" aria-controls="collapse_{{$form_id}}">
			    <span class="icon-moon icon-Add"></span>
			</button>						
			
			@if(!empty($user_details[$row->scan_by_user_id]))
				<span class="font-1-dark color-purple ml-5">@lang('messages.material_receipt.last_scanned_by') : <span class="font-12-dark color-purple bold">{{ ucwords($user_details[$row->scan_by_user_id]['first_name'].' '.$user_details[$row->scan_by_user_id]['last_name']) }} {{  system_date_time($row->scan_date) }} </span></span>
			@endif
			
		</div>
	</td>

	@php
	$discrepancy_type=config('params.discrepancy_type');				
	@endphp

	@if(isset($final_desc[$row->booking_po_product_id]) && !empty($final_desc[$row->booking_po_product_id]) && !empty($discrepancy_type))
	<td class="border-none">
		<div class="descripencies-list">
			@foreach($final_desc[$row->booking_po_product_id] as $desc)
			@php
			
			$disc_status_type='';
			if(!empty($desc['status']))
			{
				$disc_status_type=discrepancy_status_type($desc['status']);
			}
			@endphp
			<div class="d-flex align-items-center">
				<span class="font-14-dark d-flex align-items-center"><i></i>{{ $desc['qty'] }} {{ @$discrepancy_type[$desc['discrepancy_type']] }}</span>
				@if(!empty($booking_po_product_id) && $row->lock_discrepancy == 0)
					<span class="icon-moon icon-Right-Arrow font-8 ml-2 pointer-cursor" onclick="show_discrepancy('{{ $booking_po_product_id }}','{{ $product_id }}');" ></span>
				@else
					<span class="icon-moon icon-Right-Arrow font-8 ml-2 pointer-cursor"></span>
				@endif
			</div>
			<p class="font-12-dark bold mb-2">{{ $disc_status_type }}</p>
			@endforeach						
		</div>
	</td>
	@endif	
	
</tr>

<tr>
	<td colspan="6" class="p-0">
		<div class="collapse {{ ($open_cases == true) ? 'show' : '' }}" id="collapse_{{$form_id}}" attr-form="{{$form_id}}">
		    <div class="case-location-box">
		    	<div class="left">
		    		<button type="submit" class="btn btn-blue font-12 px-4 btn-float-top" form="{{$form_id}}" title="Save">@lang('messages.common.save')</button>
		    		<ul>
		    			@if($row->product_type != "parent")
			    			<li>
			    				<p class="font-14-dark color-purple bold mb-4">@lang('messages.material_receipt.cases_and_barcode_best_before_date')</p>

			    				@php
			    					if($open_cases == true 
				    					&& empty($booking_po_product_id) 
				    					&& $row->po_is_variant == 0
				    					&& (!empty($row->po_best_before_date) || $inventory_case_details[$product_id])
				    				)
			    					{
			    						$sel_is_inner_outer_case = 1;

			    						if(!empty($row->po_best_before_date))
			    						{
			    							$sel_best_before = 1;
										}
									}
									else
									{
										if((!empty($row->is_inner_outer_case) && !empty($booking_po_product_id)))
				    					{
					    					$sel_is_inner_outer_case =  1;
					    				}

				    					if($row->is_best_before_date == '1' && !empty($booking_po_product_id))
				    					{
				    						$sel_best_before = '1';
				    					}
									}

									$case_disable = "";
									
									$case_onclick = "";
									
									if(!empty($product_case_details[$booking_po_product_id]) && ($sel_is_inner_outer_case == '1'))
									{
										$case_disable = "disabled-control";
										$case_onclick = 'onclick=sendValidationText("cases_and_barcodes")';
									}

								@endphp
			    				<label class="fancy-radio sm mr-5 {{$case_disable}}" {{$case_onclick}}>
			    					<input type="radio" name="is_inner_outer_case" value="1" onchange="setCasesDetails(this)" form="{{$form_id}}" {{ ($sel_is_inner_outer_case == '1') ? 'checked="checked"' : '' }}>
			    					<span><i></i>Yes</span>
			    				</label>
			    				<label class="fancy-radio sm {{$case_disable}}" {{ $case_onclick }}>
			    					<input type="radio" name="is_inner_outer_case" value="0" onchange="setCasesDetails(this)" form="{{$form_id}}"  {{ ($sel_is_inner_outer_case == 0) ? 'checked="checked"' : '' }}>
			    					<span><i></i>No</span>
			    				</label>
			    			</li>
			    			@php
			    				$best_before_onclick = "";
			    				$best_before_disable = "";
			    				if($row->put_away_quantity > 0)
			    				{
			    					$best_before_onclick = 'onclick=sendValidationText("best_before_date")';
			    					$best_before_disable = "disabled-control";
			    				}

			    			@endphp
			    			<li class="{{ ($sel_is_inner_outer_case == 1) ? '' : 'display-none' }} best-before-date-option">
			    				<label class="fancy-checkbox sm d-block {{$best_before_disable}}" {{ $best_before_onclick }}>
									<input type="checkbox" name="is_best_before_date" value="1" form="{{$form_id}}" onchange="setBestBeforeDate(this)" {{ ($sel_best_before == 1) ? 'checked="checked"' : '' }} {{ ($row->put_away_quantity > 0) ? "readonly='readonly'" : "" }} >
									<span class="font-14-dark color-purple bold"><i></i>@lang('messages.material_receipt.best_before_date')</span>
								</label>
							</li>
		    			@endif

		    			@if(empty($row->return_to_supplier))
			    			@if($row->product_type == "variation" || ($row->product_type=="normal" && $row->is_new_product != '0'))
			    			<!-- IF BLOCK -->

		    				@else
			    				@php
			    					if($open_cases == true 
					    				&& empty($booking_po_product_id)
			    						&& $row->po_is_variant == 1
			    					)
			    					{
				    					$sel_variants = 1;
				    				}
				    				else
				    				{
				    					if($row->is_variant == '1')
				    					{
				    						$sel_variants = 1;	
				    					}
				    				}	

			    					$variants_disable = "";
										
									$variants_onclick = "";
									
									if(!empty($product_case_details[$booking_po_product_id]) && !empty($sel_is_inner_outer_case))
									{
										$variants_disable = "disabled-control";
										
										$variants_onclick = 'onclick=sendValidationText("cases_and_barcodes")';
									}
			    				@endphp
				    			<li>
				    				<p class="font-14-dark color-purple bold mb-4">Variants</p>
				    				<label class="fancy-radio sm mr-5 {{ $variants_disable }}" {{ $variants_onclick }}>
				    					<input type="radio" name="is_variant" value="1" onchange="setVariants(this)" {{ $sel_variants == 1 ? 'checked="checked"' : '' }} form="{{$form_id}}">
				    					<span><i></i> Yes</span>
				    				</label>
				    				<label class="fancy-radio sm {{ $variants_disable }}" {{ $variants_onclick }}>
				    					<input type="radio" name="is_variant" value="0" onchange="setVariants(this)" {{ $sel_variants == 0 ? 'checked="checked"' : '' }} form="{{$form_id}}">
				    					<span><i></i> No</span>
				    				</label>
				    			</li>
			    			@endif
		    			@endif
		    		</ul>
		    	</div>

				<div class="right">
					
					<div class="product-case-detail {{ empty($row->is_inner_outer_case) ? 'display-none' : '' }}">
						<!-- Title Header -->
						<div class="card-titles">
							<div class="content">
								<div class="card-cols">
									<div class="barcode">
										<span class="font-14-dark bold">@lang('messages.material_receipt.barcode')</span>
									</div>
									<div class="case">
										<span class="font-14-dark bold">@lang('messages.material_receipt.case')</span>
									</div>
									<div class="inclide-in-cnt">
										<span class="font-14-dark bold">@lang('messages.material_receipt.include_in_count')</span>
									</div>
									<div class="qty-box">
										<span class="font-14-dark bold">@lang('messages.material_receipt.qty_per_box')</span>
									</div>
									<div class="box">
										<span class="font-14-dark bold">@lang('messages.material_receipt.no_of_boxes')</span>
									</div>
									<div class="total">
										<span class="font-14-dark bold">@lang('messages.material_receipt.total')</span>
									</div>
									<div class="blank">&nbsp;</div>
								</div>
							</div>

							<div class="add-more">
							</div>
						</div>
						<!-- Title Header END -->
						<div class="cases_blocks">	
							@include('material_receipt.list-ajax-case-details')
						</div>	
					</div>
					@if($row->product_type != "variation")
						<div class="product-varient mt-2" style="{{ $sel_variants == 0 ? 'display:none' : '' }}">
							<span class="font-14-dark bold">@lang('messages.material_receipt.variants')</span>

							<button type="button" class="btn btn-green font-12 ml-4" onclick="manageVariants(this,{{$product_id}})" form="{{$form_id}}">
							  @lang('messages.material_receipt.manage_variants')
							</button>
						</div>
					@endif
					<div class="form-group mt-4">
						<label class="font-14-dark bold mb-2">Comments</label>
						<textarea class="form-control" placeholder="Comment here" rows="4" name="comments" form="{{$form_id}}" >{{ !empty($row->comments) ? $row->comments : '' }}</textarea>
					</div>
				</div>
		    </div>
		</div>
	</td>
</tr>