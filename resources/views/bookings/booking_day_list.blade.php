@extends('layouts.app')
@section('content')
@section('title',__('messages.bookings.book_in'))
<?php
$link = $_SERVER['REQUEST_URI'];
$link_array = explode('/',$link);
$date = end($link_array);
?>
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.bookings.book_in')</h3>	
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.bookings.search')" />
            <span class="refresh"></span>
        </div>
        <div class="right-items">
            <p class="mr-2">
                <span class="font-12-dark d-block">Date:</span>
                <span class="font-12-dark bold date_wise_data_class">{{ !empty($date)?date('d-M-Y',strtotime($date)):'' }}</span>
            </p>
            <a href="{{route('booking-in.index')}}" class="btn btn-header btn-blue">@lang('messages.goods_in_master.back_to_week')</a>
            <button id="btnFilter" class="btn btn-filter btn-header"><span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/></button>
            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex" id="booking_day_advance_search">
                    <div class="form-fields">
                        <!-- <div class="sort-container">
                            <h2 class="title">@lang('messages.modules.sort_by')</h2>
                            <label class="fancy-checkbox">
                                <input name="" type="checkbox" class="master">
                                <span><i></i>Supplier who are over Credit limit</span>
                            </label>
                            <label class="fancy-checkbox">
                                <input name="" type="checkbox" class="master">
                                <span><i></i>Suppliers with Retro discount</span>
                            </label>
                        </div> -->
                        <div class="filter-container">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.bookings.booking_table.bookin_date')</label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control datepicker" id="booking_date" name="booking_date" value="" readonly="readonly">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="fancy-checkbox col-lg-5 col-form-label">
                                                <input name="booking_status[]" type="checkbox" class="form-control" value="1" id="booking_status_comp">
                                                <span><i></i>@lang('messages.goods_in_master.status_completed')</span>
                                            </label>
                                            <label class="fancy-checkbox col-lg-5 col-form-label">
                                                <input name="booking_status[]" type="checkbox" class="form-control" value="2" id="booking_status_not_comp">
                                                <span><i></i>@lang('messages.goods_in_master.status_not_completed')</span>
                                            </label>
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
            
            <a class="btn btn-add btn-light-green btn-header" href="{{ route('booking-in.create') }}" title="@lang('messages.bookings.create')">
                <span class="icon-moon icon-Add"></span>
            </a>  

            
            <!--@can('cartons-delete')
            <button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span>Delete Carton</button>
            @endcan-->
        </div>					
    </div>	
    <div class="card-flex-container d-flex">					               
        <div class="d-flex-xs-block">                
           <!--  <div class="text-right">
                
            </div> -->
            <input type="hidden" id="prev_date" value="{{ booking_prev_day_date($date) }}">
            <input type="hidden" id="view_date" value="{{ $date }}">
            <input type="hidden" id="next_date" value="{{ booking_next_day_date($date) }}">
            <input type="hidden" id="url_date" value="{{ $date }}">               
            <div class="table-responsive">
            <table id="booking_day_table" class="display">
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
                                        <button class="btn btn-add delete-many">
                                        <span class="icon-moon red icon-Delete"></span>
                                        @lang('messages.bookings.delete_booking')</button>
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
                        <th class="m-w-100">@lang('messages.bookings.booking_table.bookin_date')</th>
                        <th class="m-w-60">@lang('messages.bookings.booking_table.slot')</th>
                        <th class="m-w-120">@lang('messages.bookings.booking_table.bookin_ref_num')</th>
                        <th class="m-w-120">@lang('messages.bookings.booking_table.supplier_name')</th>
                        <th class="m-w-100">@lang('messages.bookings.booking_table.Pos')</th>
                        <th class="m-w-100">@lang('messages.bookings.booking_table.no_of_pallets')</th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.no_of_sku')</th>
                        <th class="m-w-70">@lang('messages.bookings.booking_table.total_varients')</th>
                        <th class="m-w-70">@lang('messages.bookings.booking_table.essential_product')</th>
                        <th class="m-w-70">@lang('messages.bookings.booking_table.seasonal_products')</th>
                        <th class="m-w-100">@lang('messages.bookings.booking_table.total_qty')</th>
                        <th class="dt-head-align-right m-w-100">
                            <span class="dt-head-text">
                                @lang('messages.bookings.booking_table.po_val')
                            </span>
                        </th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.per_complete')</th>
                        <th class="m-w-120">@lang('messages.bookings.booking_table.status')</th>
                        <th class="m-w-100 action action-three" data-class-name="action">@lang('messages.table_label.action')</th>
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
<script type="text/javascript" src="{{asset('js/bookings/booking_day_list.js?v='.CSS_JS_VERSION)}}"></script>
@endsection