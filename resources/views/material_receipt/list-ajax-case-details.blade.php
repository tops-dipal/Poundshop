<!-- Each of outer case -->
@if(!empty($product_case_details[$booking_po_product_id][3]))
	@php
		$case_key = 0;
	@endphp
	@forelse($product_case_details[$booking_po_product_id][3] as $outer_case_details)
		@php
			$disabled_txt = "";

			$case_key = $case_key+1;

			$inner_case_details = !empty($outer_case_details['inner_cases'][0]) ? $outer_case_details['inner_cases'][0] : array();
			if($outer_case_details['put_away_started'])
			{
				$disabled_txt = 'disabled-control';
			}
		@endphp
		<div class="card-outer-inner mt-2">
			<div class="content">
				<div class="outer">
					<div class="card-cols">
						<div class="barcode">
							<div class="d-flex align-items-center">
								<input type="text" class="form-control font-14-dark bold mr-3 required_mr input_barcode {{$disabled_txt}}"  name="inner_outer_case_detail[{{ $case_key }}][outer][barcode]" value="{{ $outer_case_details['barcode'] }}" form="{{$form_id}}" nameattrindex="{{ $case_key }}" onchange="setInnerOuterBarcodeDetails(this)">
								
								<input type="hidden" class="outer_id {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][outer][id]" form="{{$form_id}}" value="{{ $outer_case_details['id'] }}">
								<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
							</div>
						</div>
						<div class="case">
							<span class="font-14-dark">Outer Case</span>
						</div>
						<div class="inclide-in-cnt">
							<label class="fancy-radio sm mr-3 {{$disabled_txt}}">
								<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][outer][is_include_count]" onchange="showHideMovetoLocation(this)" class="radio_for_outer" value="1"  {{ ($outer_case_details['is_include_count'] == 1) ? 'checked="checked"' : '' }} form="{{$form_id}}" nameattrindex="{{$case_key}}"/>
								<span class="font-12-dark"><i></i>Yes</span>
							</label>
							<label class="fancy-radio sm {{$disabled_txt}}">
								<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][outer][is_include_count]" onchange="showHideMovetoLocation(this)" class="radio_for_outer" value="0" form="{{$form_id}}" nameattrindex="{{$case_key}}" {{ ($outer_case_details['is_include_count'] == 0) ? 'checked="checked"' : '' }} />
								<span class="font-12-dark"><i></i>No</span>
							</label>
						</div>
						<div class="qty-box">
							<input type="text" class="form-control w-60 required_digit {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][outer][qty_per_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ $outer_case_details['qty_per_box'] }}" onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="box">
							<input type="text" class="form-control w-60 required_digit {{ ($outer_case_details['is_include_count'] == 0) ? 'display-none' : '' }} {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][outer][no_of_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ $outer_case_details['no_of_box'] }}" onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="total">
							<span class="font-14-dark outer_total">{{ !empty($outer_case_details['total']) ? $outer_case_details['total'] : @$inner_case_details['total'] }}</span>
						</div>
					</div>
					
					<!-- Multiple Location Row  -->
					@if($outer_case_details['is_include_count'] == 1 && !empty($outer_case_details['case_locations']))
						@php
							$outer_loc_key = 0;
						@endphp
						@foreach($outer_case_details['case_locations'] as $case_locations)
							@php
								$outer_loc_key = $outer_loc_key + 1;
							@endphp
						<div class="product-location-row for_outer">
							<div class="d-flex my-4 align-items-center">
								<input type="hidden" class="case_location_id" name="inner_outer_case_detail[{{ $case_key }}][outer][location_id][]" form="{{$form_id}}" value="{{ $case_locations['id'] }}">

								<span class="font-14-dark">Move Boxes</span>
								<input type="text" class="form-control w-60 mx-3 required_digit {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][outer][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ $case_locations['boxes'] }}">
								<span class="font-14-dark">to Location</span>
								<div class="px-3 location-container">
									<input type="text" class="form-control w-120 required_mr set_location_details {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][outer][location][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ $case_locations['location_details']['location'] }}" autocomplete="off">
									@if(!empty($case_locations['location_details']['type_of_location']))
										<span class="location_type font-10-dark bold d-block mt-1">{{ LocationType($case_locations['location_details']['type_of_location']) }}</span>
									@endif
								</div>
								<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
									<span class="font-14-dark">Best Before Date</span>
									<input type="text" class="form-control w-120 mx-3 datepicker required_mr {{$disabled_txt}}" readonly="readonly"  name="inner_outer_case_detail[{{ $case_key }}][outer][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ !empty($case_locations['best_before_date']) ? system_date($case_locations['best_before_date'])  : '' }}">
								</div>
								@if(empty($disabled_txt))
									@if($outer_loc_key == '1')
										<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForOuter(this)" form="{{$form_id}}">
											<span class="icon-moon icon-Add font-10"></span>
										</a>
									@else
										<a href="javascript:void(0)" class="move_location_action" onclick="removeMoveLocationForOuter(this)" form="{{$form_id}}">
											<span class="icon-moon color-red icon-Close font-10"></span>
										</a>
									@endif
								@endif
							</div>
						</div>
						@endforeach
					@else
						<div class="product-location-row for_outer {{ $outer_case_details['is_include_count'] == 1 ? "" : 'display-none' }}">
							<div class="d-flex my-4 align-items-center">
								<span class="font-14-dark">Move Boxes</span>
								<input type="text" class="form-control w-60 mx-3 required_digit {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][outer][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
								<span class="font-14-dark">to Location</span>
								<div class="px-3 location-container">
									<input type="text" class="form-control w-120 required_mr set_location_details {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][outer][location][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" autocomplete="off">
								</div>
								<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
									<span class="font-14-dark">Best Before Date</span>
									<input type="text" class="form-control w-120 mx-3 datepicker required_mr {{$disabled_txt}}" readonly="readonly"  name="inner_outer_case_detail[{{ $case_key }}][outer][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
								</div>
								
								@if(empty($disabled_txt))
									<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForOuter(this)" form="{{$form_id}}">
										<span class="icon-moon icon-Add font-10"></span>
									</a>
								@endif
							</div>
						</div>	
					@endif
					<!-- Multiple Location Row END  -->
				</div>														

				<div class="inner">
					<div class="card-cols">
						<div class="barcode">
							<div class="d-flex align-items-center">
								<input type="text" class="form-control font-14-dark bold mr-3 input_barcode {{$disabled_txt}}"  name="inner_outer_case_detail[{{ $case_key }}][inner][barcode]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ !empty($inner_case_details['barcode']) ? $inner_case_details['barcode'] : '' }}">

								@if(!empty($inner_case_details['id']))
									<input type="hidden" class="inner_id" name="inner_outer_case_detail[{{ $case_key }}][inner][id]" form="{{$form_id}}" value="{{ $inner_case_details['id'] }}">
								@endif

								<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
							</div>
						</div>
						<div class="case">
							<span class="font-14-dark">Inner Case</span>
						</div>
						<div class="inclide-in-cnt">
							<label class="fancy-radio sm mr-3 {{$disabled_txt}}">
								<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][inner][is_include_count]" class="radio_for_inner" onchange="showHideMovetoLocation(this)" value="1" form="{{$form_id}}" nameattrindex="{{$case_key}}" {{ (@$inner_case_details['is_include_count'] == '1') ? 'checked="checked"' : '' }}/>
								<span class="font-12-dark"><i></i>Yes</span>
							</label>
							<label class="fancy-radio sm {{$disabled_txt}}">
								<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][inner][is_include_count]" class="radio_for_inner" onchange="showHideMovetoLocation(this)" value="0" form="{{$form_id}}" nameattrindex="{{$case_key}}" {{ (@$inner_case_details['is_include_count'] == '0' || empty($inner_case_details['is_include_count'])) ? 'checked="checked"' : '' }}/>
								<span class="font-12-dark"><i></i>No</span>
							</label>
						</div>
						<div class="qty-box">
							<input type="text" class="form-control w-60 {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][inner][qty_per_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ !empty($inner_case_details['qty_per_box']) ? $inner_case_details['qty_per_box'] : '' }}" onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="box">
							<input type="text" class="form-control w-60 {{ (empty($inner_case_details['is_include_count'])) ? 'display-none' : '' }} {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][inner][no_of_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ !empty($inner_case_details['no_of_box']) ? $inner_case_details['no_of_box'] : '' }}" onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="total"><span class="font-14-dark inner_total">
							
						</span></div>
					</div>

					<!-- Multiple Location Row  -->
					@if(@$inner_case_details['is_include_count'] == '1' && !empty($inner_case_details['case_locations']))
						@php
							$inner_loc_key = 0;
						@endphp
						@foreach($inner_case_details['case_locations'] as $inner_case_locations)
							@php
								$inner_loc_key = $inner_loc_key + 1;
							@endphp
							<div class="product-location-row for_inner">
								
								<input type="hidden" class="case_location_id" name="inner_outer_case_detail[{{ $case_key }}][inner][location_id][]" form="{{$form_id}}" value="{{ $inner_case_locations['id'] }}">

								<div class="d-flex my-4 align-items-center">
									<span class="font-14-dark">Move Boxes</span>
									<input type="text" class="form-control w-60 mx-3 {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][inner][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ !empty($inner_case_locations['boxes']) ? $inner_case_locations['boxes'] : '' }}">
									<span class="font-14-dark">to Location</span>
									<div class="px-3 location-container">
										<input type="text" class="form-control w-120 set_location_details {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][inner][location][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ !empty($inner_case_locations['location_details']['location']) ? $inner_case_locations['location_details']['location'] : '' }}" autocomplete="off">

										@if(!empty(!empty($inner_case_locations['location_details'])))
											<span class="location_type font-10-dark bold d-block mt-1">{{ LocationType($inner_case_locations['location_details']['type_of_location']) }}</span>
										@endif

									</div>
									<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
										<span class="font-14-dark">Best Before Date</span>
										<input type="text" class="form-control w-120 mx-3 datepicker {{$disabled_txt}}" readonly="readonly"  name="inner_outer_case_detail[{{ $case_key }}][inner][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="{{ !empty($inner_case_locations['best_before_date']) ? system_date($inner_case_locations['best_before_date']) : '' }}">
									</div>

									@if(empty($disabled_txt))
										@if($inner_loc_key == 1)
											<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForInner(this)" form="{{$form_id}}">
												<span class="icon-moon icon-Add font-10"></span>
											</a>
										@else
											<a href="javascript:void(0)" class="move_location_action" onclick="removeMoveLocationForOuter(this)" form="{{$form_id}}">
												<span class="icon-moon color-red icon-Close font-10"></span>
											</a>
										@endif
									@endif
								</div>
							</div>
						@endforeach
					@else
						<div class="product-location-row for_inner {{ @$inner_case_details['is_include_count'] == '1' ? '' : 'display-none' }}">
							<div class="d-flex my-4 align-items-center">
								<span class="font-14-dark">Move Boxes</span>
								<input type="text" class="form-control w-60 mx-3 {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][inner][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
								<span class="font-14-dark">to Location</span>
								<div class="px-3 location-container">
									<input type="text" class="form-control w-120 set_location_details {{$disabled_txt}}" name="inner_outer_case_detail[{{ $case_key }}][inner][location][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" autocomplete="off">
								</div>
								<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
									<span class="font-14-dark">Best Before Date</span>
									<input type="text" class="form-control w-120 mx-3 datepicker {{$disabled_txt}}" readonly="readonly"  name="inner_outer_case_detail[{{ $case_key }}][inner][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
								</div>
								@if(empty($disabled_txt))
									<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForInner(this)" form="{{$form_id}}">
										<span class="icon-moon icon-Add font-10"></span>
									</a>
								@endif
							</div>
						</div>	
					@endif	
					<!-- Multiple Location Row END  -->
				</div>
			</div>
			<div class="add-more">
				@if($case_key == '1')
					<a href="javascript:void(0)" class="case_action" onclick="addCase(this)" form="{{$form_id}}">
						<span class="icon-moon icon-Add font-10"></span>
					</a>
				@elseif(empty($disabled_txt))
					<a href="javascript:void(0)" class="case_action" onclick="removeCase(this)" form="{{$form_id}}">
						<span class="icon-moon color-red icon-Close font-10"></span>
					</a>
				@endif
			</div>
		</div>
	@endforeach
@else
	@php
		$case_key = 1;
	@endphp
	<div class="card-outer-inner mt-2">
		<div class="content">
			<div class="outer">
				<div class="card-cols">
					<div class="barcode">
						<div class="d-flex align-items-center">
							<input type="text" class="form-control font-14-dark bold mr-3 required_mr input_barcode"  name="inner_outer_case_detail[{{ $case_key }}][outer][barcode]" value="" form="{{$form_id}}" nameattrindex="{{ $case_key }}" onchange="setInnerOuterBarcodeDetails(this)">
							<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
						</div>
					</div>
					<div class="case">
						<span class="font-14-dark">Outer Case</span>
					</div>
					<div class="inclide-in-cnt">
						<label class="fancy-radio sm mr-3">
							<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][outer][is_include_count]" onchange="showHideMovetoLocation(this)" class="radio_for_outer" value="1"  checked="checked" form="{{$form_id}}" nameattrindex="{{$case_key}}"/>
							<span class="font-12-dark"><i></i>Yes</span>
						</label>
						<label class="fancy-radio sm">
							<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][outer][is_include_count]" onchange="showHideMovetoLocation(this)" class="radio_for_outer" value="0" form="{{$form_id}}" nameattrindex="{{$case_key}}" />
							<span class="font-12-dark"><i></i>No</span>
						</label>
					</div>
					<div class="qty-box">
						<input type="text" class="form-control w-60 required_digit" name="inner_outer_case_detail[{{ $case_key }}][outer][qty_per_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" onkeyup="setTotalCaseQty(this)">
					</div>
					<div class="box">
						<input type="text" class="form-control w-60 required_digit" name="inner_outer_case_detail[{{ $case_key }}][outer][no_of_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" onkeyup="setTotalCaseQty(this)">
					</div>
					<div class="total">
						<span class="font-14-dark outer_total">0</span>
					</div>
				</div>
				
				<!-- Multiple Location Row  -->
				<div class="product-location-row for_outer">
					<div class="d-flex my-4 align-items-center">
						<span class="font-14-dark">Move Boxes</span>
						<input type="text" class="form-control w-60 mx-3 required_digit" name="inner_outer_case_detail[{{ $case_key }}][outer][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
						<span class="font-14-dark">to Location</span>
						<div class="px-3 location-container">
							<input type="text" class="form-control w-120 required_mr set_location_details" name="inner_outer_case_detail[{{ $case_key }}][outer][location][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" autocomplete="off">
						</div>
						<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
							<span class="font-14-dark">Best Before Date</span>
							<input type="text" class="form-control w-120 mx-3 datepicker required_mr" readonly="readonly"  name="inner_outer_case_detail[{{ $case_key }}][outer][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
						</div>
						
						<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForOuter(this)" form="{{$form_id}}">
							<span class="icon-moon icon-Add font-10"></span>
						</a>
					</div>
				</div>	
				<!-- Multiple Location Row END  -->
			</div>														

			<div class="inner">
				<div class="card-cols">
					<div class="barcode">
						<div class="d-flex align-items-center">
							<input type="text" class="form-control font-14-dark bold mr-3 input_barcode"  name="inner_outer_case_detail[{{ $case_key }}][inner][barcode]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">

							<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
						</div>
					</div>
					<div class="case">
						<span class="font-14-dark">Inner Case</span>
					</div>
					<div class="inclide-in-cnt">
						<label class="fancy-radio sm mr-3">
							<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][inner][is_include_count]" class="radio_for_inner" onchange="showHideMovetoLocation(this)" value="1" form="{{$form_id}}" nameattrindex="{{$case_key}}" />
							<span class="font-12-dark"><i></i>Yes</span>
						</label>
						<label class="fancy-radio sm">
							<input type="radio" name="inner_outer_case_detail[{{ $case_key }}][inner][is_include_count]" class="radio_for_inner" onchange="showHideMovetoLocation(this)" value="0" form="{{$form_id}}" nameattrindex="{{$case_key}}" checked="checked" />
							<span class="font-12-dark"><i></i>No</span>
						</label>
					</div>
					<div class="qty-box">
						<input type="text" class="form-control w-60" name="inner_outer_case_detail[{{ $case_key }}][inner][qty_per_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" onkeyup="setTotalCaseQty(this)">
					</div>
					<div class="box">
						<input type="text" class="form-control w-60 display-none" name="inner_outer_case_detail[{{ $case_key }}][inner][no_of_box]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" onkeyup="setTotalCaseQty(this)">
					</div>
					<div class="total"><span class="font-14-dark inner_total">
						
					</span></div>
				</div>

				<!-- Multiple Location Row  -->
				<div class="product-location-row for_inner display-none">
					<div class="d-flex my-4 align-items-center">
						<span class="font-14-dark">Move Boxes</span>
						<input type="text" class="form-control w-60 mx-3" name="inner_outer_case_detail[{{ $case_key }}][inner][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
						<span class="font-14-dark">to Location</span>
						<div class="px-3 location-container">
							<input type="text" class="form-control w-120 set_location_details" name="inner_outer_case_detail[{{ $case_key }}][inner][location][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="" autocomplete="off">
						</div>
						<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
							<span class="font-14-dark">Best Before Date</span>
							<input type="text" class="form-control w-120 mx-3 datepicker" readonly="readonly"  name="inner_outer_case_detail[{{ $case_key }}][inner][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$case_key}}" value="">
						</div>
						
						<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForInner(this)" form="{{$form_id}}">
							<span class="icon-moon icon-Add font-10"></span>
						</a>
					</div>
				</div>		
				<!-- Multiple Location Row END  -->
			</div>
		</div>
		<div class="add-more">
			<a href="javascript:void(0)" class="case_action" onclick="addCase(this)" form="{{$form_id}}">
				<span class="icon-moon icon-Add font-10"></span>
			</a>
		</div>
	</div>
@endif
<!-- end each of outer case -->

<!-- loose location -->
@php
	$loose_case_key = 0;
@endphp
@if(!empty($product_case_details[$booking_po_product_id][1]))
	@foreach($product_case_details[$booking_po_product_id][1] as $loose_case_details)
		@php
			$disabled_txt = "";

			if($loose_case_details['put_away_started'])
			{
				$disabled_txt = 'disabled-control';
			}
			
			$loose_case_key = $loose_case_key + 1;
		@endphp
		<div class="card-loose mt-2">
			<div class="content">
				<div class="card-cols">
					<div class="barcode">
						<div class="d-flex align-items-center">
							<input type="text" class="form-control font-14-dark bold mr-3 input_barcode {{$disabled_txt}}"  name="inner_outer_case_detail[loose][{{ $loose_case_key }}][barcode]" form="{{$form_id}}" value="{{ !empty($loose_case_details['barcode']) ? $loose_case_details['barcode'] : '' }}" nameattrindex="{{ $loose_case_key }}">
							<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
							<input type="hidden" class="loose_id" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][id]" form="{{$form_id}}" value="{{ $loose_case_details['id'] }}">
						</div>
					</div>
					<div class="case">
						<span class="font-14-dark">@lang('messages.common.case_type_single')</span>
					</div>
					<div class="inclide-in-cnt">&nbsp;</div>
					<div class="qty-box">
						<input type="text" class="form-control w-60 {{$disabled_txt}}" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][qty_per_box]" only_digit form="{{$form_id}}" value="{{ !empty($loose_case_details['qty_per_box']) ? $loose_case_details['qty_per_box'] : '' }}" nameattrindex="{{ $loose_case_key }}" onkeyup="setTotalCaseQty(this)">
					</div>
					<div class="box">&nbsp;</div>
					<div class="total">
						<span class="font-14-dark loose_total">{{ !empty($loose_case_details['total']) ? $loose_case_details['total'] : 0 }}</span>
					</div>
				</div>

				<!-- Multiple Location Row  -->
				@if(!empty($loose_case_details['case_locations']))
					@php
						$loose_loc_key = 0;
					@endphp
					@foreach($loose_case_details['case_locations'] as $loose_case_locations)
						@php
							$loose_loc_key = $loose_loc_key + 1;
						@endphp
						<div class="product-location-row for_loose">
							
							<input type="hidden" class="case_location_id" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][location_id][]" form="{{$form_id}}" value="{{ $loose_case_locations['id'] }}">

							<div class="d-flex my-4 align-items-center">
								<span class="font-14-dark">Move</span>
								<input type="text" class="form-control w-60 mx-3 {{$disabled_txt}}" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$loose_case_key}}" value="{{ !empty($loose_case_locations['qty']) ? $loose_case_locations['qty'] : '' }}">
								<span class="font-14-dark">to Location</span>
								<div class="px-3 location-container">
									<input type="text" class="form-control w-120 set_location_details {{$disabled_txt}}"  name="inner_outer_case_detail[loose][{{ $loose_case_key }}][location][]" form="{{$form_id}}" nameattrindex="{{$loose_case_key}}" value="{{ !empty($loose_case_locations['location_details']['location']) ? $loose_case_locations['location_details']['location'] : '' }}" autocomplete="off">

									<span class="location_type font-10-dark bold d-block mt-1">{{ LocationType($loose_case_locations['location_details']['type_of_location']) }}</span>
								</div>
								<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
									<span class="font-14-dark">Best Before Date</span>
									<input type="text" class="form-control w-120 mx-3 datepicker {{$disabled_txt}}" readonly="readonly"  name="inner_outer_case_detail[loose][{{ $loose_case_key }}][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$loose_case_key}}" value="{{ !empty($loose_case_locations['best_before_date']) ? system_date($loose_case_locations['best_before_date']) : '' }}">
								</div>

								@if(empty($disabled_txt))
									@if($loose_loc_key == 1)
										<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForLoose(this)" form="{{$form_id}}">
											<span class="icon-moon icon-Add font-10"></span>
										</a>
									@else
										<a href="javascript:void(0)" class="move_location_action" onclick="removeMoveLocationForOuter(this)" form="{{$form_id}}">
											<span class="icon-moon color-red icon-Close font-10"></span>
										</a>
									@endif
								@endif
							</div>
						</div>
					@endforeach
				@else
					<div class="product-location-row for_loose">
						<div class="d-flex my-4 align-items-center">
							<span class="font-14-dark">Move</span>
							<input type="text" class="form-control w-60 mx-3 {{$disabled_txt}}" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][qty][]" only_digit form="{{$form_id}}" nameattrindex="{{$loose_case_key}}" value="">
							<span class="font-14-dark">to Location</span>
							<div class="px-3 location-container">
								<input type="text" class="form-control w-120 set_location_details {{$disabled_txt}}" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][location][]" form="{{$form_id}}" nameattrindex="{{$loose_case_key}}" value="" autocomplete="off">
							</div>
							<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
								<span class="font-14-dark">Best Before Date</span>
								<input type="text" class="form-control w-120 mx-3 datepicker {{$disabled_txt}}" readonly="readonly"  name="inner_outer_case_detail[loose][{{ $loose_case_key }}][best_before_date][]" form="{{$form_id}}" nameattrindex="{{$loose_case_key}}" value="">
							</div>
							
							@if(empty($disabled_txt))
								<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForInner(this)" form="{{$form_id}}">
									<span class="icon-moon icon-Add font-10"></span>
								</a>
							@endif
						</div>
					</div>	
				@endif	
				<!-- Multiple Location Row END  -->
			</div>
			<div class="add-more">
				@if($loose_case_key == '1')
					<a href="javascript:void(0)" class="loose_location_action" color-red onclick="addLoseLocation(this)" form="{{$form_id}}">
						<span class="icon-moon icon-Add font-10"></span>
					</a>
				@elseif(empty($disabled_txt))
					<a href="javascript:void(0)" class="loose_location_action" color-red onclick="removeLoseLocation(this)" form="{{$form_id}}">
						<span class="icon-moon color-red icon-Close font-10"></span>
					</a>
				@endif
			</div>
		</div>
	@endforeach
@else
	@php
		$loose_case_key = $loose_case_key + 1;
	@endphp
	<div class="card-loose mt-2">
		<div class="content">
			<div class="card-cols">
				<div class="barcode">
					<div class="d-flex align-items-center">
						<input type="text" class="form-control font-14-dark bold mr-3 input_barcode"  name="inner_outer_case_detail[loose][{{ $loose_case_key }}][barcode]" form="{{$form_id}}" value="" nameattrindex="{{ $loose_case_key }}">
						<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
					</div>
				</div>
				<div class="case">
					<span class="font-14-dark">@lang('messages.common.case_type_single')</span>
				</div>
				<div class="inclide-in-cnt">&nbsp;</div>
				<div class="qty-box">
					<input type="text" class="form-control w-60" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][qty_per_box]" only_digit form="{{$form_id}}" value="" nameattrindex="{{ $loose_case_key }}" onkeyup="setTotalCaseQty(this)">
				</div>
				<div class="box">&nbsp;</div>
				<div class="total">
					<span class="font-14-dark loose_total">0</span>
				</div>
			</div>

			<!-- Multiple Location Row TEMPLATE FOR JS -->
			<div class="product-location-row for_loose">
				<div class="d-flex my-4 align-items-center">
					<span class="font-14-dark">Move</span>
					<input type="text" class="form-control w-60 mx-3" name="inner_outer_case_detail[loose][{{ $loose_case_key}}][qty][]" only_digit nameattrindex="{{ $loose_case_key }}" form="{{$form_id}}">
					<span class="font-14-dark">to Location</span>
					<div class="px-3 location-container">
						<input type="text" class="form-control w-120 set_location_details" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][location][]" nameattrindex="{{ $loose_case_key }}" form="{{$form_id}}" autocomplete="off">
					</div>
					<div class="best-date ml-5 {{ empty($row->is_best_before_date) ? 'display-none' : '' }}">
						<span class="font-14-dark">Best Before Date</span>
						<input type="text" class="form-control w-120 mx-3 datepicker" readonly="readonly" name="inner_outer_case_detail[loose][{{ $loose_case_key }}][best_before_date][]" nameattrindex="{{ $loose_case_key }}" form="{{$form_id}}">
					</div>
					<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForLoose(this)" form="{{$form_id}}">
						<span class="icon-moon icon-Add font-10"></span>
					</a>
				</div>
			</div>
			<!-- Multiple Location Row TEMPLATE END  -->

		</div>
		<div class="add-more">
			<a href="javascript:void(0)" class="loose_location_action" color-red onclick="addLoseLocation(this)" form="{{$form_id}}">
				<span class="icon-moon icon-Add font-10"></span>
			</a>
		</div>
	</div>
@endif
<!-- loose location end-->

<!-- PhotoBooth -->
@if($set_photobooth == 1)
	<div class="card-photobooth mt-2">
		<div class="content">
			<div class="card-cols">
				<div class="barcode">
					@lang('messages.material_receipt.photobooth')
				</div>
				<div class="case">&nbsp;</div>
				<div class="inclide-in-cnt">&nbsp;</div>
				<div class="qty-box">
					1
				</div>
				<div class="box">&nbsp;</div>
				<div class="total">
					<span class="font-14-dark loose_total">1</span>
				</div>
			</div>
		</div>
		<div class="add-more">
			
		</div>
	</div>
@endif
<!-- End PhotoBooth -->
