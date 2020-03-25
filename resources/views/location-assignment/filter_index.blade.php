<button id="btnFilter" class="btn btn-filter btn-header">
    <span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span>
    <span class="icon-moon icon-Drop-Down-1"/>

</button>
<div class="search-filter-dropdown">
    <form class="form-horizontal form-flex" id="locationAssignFilterForm">
        <div class="form-fields">
            <div class="filter-container" id="custom_advance_search_fields">
                <h2 class="title">@lang('messages.modules.filter_by')</h2>
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="show_product_booked_in" id="show_product_booked_in" value="1">
                                <span  class="flex-input"><i></i>
                                    @lang('messages.location_assign.filters.show_product_booked_in')
                                </span>
                            </label>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="product_location_not_assign" value="1" id="product_location_not_assign">
                                <span  class="flex-input"><i></i>
                                   @lang('messages.location_assign.filters.products_with_location_not_assign')
                                </span>
                            </label>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="product_location_assign" value="1" id="product_location_assign">
                                <span  class="flex-input"><i></i>
                                   @lang('messages.location_assign.filters.product_with_location_assign')
                                </span>
                            </label>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="new_products" id="new_products" value="1">
                                <span  class="flex-input"><i></i>
                                    @lang('messages.location_assign.filters.new_products')
                                </span>
                            </label>
                           
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="red_days_stock_holding" id="red_days_stock_holding" value="1">
                                <span  class="flex-input"><i></i>
                                    @lang('messages.location_assign.filters.red_days_stock_holding')
                                </span>
                            </label>

                            <label class="fancy-checkbox">
                                <input type="checkbox" name="box_turn_filter" id="box_turn_filter" value="1">
                                <span class="flex-input"><i></i>
                                    @lang('messages.location_assign.filters.box_turn_from') 
                                    <input type="text" name="box_turn_from" id="box_turn_from" class="form-control col-lg-2">
                                    TO
                                     <input type="text" name="box_turn_to" id="box_turn_to" class="form-control col-lg-2">
                                </span>
                            </label>

                            <label class="fancy-checkbox">
                                <input type="checkbox" name="box_turn_undefined" id="box_turn_undefined" value="1">
                                <span  class="flex-input"><i></i>
                                    @lang('messages.location_assign.filters.box_turn_undefinded')
                                </span>
                            </label>

                            <label class="fancy-checkbox">
                                <input type="checkbox" name="assigned_aisle_filter" id="assigned_aisle_filter" value="1">
                                <span class="flex-input"><i></i>
                                    @lang('messages.location_assign.filters.products_which_are_assigned_to_aisle')
                                    <select name="assigned_aisle" class="ml-2" id="assigned_aisle">
                                        @forelse($aisleData as $ak=>$av)
                                        <option value="{{ $av }}">{{ $av }}</option>
                                        <!-- <input type="text"  name="assigned_aisle" id="assigned_aisle" class="form-control col-lg-2"> -->
                                        @empty
                                        @endforelse
                                    </select>
                                </span>
                            </label>
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