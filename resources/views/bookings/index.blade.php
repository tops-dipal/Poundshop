@extends('layouts.app')
@section('content')
@section('title',__('messages.bookings.book_in'))

<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.bookings.book_in')</h3>
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.bookings.search')" />
            <span class="refresh"></span>
        </div>
        <div class="right-items">
            <p class="mr-4">
                <span class="font-12-dark d-block">Start Date:</span>
                <span class="font-12-dark bold" id="start_date">{{ date('d-M-Y',strtotime(booking_prev_week_date($date))) }}</span>
            </p>
            <p class="mr-4">
                <span class="font-12-dark d-block">End Date:</span>
                <span class="font-12-dark bold" id="end_date">{{ date('d-M-Y',strtotime(booking_next_week_date($date))) }}</span>
            </p>
            @can('cartons-create')
            <a class="btn btn-add btn-light-green btn-header" href="{{ route('booking-in.create') }}" title="@lang('messages.bookings.create')">
                <span class="icon-moon icon-Add"></span>
            </a>
            @endcan
            <button id="btnFilter" class="btn btn-filter btn-header"><span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/></button>
            @include('bookings._index-filter')
            <!--                @can('cartons-delete')
                            <button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span>Delete Carton</button>
                            @endcan-->
        </div>
    </div>
    <div class="card-flex-container d-flex">
        <div class="d-flex-xs-block">
            <input type="hidden" id="prev_date" value="{{ booking_prev_week_date($date) }}">
            <input type="hidden" id="view_date" value="{{ $date }}">
            <input type="hidden" id="next_date" value="{{ booking_next_week_date($date) }}">
            <input type="hidden" id="url_date" value="">
            <div class="table-responsive">
            <table id="booking_table" class="display">
                <thead>
                    <tr>
                        <th class="m-w-100">@lang('messages.bookings.booking_table.bookin_date')</th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.no_of_booking')</th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.no_of_pallets')</th>
                        <th class="m-w-100">@lang('messages.bookings.booking_table.dropship_skus')</th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.no_of_sku')</th>
                        <th class="m-w-70">@lang('messages.bookings.booking_table.total_varients')</th>
                        <th class="m-w-70">@lang('messages.bookings.booking_table.new_product')</th>
                        <th class="m-w-70">@lang('messages.bookings.booking_table.essential_product')</th>
                        <th class="m-w-70">@lang('messages.bookings.booking_table.seasonal_products')</th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.promotion_products')</th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.short_date')</th>
                        <th class="m-w-100">@lang('messages.bookings.booking_table.total_qty')</th>
                        <th class="m-w-80 dt-head-align-right">
                            <span class="dt-head-text">
                                @lang('messages.bookings.booking_table.po_val')
                            </span>
                        </th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.qty_shortage')</th>
                        <th class="m-w-80">@lang('messages.bookings.booking_table.qty_overs')</th>
                        <th class="m-w-120 dt-head-align-right">
                            <span class="dt-head-text">
                                @lang('messages.bookings.booking_table.received_val')
                            </span>
                        </th>
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
<script type="text/javascript" src="{{asset('js/bookings/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection