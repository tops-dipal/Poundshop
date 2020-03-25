<button id="btnFilter" class="btn btn-filter btn-header">
    <span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span>
    <span class="icon-moon icon-Drop-Down-1"/>

</button>
<div class="search-filter-dropdown">
    <form class="form-horizontal form-flex" id="replenRequestFilterForm">
        <div class="form-fields">
            <div class="filter-container" id="custom_advance_search_fields">
                <h2 class="title">@lang('messages.modules.filter_by')</h2>
                <div class="container-fluid p-0">
                    <div class="row">

                        <div class="col-lg-12">
                            
                                    <div class="form-group row align-items-center">
                                       <label class="col-lg-5 col-form-label"> @lang('messages.inventory.product_tag_section')</label>
                                        <div class="col-lg-7">
                                             <select class="select2-tag" multiple="multiple" name="filter_custom_tags[]" >
                                                  @forelse($staticTags as $sk=>$sv)
                                                  <option value="{{ $sv }}">{{ $sv }}</option>
                                                  @empty
                                                  @endforelse  
                                                  @forelse($dynamicTags as $dk=>$dv)
                                                  <option value="{{ $dv->name }}">{{ $dv->name }}</option>
                                                  @empty
                                                  @endforelse    
                                            </select>
                                            <div id="select_2_dropdown"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                       <label class="col-lg-5 col-form-label"> @lang('messages.replen_request.pick_aisle')</label>
                                        <div class="col-lg-7">
                                            <select class="form-control" name="pick_aisle" id="pick_aisle">
                                            <option value="">--Select Aisle--</option>
                                            @forelse($pickaisleData as $pickaisleKey=>$pickaisleVal)
                                            <option value="{{ $pickaisleVal }}">{{ $pickaisleVal }}</option>
                                            @empty
                                             <option value="">No Any Pick Aisle</option>
                                            @endforelse
                                        </select>
                                           
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center">
                                       <label class="col-lg-5 col-form-label"> @lang('messages.replen_request.bulk_aisle')</label>
                                        <div class="col-lg-7">
                                            
                                             <select class="form-control" name="bulk_aisle" id="bulk_aisle">
                                            <option value="">--Select Aisle--</option>
                                            @forelse($bulkaisleData as $bulkaislekey=>$bulkaisleval)
                                            <option value="{{ $bulkaisleval }}">{{ $bulkaisleval }}</option>
                                            @empty
                                            <option value="">No Any Bulk Aisle</option>
                                            @endforelse
                                        </select>
                                        </div>
                                    </div>
                                    
                                      <div class="form-group row align-items-center">
                                        <label class="col-lg-5 col-form-label"> @lang('messages.common.status')</label>
                                        <div class="col-lg-7">
                                           <select class="form-control" name="status" id="status">
                                            <option value="">--Select Status--</option>
                                            
                                        </select>
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label class="col-lg-5 col-form-label"> @lang('messages.replen_request.priority')</label>
                                        <div class="col-lg-7">
                                           <select class="form-control" name="priority" id="priority">
                                            <option value="">--Select Priority--</option>
                                            @forelse($priorityArr as $priorityKey=>$priorityVal)
                                            <option value="{{ $priorityKey }}">{{ $priorityVal }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        </div>
                                    </div>

                                    <div class="form-group row align-items-center">
                                        <label class="col-lg-5 col-form-label"> @lang('messages.user_management.site')</label>
                                        <div class="col-lg-7">
                                           <select class="form-control" name="warehouse_id" id="warehouse_id">
                                            <option value="">--Select Site--</option>
                                            @forelse($siteData as $siteKey=>$siteVal)
                                            <option value="{{ $siteVal->id }}" {{ $siteVal->is_default=='1' ? 'selected="selected"' :'' }}>{{ $siteVal->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        </div>
                                    </div>
                                    
                                    <label class="fancy-checkbox">
                                        <input type="checkbox" name="product_with_day_stock_filter" id="product_with_day_stock_filter" value="1">
                                        <span class="flex-input"><i></i>
                                            @lang('messages.replen_request.filter_day_stock1')<br>
                                            <input type="text" name="days" id="days" class="form-control col-lg-2">
                                            @lang('messages.replen_request.filter_day_stock2')
                                        </span>
                                    </label>
                                    
                           

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="button" class="btn btn-gray cancle_fil" title="@lang('messages.modules.button_cancel')" value="@lang('messages.modules.button_cancel')">
            <input type="button" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch();">
        </div>
    </form>
</div>