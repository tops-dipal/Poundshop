@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>	
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="Search by Sup. Name, Goods In Ref. No, PO Number" />
        </div>	
        <div class="right-items">
            <button id="btnFilter" class="btn btn-filter btn-header">
                <span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span>
                <span class="icon-moon icon-Drop-Down-1"/>

            </button>
           
            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex" id="custom_advance_search" method="post">
                    <div class="form-fields">
                        <div class="filter-container" id="custom_advance_search_fields">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>
                             <div class="container-fluid p-0">
                                <div class="row">
                                    
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.excess_qty_report.filter_with_date')</label>
                                            <div class="col-lg-7" style="display: flex">
                                                <input type="text"  class="form-control mr-2" id="startdate" readonly="" name="filter_with_date_from" />
                                                <input type="text" class="form-control" id="enddate" name="filter_with_date_to" readonly=""/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.excess_qty_report.filter_confirm_with_supplier')</label>
                                            <div class="col-lg-7">
                                                <select class="form-control" name="filter_confirm_with_supplier">
                                                    <option value="">All</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
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
                        <input type="submit" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch(event);">
                    </div>
                </form>
            </div>
        </div>					
    </div>	
    <div class="card-flex-container">					    
        <div class="container-fluid h-100 d-flex flex-column">
            <div class="container-info">
                <div class="form">
                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.excess_qty_report.total_extra_products'):</label>
                        <span class="total_extra_products">-</span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.excess_qty_report.total_extra_quantity'):</label>
                        <span class="total_extra_quantity">-</span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.excess_qty_report.total_value'):</label>
                        <span class="total_value">-</span>
                    </div>
                </div>
            </div>
            <table id="listing_table" class="display">
                <thead>
                    <tr>
                        <th style="display:none"></th>
                        <th>@lang('messages.excess_qty_report.material_receipt_start_date')</th>
                        <th>@lang('messages.excess_qty_report.completion_date')</th>
                        <th>@lang('messages.excess_qty_report.goods_in_ref_no')</th>
                        <th>@lang('messages.excess_qty_report.supplier_name')</th>
                        <th>@lang('messages.excess_qty_report.sku')</th>
                        <th>@lang('messages.excess_qty_report.Quantity')</th>
                        <th class="dt-head-align-right">
                            <span class="dt-head-text"> 
                                @lang('messages.excess_qty_report.value')
                            </span>
                        </th>
                        <th>@lang('messages.excess_qty_report.confirmed_with_supplier')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>	
        </div>    
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/reports/excess_qty_received_report.js')}}"></script>
@endsection