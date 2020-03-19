@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<script type="text/javascript">
var logical_tags = '<?php echo json_encode(product_logic_base_tags()) ?>';
</script>
<div class="content-card custom-scroll">

        <div class="content-card-header">
            
            <h3 class="page-title">{{$page_title}}</h3>		
            
             <div class="center-items">

                <div class="search-by-category">
                    <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="Search by Our SKU, Title, Barcode" />
                    <select class="form-control search-category" name="search_type" form="custom_advance_search" onchange="draw_table(this)">
                        <option value="all">@lang('messages.common.all')</option>
                        <option value="sku">@lang('messages.common.sku')</option>
                        <option value="title">@lang('messages.inventory.title')</option>
                        <option value="product_barcode">@lang('messages.common.barcode')</option>
                    </select>
                    <span class="refresh" title="@lang('messages.modules.clear_filter')"></span>
                </div>
            </div>  

            <div class="right-items">
                <button id="btnFilter" class="btn btn-filter btn-header">
                    <span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span>
                    <span class="icon-moon icon-Drop-Down-1"/>

                </button>
                <div class="search-filter-dropdown">
                    <form class="form-horizontal form-flex" id="custom_advance_search">
                        <div class="form-fields">
                            <div class="filter-container" id="custom_advance_search_fields">
                                <h2 class="title">@lang('messages.modules.filter_by')</h2>
                                <div class="container-fluid p-0">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="fancy-checkbox">
                                                <input type="checkbox" name="filter_missing_images" value="1">
                                                <span><i></i>
                                                    @lang('messages.common.filter_missing_images')
                                                </span>
                                            </label>
                                            <label class="fancy-checkbox">
                                                <input type="checkbox" name="filter_missing_product_info" value="1">
                                                <span><i></i>
                                                    @lang('messages.common.filter_missing_product_info')
                                                </span>
                                            </label>
                                            <label class="fancy-checkbox">
                                                <input type="checkbox" name="filter_show_new_products_only" value="1">
                                                <span><i></i>
                                                    @lang('messages.common.filter_show_new_products_only')
                                                </span>
                                            </label>
                                            @foreach(product_logic_base_tags() as $db_fields => $tag)
                                            <label class="fancy-checkbox">
                                                <input type="checkbox" name="filter_{{$db_fields}}" value="1">
                                                <span><i></i>
                                                    Show Products With {{$tag}} Tags
                                                </span>
                                            </label>
                                            @endforeach
                                            <label class="fancy-checkbox">
                                                <input type="checkbox" name="filter_show_seasonal_products_only" value="1">
                                                <span><i></i>
                                                    @lang('messages.common.filter_show_seasonal_products_only')
                                                </span>
                                            </label>
                                            
                                            <div class="col-lg-12 hidden" id="seasonal_range">
                                                <div class="form-group row  align-items-center">
                                                    <div class="col-md-6">
                                                        <label class="col-lg-5 col-form-label">@lang('messages.inventory.filter_seasonal_from_date')</label>
                                                        <div class="col-lg-7">
                                                            <input type="text" class="form-control datepicker_month_and_date clear_except" name="filter_seasonal_from_date"  value="{{ date('d-M') }}" readonly="readonly">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="col-lg-5 col-form-label">@lang('messages.inventory.filter_seasonal_to_date')</label>
                                                        <div class="col-lg-7">
                                                            <input type="text" class="form-control datepicker_month_and_date clear_except" name="filter_seasonal_to_date"  value="{{ date('d-M', strtotime('+1 month')) }}" readonly="readonly">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center">
                                                <label class="col-lg-5 col-form-label">@lang('messages.inventory.select_tags')</label>
                                                <div class="col-lg-7">
                                                    <select class="select2-tag" multiple="multiple" name="filter_custom_tags[]" >
                                                        
                                                    </select>
                                                    <div id="select_2_dropdown"></div>
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
                @can('product-create')
                <a class="btn btn-add btn-green btn-header" href="{{ url('product/form') }}" title= "@lang('messages.common.add') @lang('messages.common.product') ">
                    <span class="icon-moon icon-Add m-0"></span>
                </a>
                @endcan
            </div>
        </div>
        <div class="card-flex-container d-flex">
            <div class="d-flex-xs-block">
                <div class="table-responsive">
                <table id="listing_table" class="display">
                    <thead>
                        <tr>
                            <th>
                                <div class="d-flex">
                                    <label class="fancy-checkbox">
                                        <input name="agree" type="checkbox" class="master-checkbox">
                                        <span><i></i></span>
                                    </label>
                                    <div class="dropdown bulk-action-dropdown">
                                        <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                                        <span class="icon-moon icon-Drop-Down-1"/>
                                            </button>
                                            
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                                @can('product-delete')
                                                <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                                <button class="btn btn-add delete-many" onclick="delete_record(this)">
                                                <span class="icon-moon red icon-Delete"></span>
                                                @lang('messages.common.delete')
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </th>
                            <th><div class="m-w-80">@lang('messages.inventory.image')</div></th>
                            <th><div class="m-w-200">@lang('messages.inventory.title')</div></th>
                            <th><div class="m-w-100">@lang('messages.inventory.sku')</div></th>
                            <th><div class="m-w-80">@lang('messages.inventory.instock')</div></th>
                            <th><div class="m-w-80">@lang('messages.inventory.allocated')</div></th>
                            <th><div class="m-w-80">@lang('messages.inventory.free_stock')</div></th>
                            <th><div class="m-w-80">@lang('messages.inventory.product_id')</div></th>
                            <th><div class="m-w-80">@lang('messages.inventory.cost_price')</div></th>
                            <th><div class="m-w-80">@lang('messages.inventory.selling_price')</div></th>
                            <th><div class="m-w-100">@lang('messages.inventory.tag')</div></th>
                            <th><div class="m-w-120">@lang('messages.inventory.last_stock_receipt')</div></th>
                            <th><div class="m-w-80">@lang('messages.inventory.magento_status')</div></th>
                            <th data-class-name="action"><div class="m-w-100">@lang('messages.table_label.action')</div></th>
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
<script type="text/javascript" src="{{asset('js/product/index.js')}}"></script>
@endsection
@section('css')
<style type="text/css">
.thumb-img{
height:25px;
width:25px;
}
</style>
@endsection