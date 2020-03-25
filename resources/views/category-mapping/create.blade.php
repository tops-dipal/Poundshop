 <div class="col-lg-4 form" id="form-data">
                    <form action="{{route('api-category-mapping.store')}}" method="post" class="" id="create-mapping-form" name="create-mapping-form">
                        <div class="row">                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="col-lg-12 form">
                                <input type="hidden" name="range_id" id="range_id">
                                <input type="hidden" name="magento_category_id" id="magento_category_id">
                                <div class="add-category-form">
                                    <h3 class="bold mb-2">@lang('messages.category_mapping.select_buying_range')</h3>
                                    <div class="form-group">                                        
                                        <select class="form-control parent_id" name="range_cat_id">
                                            <option value="0">--@lang('messages.user_management.select') @lang('messages.range_management.parent_cat')--</option>
                                            @forelse($allRanges as $parentKey=>$parentVal)
                                            @if(is_null($parentVal['parent_id']))
                                            @php
                                                if(!empty($parentVal['children']))
                                                 $data=str_replace("'", "\'", json_encode($parentVal['children']));
                                                 
                                            @endphp
                                            <option value="{{ $parentVal['id'] }}" attr-child-nodes="{{ !empty($parentVal['children']) ? $data : '' }}">{{ $parentVal['category_name'] }}</option>
                                            @endif
                                            @empty
                                            @endforelse
                                        </select>                                        
                                    </div>
                                    
                                    <div id="child_category">
                                    </div>                                    
                                </div>                                
                            </div>
                            <div class="col-lg-12 mt-3">
                                <div class="add-category-form">
                                    <h3 class="bold mb-2">@lang('messages.category_mapping.select_magento_sell_range')</h3>
                                    <div class="form-group" id="categoryLevelDiv">
                                        <select class="form-control magentoparent_id" name="magento_cat_id">
                                            <option>--@lang('messages.user_management.select') @lang('messages.range_management.parent_cat')--</option>
                                            @forelse($allMagentoCat[0]['children'] as $parentKey=>$parentVal)
                                            @if($parentVal['parent_id']==1)
                                            <option value="{{ $parentVal['id'] }}" attr-child-nodes="{{ !empty($parentVal['children']) ? json_encode($parentVal['children']) : '' }}" attr-table-id="{{ $parentVal['table_id'] }}">{{ $parentVal['name'] }}</option>
                                            @endif
                                            @empty
                                            @endforelse
                                        </select>                                        
                                    </div>
                                    <div id="magentochild_category">
                                    </div>
                                </div>
                                <div class="button-container">
                                    <button class="btn btn-blue btn-form submitBtn" title="@lang('messages.modules.button_save')">@lang('messages.category_mapping.add_mapping')</button>
                                    <!-- <a href="{{route('range.index')}}" class="btn btn-gray btn-form" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a> -->
                                </div>
                            </div>                            
                        </div>
                    </form>
                </div>