
@php
	$variations = array();

	$variation_theme_id = $select_data($magento_posting_details, $product_master_details, 'variation_theme_id', 'variation_theme_id');

	if(!empty($magento_posting_details->variation))
	{
		$variations = $magento_posting_details->variation;
		$variation_theme_details = $magento_posting_details->variation_theme_detatils;
	}
	else
	{
		$variations = $product_master_details->variation;
		$variation_theme_details = $product_master_details->variation_theme_detatils;
	}
@endphp

@if(!empty($variations))
	<input type="hidden" name="variation_theme_id" value="{{ $variation_theme_details->id }}">
	<div class="row">
		<div class="col-md-12">	
			<h3>@lang('messages.magento_listing.product_variation')</h3>
		</div>
	</div>

	<div class="row">
		<div class="table-responsive">
			<table class="table table-striped display variation-table">
				<thead>
					<th>
						<label class="fancy-checkbox">
                            <input type="checkbox" class="master-checkbox">
                            <span><i></i></span>
                        </label>
					</th>

					<?php if(!empty($variation_theme_details->variation_theme_1)){ ?> 
						<th><?php echo $variation_theme_details->variation_theme_1  ?></th>
		            <?php } ?>

		            <?php if(!empty($variation_theme_details->variation_theme_2)){ ?> 
						<th><?php echo $variation_theme_details->variation_theme_2;  ?></th>
					<?php } ?>
					
					<th>@lang('messages.magento_listing.product_title')</th>
					<th>@lang('messages.common.sku')</th>
					<th>@lang('messages.magento_listing.single_selling_price')</th>
					<th>@lang('messages.common.quantity')</th>
				</thead>

				<tbody>
					@foreach($variations as $variation)
						<tr>
							<td>
								<div class="d-flex">
		                            <label class="fancy-checkbox">
		                                <input type="checkbox" class="child-checkbox">
		                                <span><i></i></span>
		                            </label>
		                        </div>
		                        <input type="hidden" name="var_product_master_id[]" value="{{ $variation->id }}">
							</td>
							
							@if(!empty($variation->variation_theme_value1) || !empty($variation_theme_details->variation_theme_1))
								<td>
									<input type="text" class="form-control" name="var_variation_theme_value1[]" value="{{ $variation->variation_theme_value1 }}">
								</td>
							@endif
							@if(!empty($variation->variation_theme_value2) || !empty($variation_theme_details->variation_theme_2))
								<td>
									<input type="text" class="form-control" name="var_variation_theme_value2[]" value="{{ $variation->variation_theme_value2 }}">
								</td>
							@endif
							<td>
								<input type="text" class="form-control" name="var_title[]" value="{{$variation->title}}">
							</td>
							<td>
								<input type="text" class="form-control" name="var_sku[]" value="{{$variation->sku}}">
							</td>
							<td>
								<input type="text" class="form-control" name="var_selling_price[]" value="{{$variation->single_selling_price}}">
							</td>
							<td>
								<input type="text" class="form-control" name="var_quantity[]" value="{{ !empty($variation->quantity) ? $variation->quantity : '' }}">
							</td>
						</tr>	
					@endforeach
				</tbody>
			</table>	
		</div>	
	</div>	
@endif
