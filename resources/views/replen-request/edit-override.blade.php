<div class="modal fade" id="overrideModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">Add Mannual Job</h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    <button class="btn btn-blue font-12 px-3 ml-2 submit" form="editOverideForm">@lang('messages.common.save')</button>
                </div>
            </div>
            <div class="modal-body">
                <form id="editOverideForm" method="post">
                    <div class="form-row">
                        <div class="col-lg-9">
                            <input type="text" name="search_edit_override" placeholder="Search Barcode, SKU, Product Title" class="form-control" id="search_edit_override">
                        </div>    
                        <div class="col-lg-3">
                            <input type="button" class="btn btn-blue aisle_filter py-2 col-lg-12" title="@lang('messages.common.search')" value="@lang('messages.common.search')" onclick="searchProductFromModel()" >        
                        </div>
                    </div>                  
                       
                    <input type="hidden" name="replen_id" class="replen_id">
                    <input type="hidden" name="product_id" class="product_id">
                    <table id="productInfoTable" class="display dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="w-150">Product Information</th>
                                <th class="w-100">Replen In Progress</th>
                                <th class="w-80">Replen Priority</th>
                                <th class="w-100">Replen Quantity</th>
                                <th class="w-80">Priority</th>
                                </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                           
                </form>
            </div>                
        </div>
    </div>
</div>