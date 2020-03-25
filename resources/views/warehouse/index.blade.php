@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.warehouse_master'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.modules.warehouse_master')</h3>
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_site')" />
            <span class="refresh" title="@lang('messages.modules.clear_filter')"></span>
        </div>     
        <div class="right-items">
            @can('warehouse-create')
            <a class="btn btn-add btn-light-green btn-header" href="{{ route('warehouse.create') }}" title="@lang('messages.modules.warehouse_add')">
                <span class="icon-moon icon-Add"></span>
                <!-- @lang('messages.modules.warehouse_add') -->
            </a>
            @endcan
            
            <!-- @can('warehouse-delete')
                <button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span>@lang('messages.modules.warehouse_delete')</button>
            @endcan -->
        </div>                  
    </div>  
    <div class="card-flex-container d-flex">                        
        <div class="d-flex-xs-block">
            <div class="table-responsive">
            <table id="warehouse_table" class="display">
                <thead>
                    <tr>
                        <th class="remove_sort">
                            <div class="d-flex">
                                <label class="fancy-checkbox">
                                    <input name="ids[]" type="checkbox" class="master">
                                    <span><i></i></span>
                                </label>
                                <div class="dropdown bulk-action-dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')"><span class="icon-moon icon-Drop-Down-1"/></button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                            <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                            <button class="btn btn-add delete-many" title="@lang('messages.modules.warehouse_delete')">
                                            <span class="icon-moon red icon-Delete"></span>
                                            @lang('messages.modules.warehouse_delete')
                                            </button>
                                            <!-- <button class="btn btn-add delete-many">
                                            <span class="icon-moon yellow icon-Delete"></span>
                                            Select All
                                            </button>
                                            <button class="btn btn-add delete-many">
                                            <span class="icon-moon gray icon-Delete"></span>
                                            Deselect All
                                            </button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="m-w-120">@lang('messages.table_label.warehouse_name')</th>
                        <th class="m-w-120">@lang('messages.table_label.warehouse_type')</th>
                        <th>@lang('messages.table_label.contact_person')</th>
                        <th>@lang('messages.table_label.phone_no')</th>
                        <th data-class-name="action action-two">@lang('messages.table_label.action')</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>      
            </div>          
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/warehouse/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection