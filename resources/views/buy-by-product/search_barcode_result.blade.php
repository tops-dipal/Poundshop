@extends('layouts.app')
@section('content')
@section('title',__('messages.common.buy_by_product'))

<input type="hidden" name="product_id" id="product_id" value="{{$productData['id']}}">
<input type="hidden" name="product_po_id" id="product_po_id" value="{{ $productData['po_id'] }}">
<input type="hidden" name="barcode" id="barcode" value="{{$barcode}}">
	<div class="content-card custom-scroll">
		<div class="content-card-header">
			<h3 class="page-title">@lang('messages.common.buy_by_product')</h3>	
		</div>	
		<div class="card-flex-container">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-6">
						<div class="card mb-3">
							<div class="card-header">
								<h4 class="py-2">Product Information</h4>
							</div>
							<div class="card-body">
								@if(!empty($productData))
								<div class="load_data">
									<div class="form">
										<div class="form-field mb-3">
									        <!-- <label class="custom-lbl">@lang('messages.inventory.title'):</label> -->
									        <span class="total_tax bold">{{ $productData['title'] }}</span>
									    </div>
									    <div class="row mb-3">
									    	<div class="col-lg-4 col-4">
									    		<div class="form-field">
											        <label class="custom-lbl">
											        	<a href="{{ $productData['main_image_internal_thumb'] }}" data-rel="lightcase">
															<img src="{{url('/img/img-loading.gif') }}" data-original="{{$productData['main_image_internal_thumb']}}" class="img-fluid" />
														</a>
													</label>
											    </div>
									    	</div>
									    	<div class="col-lg-8 col-8">
									    		<div class="form-field mb-3">
											        <label class="custom-lbl">@lang('messages.common.barcode'):</label>
											        <span>{{ $barcode }}</span>
											    </div>
											    <div class="form-field">
											        <label class="custom-lbl">@lang('messages.buy_by_product.sku'):</label>
											        <span class="total_vat">{{ $productData['sku'] }}</span>
											    </div>
									    	</div>
									    </div>									    
									    <div class="form-field">
									        <a class="btn btn-blue" href="{{ route('buy-by-product.index') }}"> @lang('messages.buy_by_product.next_product') </a>
									        
									    </div>
								    </div>               
								</div>
								@else
								<div class="load_data">
									<div class="form">
									   
									    <div class="form-field">
									        <label class="custom-lbl">@lang('messages.common.barcode'):</label>
									        <span>{{ $barcode }}</span>
									    </div>
									    <div class="form-field my-4">
									        <span class="color-red">@lang('messages.buy_by_product.product_not_found')</span>
									    </div>
									    
									     <div class="form-field">
									        <a class="btn btn-blue" href="{{ route('buy-by-product.index') }}"> @lang('messages.buy_by_product.next_product') </a>
									        
									    </div>
								    </div>               
								</div>
								@endif
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="card">
							<div class="card-header">
								<h4 class="py-2">PO Detail</h4>
							</div>
							<div class="card-body">
								<div class="form">
								   <form id="supplierPosForm" method="post" class="form-horizontal form-flex">
								   		<input type="hidden" name="productId" value="{{ $productData['id'] }}" class="productId">
								   		<input type="hidden" name="po_import_type" value="" class="po_import_type">
								   		<input type="hidden" name="perform_action" value="" class="perform_action">
								   		<input type="hidden" name="country_id" id="country_id" value="">
										<div class="form-field mb-3">
											<label class="custom-lbl mb-2">Select Supplier</label>
						    				<select name="supplier_id" class="supplier_id form-control custom-select-search" id="supplier_id">
						                        <option value="">--@lang('messages.buy_by_product.select_supplier')--</option>
												@forelse($supplierList as $supplierKey=>$supplierVal)
													<option value="{{ $supplierVal->id }}" data-country="{{ $supplierVal->country_id}}" {{ $selectedSupplier==$supplierVal->id ? 'selected' : '' }}>{{ $supplierVal->name }}</option>
												@empty
												@endforelse
											</select>
										</div>
						    			<div class="form-field">
						                    <button class="btn btn-primary actionBtn" id="add_to_existing_po_btn" >@lang('messages.buy_by_product.add_to_existing_po')</button>
						                    
						                    <button class="btn btn-blue actionBtn"  id="create_po_btn" >@lang('messages.buy_by_product.create_po')</button>
						                </div>
									</form>
							    </div>  
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="addToExistingPoModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			    <div class="custom-modal modal-dialog modal-lg" role="document">
			        <div class="modal-content">
			            <div class="modal-header align-items-center">
			                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.buy_by_product.add_product_to_po')</h5>
			                <div>
			                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
			                    <button type="button" class="btn btn-blue font-12 px-3 ml-2 submit_add_product_to_po" onclick="addProductToPo()">@lang('messages.common.save')</button>
			                </div>
			            </div>
			            <div class="modal-body">
			                <form id="addProductToPoForm" method="post" action="{{ route('api-add-product-to-existing-po') }}">
			                    <input type="hidden" name="product_id" value="" class="product_id" value="{{ $productData['id'] }}">
			                    <table id="existingPosTable" class="table table-striped custom-table">
			                        <thead>
			                            <tr>
			                               <th></th>
			                                <th>@lang('messages.bookings.booking_table.Pos')</th>
			                                <th>@lang('messages.buy_by_product.created_date_time')</th>
			                                <th>@lang('messages.buy_by_product.exp_deli_date')</th>
			                                <th>@lang('messages.buy_by_product.total_num_items')</th>
			                                <th>@lang('messages.buy_by_product.total_cost')</th>
                                		</tr>
			                            </thead>
			                        <tbody></tbody>
			                    </table>
			                           
			                </form>
			            </div>                
			        </div>
			    </div>
			</div>
			
		
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/buy-by-product/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection
