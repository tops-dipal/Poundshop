@extends('layouts.app')
@section('title','Purchase Order List')
@section('content')
<div class="content-card custom-scroll">
        <div class="content-card-header">
            <h3 class="page-title">@lang('messages.purchase_order.po_list_title')</h3>		
            <div class="center-items">
                <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.purchase_order.filters.search')" />
                <span class="refresh"></span>
            </div>
            <div class="right-items">
                @include('purchase-orders._search-filter')
                <a title="@lang('messages.purchase_order.add')" class="btn btn-add btn-light-green btn-header" href="{{route('purchase-orders.create')}}">
                    <span class="icon-moon icon-Add"></span>
                </a>
            </div>
        </div>	
        <div class="card-flex-container d-flex">					    
            <div class="d-flex-xs-block">
                    <table id="po_table" class="display">
                            <thead>
                                <tr>
                                    <th>
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
                                                    <h4 class="title">@lang('messages.common.bulk_action')</h4>
                                                    <button class="btn btn-add delete-many" title="@lang('messages.purchase_order.multi_delete')">
                                                    <span class="icon-moon red icon-Delete"></span>
                                                    @lang('messages.purchase_order.multi_delete')
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
                                    </th>
                                    <th>@lang('messages.purchase_order.po_number')</th>
                                    <th>@lang('messages.purchase_order.supplier_ord_number')</th>
                                    <th>@lang('messages.purchase_order.supplier_name')</th>
                                    <th>@lang('messages.purchase_order.total_cost')</th>
                                    <th>@lang('messages.purchase_order.status')</th>
                                    <th>@lang('messages.purchase_order.created_at')</th>
                                    <th data-class-name="action">@lang('messages.table_label.action')</th>				
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>				
                </div>
        </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/po/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection