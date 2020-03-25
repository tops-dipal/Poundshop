@extends('layouts.app')
@section('content')
@section('title',__("messages.storage.put_away_dashboard"))
<div class="content-card custom-scroll">
    <input type="hidden" name="active_tab" id="active_tab" value="{{ $active_tab }}">
    <div class="content-card-header">
        <h3 class="page-title putaway-fix-title">@lang('messages.storage.put_away_dashboard')</h3>
        <div class="center-items">
            <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'put-away-dashboard' ? 'active' : ''}}" id="put-away-dashboard-tab" href="{{ route('put-away-dashboard') }}">
                        @lang('messages.storage.put_away_dashboard')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'put-away' ? 'active' : ''}}" id="put-away-tab" href="{{ route('put-away') }}">
                        @lang('messages.storage.put_away')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  {{ $active_tab == 'put-away-job-list' ? 'active' : ''}}" id="put-away-job-list-tab"  href="{{ route('put-away-job-list') }}">
                        @lang('messages.storage.put_away_job_list')
                    </a>
                </li>
            </ul>
        </div>
        @if($active_tab == 'put-away-dashboard')
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
                                            <label class="col-lg-5 col-form-label">@lang('messages.storage.filter_by_warehouse')</label>
                                            <div class="col-lg-7">
                                                <select name="warehouse_id" id="warehouse_id"  class="form-control">
                                                    <option value="">@lang('messages.storage.select_warehouse')</option>
                                                    @if(!empty($wareHouses))
                                                    @foreach($wareHouses as $key=>$val)
                                                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <input type="button" class="btn btn-gray cancle_fil" title="@lang('messages.common.reset_filter')" value="@lang('messages.common.reset_filter')">
                        <input type="submit" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch(event);">
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
    <div class="card-flex-container d-flex">
        <div class="d-flex-xs-block">
            <div class="table-responsive">
                <table id="put_away_dashboard_table" class="display">
                    <thead>
                        <tr>
                            <th class="m-w-100">@lang('messages.bookings.booking_table.bookin_ref_num')</th>
                            <th class="dt-head-align-right m-w-100">    
                                <span class="dt-head-text">
                                    @lang('messages.storage.receipt_per')
                                </span>
                            </th>
                            <th class="m-w-100">@lang('messages.storage.pick_put_away')</th>
                            <th class="m-w-100">@lang('messages.storage.bulk_put_away')</th>
                            <th class="m-w-100">@lang('messages.storage.pallets_pick_skus')</th>
                            <th class="m-w-100">@lang('messages.storage.pallets_bulk_skus')</th>
                            <th class="m-w-100">@lang('messages.storage.sku_with_no_allocate')</th>
                            <th class="m-w-100">@lang('messages.storage.drop_ship_prod')</th>
                            <th class="m-w-100">@lang('messages.storage.seasonal_products')</th>
                            <th class="m-w-100">@lang('messages.storage.promotion_products')</th>
                            <th class="m-w-100">@lang('messages.storage.short_dated')</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/put-away/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection