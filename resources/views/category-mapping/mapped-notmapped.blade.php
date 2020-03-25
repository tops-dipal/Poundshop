@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.category_mapping'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.modules.category_mapping')</h3>
        <div class="center-items">
                        <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_mapping')"/>
                        <span class="refresh"></span>
                    </div>
    </div>
    <div class="card-flex-container">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-lg-4 form" id="form-data">
                    @if($status=='mapped')
                    <form action="{{route('api-category-mapping.update',$editData->id)}}" method="post" class="" id="create-mapping-form">
                          @method('PUT')
                           
                        @else
                         <form action="{{route('api-category-mapping.store')}}" method="post" class="" id="create-mapping-form">
                        @endif
                        <div class="row">                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="col-lg-12 form">
                                <input type="hidden" name="staus" value="{{ $status }}" id="status">
                               <input type="hidden" name="id" value="{{ ($status=='mapped') ? $editData->cat_map_id : '' }}">

                               <input type="hidden" name="range_id" id="range_id" value="{{ ($status=='mapped') ? $editData->range_id : $range_id }}">

                               <input type="hidden" name="selected_parent" id="selected_parent" value="{{$selectedParent}}">

                                 <input type="hidden" name="parentIds" id="parentIds" value="{{ implode(',',$parentIds) }}">

                                 <input type="hidden" name="selected_parent_magento" id="selected_parent_magento" value="{{$selectedParentMagento}}">

                                 <input type="hidden" name="parentIdsMagento" id="parentIdsMagento" value="{{ implode(',',$parentIdsMagento) }}">

                               <input type="hidden" name="magento_category_id" id="magento_category_id" value="{{  ($status=='mapped') ? $editData->magento_cat_id : ''}}">
                                <div class="add-category-form">
                                    <h3 class="bold mb-2">@lang('messages.category_mapping.select_buying_range')</h3>
                                    <div class="form-group">                                        
                                        <select class="form-control parent_id" name="range_cat_id">
                                            <option value="">--@lang('messages.user_management.select') @lang('messages.range_management.parent_cat')--</option>
                                            @forelse($allRanges as $parentKey=>$parentVal)
                                            @if(is_null($parentVal['parent_id']))
                                            @php
                                                if(!empty($parentVal['children']))
                                                 $data=str_replace("'", "\'", json_encode($parentVal['children']));
                                                 
                                            @endphp
                                            <option value="{{ $parentVal['id'] }}" attr-child-nodes="{{ !empty($parentVal['children']) ? $data : '' }}" {{ in_array($parentVal['id'],$parentIds) ? 'selected="selected"' : "" }}>{{ $parentVal['category_name'] }}</option>
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
                                            <option value="">--@lang('messages.user_management.select') @lang('messages.range_management.parent_cat')--</option>
                                            @if(count($allMagentoCat)>0)
                                            @forelse($allMagentoCat[0]['children'] as $parentKey=>$parentVal)
                                            @if($parentVal['parent_id']==1)
                                            <option value="{{ $parentVal['id'] }}" attr-child-nodes="{{ !empty($parentVal['children']) ? json_encode($parentVal['children']) : '' }}" {{ in_array($parentVal['id'],$parentIdsMagento) ? 'selected="selected"' : "" }} attr-table-id='{{ $parentVal["table_id"] }}'>{{ $parentVal['name'] }}</option>
                                            @endif
                                            @empty
                                            @endforelse
                                            @endif
                                        </select>                                        
                                    </div>
                                    <div id="magentochild_category">
                                    </div>
                                </div>
                                <div class="button-container">
                                    <button class="btn btn-blue btn-form submitBtn" title="@lang('messages.modules.button_save')" form="create-mapping-form">@if($status=='mapped')
                                        {{'Update Mapping'}}
                                        @else
                                        @lang('messages.category_mapping.add_mapping')
                                        @endif
                                    </button>
                                    <!-- <a href="{{route('range.index')}}" class="btn btn-gray btn-form" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a> -->
                                </div>
                            </div>                            
                        </div>
                    </form>
                </div>

                <div class="col-lg-8 d-flex" style="border-left: 2px solid #e6e6e6;">

                    <table id="totes_table" class="display">
                        <thead>
                            <tr>
                                <th class="remove_sort">
                                    <div class="d-flex">
                                        <label class="fancy-checkbox">
                                            <input name="ids[]" type="checkbox" class="master">
                                            <span><i></i></span>
                                        </label>
                                        <div class="dropdown bulk-action-dropdown">
                                            <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="icon-moon icon-Drop-Down-1"/>
                                                </button>
                                                
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                                    <h4 class="title">Bulk action</h4>
                                                    <button class="btn btn-add delete-many">
                                                    <span class="icon-moon red icon-Delete"></span>
                                                    @lang('messages.category_mapping.delete_mappping')
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th>@lang('messages.inventory.buying_range')</th>
                                    <th>@lang('messages.category_mapping.selling_cat')</th>
                                    <th data-class-name="action">@lang('messages.table_label.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <!--  -->
                                
                            </tbody>
                            <tbody>
                                
                            </tbody>
                        </table>
                        
                    </div>
                </div>
                
                <!-- <div class="content-card-header">
                    <h3 class="page-title"></h3>
                    <div class="center-items">
                        <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="Please enter search" />
                        <span class="refresh"></span>
                    </div>
                    <div class="right-items">
                        
                    </div>
                </div> -->
                
                
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/category-mapping/mapped-notmapped.js?v='.CSS_JS_VERSION)}}"></script>
@endsection