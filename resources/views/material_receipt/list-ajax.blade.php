<div class="flex-one overflow-auto custom_fix_header">
	<table class="table custom-table cell-align-top product_list_table table_fix_header">
		<thead>
			<tr>						
				@php
					$sort_icon = "sorting";

					if($params['sort_by'] == 'title' && $params['sort_direction'] == 'asc')
					{
						$sort_icon = "sorting_asc";
						$nxt_sort_dir = "desc";
					}
					elseif($params['sort_by'] == 'title' && $params['sort_direction'] == 'desc')
					{
						$sort_icon = "sorting_desc";
						$nxt_sort_dir = "asc";
					}
				@endphp	
				<td class="w-30 {{$sort_icon}}" sort-by="title" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Product Info</small></td>

				@php
					$sort_icon = "sorting";

					if($params['sort_by'] == 'order' && $params['sort_direction'] == 'asc')
					{
						$sort_icon = "sorting_asc";
						$nxt_sort_dir = "desc";
					}
					elseif($params['sort_by'] == 'order' && $params['sort_direction'] == 'desc')
					{
						$sort_icon = "sorting_desc";
						$nxt_sort_dir = "asc";
					}
				@endphp	
				<td class="w-15 {{$sort_icon}}" sort-by="order" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Qty. Ordered</td>

				@php
					$sort_icon = "sorting";

					if($params['sort_by'] == 'delivery_note_qty' && $params['sort_direction'] == 'asc')
					{
						$sort_icon = "sorting_asc";
						$nxt_sort_dir = "desc";
					}
					elseif($params['sort_by'] == 'delivery_note_qty' && $params['sort_direction'] == 'desc')
					{
						$sort_icon = "sorting_desc";
						$nxt_sort_dir = "asc";
					}
				@endphp	
				<td class="w-12 {{$sort_icon}}" sort-by="delivery_note_qty" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Del. Note Qty.</td>

				@php
					$sort_icon = "sorting";

					if($params['sort_by'] == 'quantity_received' && $params['sort_direction'] == 'asc')
					{
						$sort_icon = "sorting_asc";
						$nxt_sort_dir = "desc";
					}
					elseif($params['sort_by'] == 'quantity_received' && $params['sort_direction'] == 'desc')
					{
						$sort_icon = "sorting_desc";
						$nxt_sort_dir = "asc";
					}
				@endphp	
				<td class="w-12 {{$sort_icon}}" sort-by="quantity_received" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Qty. Rec. </td>

				@php
					/*
					$sort_icon = "sorting";

					if($params['sort_by'] == 'location' && $params['sort_direction'] == 'asc')
					{
						$sort_icon = "sorting_asc";
						$nxt_sort_dir = "desc";
					}
					elseif($params['sort_by'] == 'location' && $params['sort_direction'] == 'desc')
					{
						$sort_icon = "sorting_desc";
						$nxt_sort_dir = "asc";
					}
					*/
				@endphp	
				<!-- <td class="w-10 {{$sort_icon}}" sort-by="location" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Location <small class="font-10-dark d-block">Scan Barcode</small></td> -->

				<td class="w-12 location_hide">Location <!-- <small class="font-10-dark d-block">Scan Barcode</small> --></td>

				@php
					$sort_icon = "sorting";

					if($params['sort_by'] == 'difference' && $params['sort_direction'] == 'asc')
					{
						$sort_icon = "sorting_asc";
						$nxt_sort_dir = "desc";
					}
					elseif($params['sort_by'] == 'difference' && $params['sort_direction'] == 'desc')
					{
						$sort_icon = "sorting_desc";
						$nxt_sort_dir = "asc";
					}
				@endphp	
				<td class="w-12 {{$sort_icon}}" sort-by="difference" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Qty. Diff.</td>
				<td class="w-20">
					<!-- <div class="d-inline-flex position-relative">
						<label class="fancy-checkbox sm">
							<input type="checkbox" name="descri[]" class="po_item_master">
							<span><i></i></span>
						</label>
						<div class="dropdown bulk-action-dropdown">
							<button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Bulk Action">
							<span class="icon-moon icon-Drop-Down-1">
							</span></button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
								<h4 class="title">Bulk Action</h4>
								<a id="debit_note_status" class="btn btn-blue" onclick="change_status_disc(1);">@lang('messages.material_receipt.debit_note_button')</a>
				                <a id="keep_it_status" class="btn btn-blue" onclick="change_status_disc(2);">@lang('messages.material_receipt.keep_it_button')</a>
				                <a id="dispose_of_status" class="btn btn-blue" onclick="change_status_disc(3);">@lang('messages.material_receipt.dispose_of_button')</a>
				                <a id="return_supplier_status" class="btn btn-blue" onclick="change_status_disc(4);">@lang('messages.material_receipt.return_supplier_button')</a>

							</div>
						</div>
					</div> -->
					Action
				</td>
			</tr>
		</thead>
		<tbody>
			@forelse($result as $row)
				@php
						$parent_row = array();
				@endphp
				@include('material_receipt.list-ajax-product-details')
				
				@if(!empty($var_result[$row->booking_po_product_id]) && $row->is_variant == '1')
					@php
						$parent_booking_po_product_id = $row->booking_po_product_id;
						$parent_row = $row;
						$row = array();
					@endphp
					@foreach($var_result[$parent_booking_po_product_id] as $row)
						@include('material_receipt.list-ajax-product-details')
					@endforeach
				@endif
			@empty
				<tr>
					<td colspan="100%" align="center">
						<p>@lang('messages.common.no_records_found')</p>
						@if($params['search_type'] == 'pending_products' && !empty($params['search']))
							<button class="btn btn-light-blue btn-header mt-3" onclick="newProductReturnToSupplier(this)">
								<span class="icon-moon icon-Reverse-Purchse-Order font-10 mr-2 ml-0"></span>
								@lang('messages.material_receipt.products_return_to_supplier')
							</button>
						@endif
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>				

	<!-- Inner/Outer Combine TEMPLATE FOR JS -->
	<div class="display-none" id="template_case_details">
		<div class="card-outer-inner mt-2">
			<div class="content">
				<div class="outer">
					<div class="card-cols">
						<div class="barcode">
							<div class="d-flex align-items-center">
								<input type="text" class="form-control font-14-dark bold mr-3 required_mr input_barcode"  name="inner_outer_case_detail[index][outer][barcode]" onchange="setInnerOuterBarcodeDetails(this)">
								<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
							</div>
						</div>
						<div class="case">
							<span class="font-14-dark">Outer Case</span>
						</div>
						<div class="inclide-in-cnt">
							<label class="fancy-radio sm mr-3">
								<input type="radio" name="inner_outer_case_detail[index][outer][is_include_count]" onchange="showHideMovetoLocation(this)" class="radio_for_outer" value="1" checked="checked" />
								<span class="font-12-dark"><i></i>Yes</span>
							</label>
							<label class="fancy-radio sm">
								<input type="radio" name="inner_outer_case_detail[index][outer][is_include_count]" onchange="showHideMovetoLocation(this)" class="radio_for_outer" value="0" />
								<span class="font-12-dark"><i></i>No</span>
							</label>
						</div>
						<div class="qty-box">
							<input type="text" class="form-control w-60 required_digit" name="inner_outer_case_detail[index][outer][qty_per_box]" only_digit onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="box">
							<input type="text" class="form-control w-60 required_digit" name="inner_outer_case_detail[index][outer][no_of_box]" only_digit onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="total">
							<span class="font-14-dark outer_total">0</span>
						</div>
					</div>
					
					<!-- Multiple Location Row  -->
					<div class="product-location-row for_outer">
						<div class="d-flex my-4 align-items-center">
							<span class="font-14-dark">Move Boxes</span>
							<input type="text" class="form-control w-60 mx-3 required_digit" name="inner_outer_case_detail[index][outer][qty][]" only_digit>
							<span class="font-14-dark">to Location</span>
							<div class="px-3 location-container">
								<input type="text" class="form-control w-120 required_mr set_location_details" name="inner_outer_case_detail[index][outer][location][]" autocomplete="off">
							</div>
							<div class="best-date ml-5">
								<span class="font-14-dark">Best Before Date</span>
								<input type="text" class="form-control w-120 mx-3 datepicker required_mr" readonly="readonly"  name="inner_outer_case_detail[index][outer][best_before_date][]">
							</div>
							<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForOuter(this)">
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
								<input type="text" class="form-control font-14-dark bold mr-3 input_barcode"  name="inner_outer_case_detail[index][inner][barcode]">
								<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
							</div>
						</div>
						<div class="case">
							<span class="font-14-dark">Inner Case</span>
						</div>
						<div class="inclide-in-cnt">
							<label class="fancy-radio sm mr-3">
								<input type="radio" name="inner_outer_case_detail[index][inner][is_include_count]" class="radio_for_inner" onchange="showHideMovetoLocation(this)" value="1" />
								<span class="font-12-dark"><i></i>Yes</span>
							</label>
							<label class="fancy-radio sm">
								<input type="radio" name="inner_outer_case_detail[index][inner][is_include_count]" class="radio_for_inner" onchange="showHideMovetoLocation(this)" value="0" checked="checked" />
								<span class="font-12-dark"><i></i>No</span>
							</label>
						</div>
						<div class="qty-box">
							<input type="text" class="form-control w-60" name="inner_outer_case_detail[index][inner][qty_per_box]" only_digit onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="box">
							<input type="text" class="form-control w-60 display-none" name="inner_outer_case_detail[index][inner][no_of_box]" only_digit onkeyup="setTotalCaseQty(this)">
						</div>
						<div class="total"><span class="font-14-dark inner_total"></span></div>
					</div>

					<!-- Multiple Location Row  -->
					<div class="product-location-row for_inner">
						<div class="d-flex my-4 align-items-center">
							<span class="font-14-dark">Move Boxes</span>
							<input type="text" class="form-control w-60 mx-3" name="inner_outer_case_detail[index][inner][qty][]" only_digit>
							<span class="font-14-dark">to Location</span>
							<div class="px-3 location-container">
								<input type="text" class="form-control w-120 set_location_details" name="inner_outer_case_detail[index][inner][location][]" autocomplete="off">
							</div>
							<div class="best-date ml-5">
								<span class="font-14-dark">Best Before Date</span>
								<input type="text" class="form-control w-120 mx-3 datepicker" readonly="readonly"  name="inner_outer_case_detail[index][inner][best_before_date][]">
							</div>
							<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForInner(this)">
								<span class="icon-moon icon-Add font-10"></span>
							</a>
						</div>
					</div>
					<!-- Multiple Location Row END  -->
				</div>
			</div>
			<div class="add-more">
				<a href="javascript:void(0)" class="case_action" onclick="removeCase(this)">
					<span class="icon-moon color-red icon-Close font-10"></span>
				</a>
			</div>
		</div>
	</div>
	<!-- Inner/Outer Combine END -->

	<!-- Loose product TEMPLATE FOR JS-->
	<div class="display-none" id="template_loose_location">
		<div class="card-loose mt-2">
			<div class="content">
				<div class="card-cols">
					<div class="barcode">
						<div class="d-flex align-items-center">
							<input type="text" class="form-control font-14-dark bold mr-3 input_barcode"  name="inner_outer_case_detail[loose][index][barcode]">
							<span class="icon-moon icon-Print font-18" onclick="printBarcode(this)"></span>
						</div>
					</div>
					<div class="case">
						<span class="font-14-dark">@lang('messages.common.case_type_single')</span>
					</div>
					<div class="inclide-in-cnt">&nbsp;</div>
					<div class="qty-box">
						<input type="text" class="form-control w-60" name="inner_outer_case_detail[loose][index][qty_per_box]" only_digit onkeyup="setTotalCaseQty(this)">
					</div>
					<div class="box">&nbsp;</div>
					<div class="total">
						<span class="font-14-dark loose_total">0</span>
					</div>
				</div>
				<div class="product-location-row for_loose">
					<div class="d-flex my-4 align-items-center">
						<span class="font-14-dark">Move</span>
						<input type="text" class="form-control w-60 mx-3" name="inner_outer_case_detail[loose][index][qty][]" only_digit>
						<span class="font-14-dark">to Location</span>
						<div class="px-3 location-container">
							<input type="text" class="form-control w-120 set_location_details" name="inner_outer_case_detail[loose][index][location][]" autocomplete="off">
						</div>
						<div class="best-date ml-5">
							<span class="font-14-dark">Best Before Date</span>
							<input type="text" class="form-control w-120 mx-3 datepicker" readonly="readonly" name="inner_outer_case_detail[loose][index][best_before_date][]">
						</div>
						<a href="javascript:void(0)" class="move_location_action" onclick="addMoveLocationForLoose(this)">
							<span class="icon-moon icon-Add font-10"></span>
						</a>
					</div>
				</div>
			</div>
			<div class="add-more">
				<a href="javascript:void(0)" class="loose_location_action" color-red onclick="removeLoseLocation(this)">
					<span class="icon-moon color-red icon-Close font-10"></span>
				</a>
			</div>
		</div>
	</div>
	<!-- Loose product TEMPLATE END -->

	<!-- Multiple Location Row TEMPLATE FOR JS -->
	<div class="display-none" id="template_move_prduct_to_location">
		<div class="product-location-row">
			<div class="d-flex my-4 align-items-center">
				<span class="font-14-dark">Move Boxes</span>
				<input type="text" class="form-control w-60 mx-3 required_digit" name="inner_outer_case_detail[index][outer][qty][]" only_digit>
				<span class="font-14-dark location-container">to Location</span>
				<div class="px-3">
					<input type="text" class="form-control w-120 required_mr set_location_details" name="inner_outer_case_detail[index][outer][location][]" autocomplete="off">
				</div>
				<div class="best-date ml-5">
					<span class="font-14-dark">Best Before Date</span>
					<input type="text" class="form-control w-120 mx-3 datepicker required_mr" readonly="readonly" name="inner_outer_case_detail[index][outer][best_before_date][]">
				</div>
				<a href="javascript:void(0)" class="move_location_action" onclick="removeMoveLocationForOuter(this)">
					<span class="icon-moon color-red icon-Close font-10"></span>
				</a>
			</div>
		</div>
	</div>	
	<!-- Multiple Location Row TEMPLATE END  -->
	
	<!-- PHOTOBOOTH CASE TEMPLATE -->
	<div class="display-none" id="template_case_photobooth">
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
	</div>
	<!-- PHOTOBOOTH CASE TEMPLATE END -->

</div>
<div class="flex-none">
	@if(!$result->isEmpty())
	<div class="material-receipt-footer">
		<div>
			<span class="font-12-dark">Showing {{($result->currentpage()-1)*$result->perpage()+1}} to {{$result->currentpage()*$result->perpage()}} of  {{$result->total()}}</span>
		</div>
		<div class="custom-pagination">
			{{$result->links('pagination.index')}}
		</div>
		<div class="custom-per-page">
			<label class="show-page">
				Show  
				<select id="per_page_value_dropdown">
					<option value="10">10</option>
					<option value="25" {{ ($params['per_page'] == 25) ? "selected='selected'" : '' }} >25</option>
					<option value="50" {{ ($params['per_page'] == 50) ? "selected='selected'" : '' }} >50</option>
				</select>
				entries
			</label>
		</div>	
	</div>
	@endif
</div>