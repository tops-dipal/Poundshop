<div class="col-lg-6 form" style="border-right: 2px solid #e6e6e6;" id="create-form">
    <form action="{{route('api-range.store')}}" method="post" class="form-horizontal form-flex" id="create-range-form">
        <div class="add-category-form">
            <h3 class="title">@lang('messages.range_management.range_add')</h3>
            <input type="hidden" name="get_child" id="get_child" value="{{ url('get-child-category') }}">
            <input type="hidden" name="csrf_token" value="{{ csrf_token() }}" class="token">
            <input type="hidden" name="selected_parent" id="selected_parent" >
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.range_management.parent_cat')</label>
                <div class="col-lg-8">
                    <select class="form-control parent_id" name="parent_id">
                        <option value=" ">--@lang('messages.user_management.select') @lang('messages.range_management.parent_cat')--</option>
                        @forelse($parent as $parentKey=>$parentVal)
                        <option value="{{ $parentVal['id'] }}" attr-child-nodes="{{ !empty($parentVal['children']) ? json_encode($parentVal['children']) : '' }}">{{ $parentVal['category_name'] }}</option>
                        @empty
                        @endforelse
                    </select>
                    
                </div>
            </div>
            
            <div id="child_category">
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.range_management.cat_name')<span class="asterisk">*</span></label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="category_name_0" placeholder="Category Name" name="category_name[]">
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <label class="fancy-radio">
                                <input type="radio" name="seasonal_status[0]" value="2" class="seasonal_status" checked="">
                                <span><i></i>@lang('messages.range_management.non_seasonal')</span>
                            </label>                                            
                        </div>
                        <div class="col-lg-6">
                            <label class="fancy-radio">
                                <input type="radio" name="seasonal_status[0]" value="1" class="seasonal_status">
                                <span><i></i>@lang('messages.range_management.seasonal')</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-2 seasonal_show0 hidden">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.retro_from')</label>
                            <div class="col-lg-8">
                                <div class="d-flex input-select-group">
                                    <input type="number" name="seasonal_range_fromdate[]" value="" class="form-control seasonal_show" min="1" max="31" id="seasonal_range_fromdate_0">
                                    <select name="seasonal_range_frommonth[]" class="form-control seasonal_show">
                                        @php
                                        $monthArr=array("1"=>'Jan',"2"=>'Feb',"3"=>'March',"4"=>'April',"5"=>'May',"6"=>'June',"7"=>'July',"8"=>'August',"9"=>'Sep',"10"=>'Oct',"11"=>'Nov',"12"=>'Dec');
                                        @endphp
                                        @foreach($monthArr as $monthKey=>$monthVal)
                                        <option value="{{ $monthKey }}">{{ $monthVal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="seasonal_show0 hidden">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.retro_to')</label>
                            <div class="col-lg-8">
                                <div class="d-flex input-select-group">
                                    <input type="number" name="seasonal_range_todate[]" id="seasonal_range_todate_0" value="" class="form-control seasonal_show" min="1" max="31">
                                    <select name="seasonal_range_tomonth[]" class="form-control seasonal_show">
                                        @php
                                        $monthArr=array("1"=>'Jan',"2"=>'Feb',"3"=>'March',"4"=>'April',"5"=>'May',"6"=>'June',"7"=>'July',"8"=>'August',"9"=>'Sep',"10"=>'Oct',"11"=>'Nov',"12"=>'Dec');
                                        @endphp
                                        @foreach($monthArr as $monthKey=>$monthVal)
                                        <option value="{{ $monthKey }}">{{ $monthVal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.range_management.day_stock')<span class="asterisk">*</span></label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="stock_hold_days_0" placeholder="Stock Holding Days" name="stock_hold_days[]">
                </div>
            </div>                           
            <div class="addMoreCategory"></div>
            <div class="row">                                
                <div class="col-lg-12 text-right">
                    <input type="hidden" name="add-more-cat-url" value="{{ route('add-more-cat') }}" id="addMoreCatURL">
                    <button type="button" class="btn btn-success addMore">+ @lang('messages.common.add_more')</button>
                </div>                                
            </div>                           
            
            <div class="button-container">
                <button class="btn btn-blue btn-form" title="@lang('messages.modules.button_save')">@lang('messages.range_management.range_add')</button>
                <a href="{{route('range.index')}}" class="btn btn-gray btn-form" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            </div>
        </div>
    </form>
</div>