<div class="modal fade" id="moveToNewPOModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.buy_by_product.move_to_new_po')</h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-4" data-dismiss="modal" aria-label="Close">
                        Cancel                    
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <form id="supplierPosForm" method="post" class="form-horizontal form-flex">
			   		<input type="hidden" name="po_products" value="" class="po_products">
			   		<input type="hidden" name="supplier_po_import_type" value="" class="supplier_po_import_type">
			   		<input type="hidden" name="perform_action" value="" class="perform_action">
			   		<input type="hidden" name="supplier_country_id" id="supplier_country_id" value="">
					<div class="row">
                        <div class="col-lg-12">
                            <div class="form-field pb-5">
                                <select name="supplier_id" class="supplier_id form-control custom-select-search" id="move_supplier_id">
                                    <option value="">--@lang('messages.buy_by_product.select_supplier')--</option>
                                    @forelse($supplierList as $supplierKey=>$supplierVal)
                                        <option value="{{ $supplierVal->id }}" data-country="{{ $supplierVal->country_id}}">{{ $supplierVal->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-field mt-5">
                                <button class="btn btn-green py-3 px-3 btn-form" id="add_to_existing_po_btn" >@lang('messages.buy_by_product.add_to_existing_po')</button>
                                
                                <button class="btn btn-blue py-3 px-3 btn-form"  id="create_po_btn" >@lang('messages.buy_by_product.create_po')</button>
                            </div>
                        </div>                        
                    </div>
				</form>
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