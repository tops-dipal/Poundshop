<div class="modal fade" id="assignLocationModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.storage.empty_loc') <br>@lang('messages.storage.pick_loc_assign')</h5>
                <div class="col-md-6">
                    <input type="text" class="form-control search-input" id="search_data_modal" name="" placeholder="@lang('messages.location_assign.modal_search')" />
                    <span class="refresh" id="refresh_modal"></span>
                </div>
               
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    <button class="btn btn-blue font-12 px-3 ml-2 submit_add_product_to_po" form="assignLocationForm">@lang('messages.common.save')</button>
                </div>
            </div>
            <div class="modal-body">
                
                <form id="assignLocationForm" method="post" action="{{ route('api-location-assignment.store') }}">
                 <!--  <select name="filter_aisle" id="filter_aisle">
                     <option value="">Aisle</option>
                        @forelse($aisleData as $ak=>$av)
                        <option value="{{ $av }}">{{ $av }}</option>
                      
                        @empty
                        @endforelse
                    </select>
                    <input type="button" class="btn btn-blue aisle_filter" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearchAisle();">
                    <input type="button" class="btn btn-gray cancle_aisle_fil" title="@lang('messages.modules.button_cancel')" value="@lang('messages.modules.button_cancel')"  onclick="cancelAisleFilter();"> -->
                   
                       
                    <input type="hidden" name="location_for_product_id" class="location_for_product_id">
                    <table id="emptyLocationsTable" class="display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>@lang('messages.replen.aisle')</th>
                                <th>@lang('messages.location_assign.pick_face_loc')</th>
                                <th>@lang('messages.location_assign.loc_type')</th>
                                <th>@lang('messages.table_label.length')</th>
                                <th>@lang('messages.table_label.width')</th>
                                <th>@lang('messages.table_label.height')</th>
                                <th>@lang('messages.table_label.cbm')</th>
                                <th>@lang('messages.location_assign.qty_that_will_fit_location')</th>
                                </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                           
                </form>
            </div>                
        </div>
    </div>
</div>



<div class="modal fade" id="dayStockModel" tabindex="-1" role="dialog" aria-labelledby="dayStockModelLable" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="dayStockModelLable"><span class="product_title"></span></h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    <button class="btn btn-blue font-12 px-3 ml-2 btn-stock-save" form="dayStockForm">@lang('messages.common.save')</button>
                </div>
            </div>
            <div class="modal-body">
                <form id="dayStockForm" method="post" action="{{ url('api/api-product-save-warehouse') }}">
                    <input type="hidden" name="id" class="product_id_for_stock">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="col-form-label">@lang('messages.location_assign.ros')</label>
                        <input type="text" class="form-control ros" placeholder="" name="ros" disabled="">
                    </div>
                    <div class="col-md-3">                        
                        <label class="col-form-label">@lang('messages.replen_request.stock_hold_days')</label>
                        <input type="text" class="form-control day_stock_hold" placeholder="" name="day_stock_hold" disabled="">                       
                    </div>
                    <div class="col-md-3">                        
                        <label class="col-form-label">@lang('messages.location_assign.qty_stock_holding')</label>                        
                        <input type="text" class="form-control qty_stock_hold" placeholder="" name="qty_stock_hold"  disabled="">                       
                    </div>

                    <div class="col-md-4">                        
                        <label class="col-form-label">@lang('messages.location_assign.update_day_stock_holding')</label>
                        <input type="text" class="form-control stock_hold_days" placeholder="" name="stock_hold_days" >
                    </div>
                </div>
                       <!--  <span>ROS:</span> 
                        <span>Days Stock Holding:</span> 
                        <span>Qty Stock Holding:</span>  -->
                    
                       
                    
                    <table id="dayStockTable" class="display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>@lang('messages.location_assign.qty_box')</th>
                                <th>@lang('messages.location_assign.no_box')</th>
                                <th>@lang('messages.location_assign.bulk_location')</th>
                                <th>@lang('messages.location_assign.min_day_stock_hold')</th>
                            </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                           
                </form>
            </div>                
        </div>
    </div>
</div>