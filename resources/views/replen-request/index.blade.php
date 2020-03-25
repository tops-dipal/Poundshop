@extends('layouts.app')
@section('content')
@section('title',__('messages.storage.replen_request'))

<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.storage.replen_request')</h3>
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.location_assign.search')" />
            <span class="refresh"></span>

        </div>
        <div class="right-items">
            <p class="mr-4">
                <span class="font-12-dark d-block">Data As Per:</span>
                <span class="font-12-dark bold" id="livedata_last_update"> {{$cronDataLastUpdtedDate}} </span>                
            </p>
             
            <button class="btn btn-add btn-green btn-header update_live_data" onclick="callCron()" 
            data-toggle="tooltip" data-placement="bottom" title="Update Live Data" {{($disabledStatus=='True') ? 'disabled':''}}>
                <span class="icon-moon icon-Refresh mr-0"></span>
            </button>
             <a class="btn btn-add btn-light-green btn-header" data-toggle="tooltip" data-placement="bottom"  href="{{ route('assign-aisle') }}" title="@lang('messages.replen_request.assign_aisle')">
                    <span class="icon-moon icon-Add"></span></a>
           @include('replen-request.filter-index')
        </div>
    </div>
    <div class="card-flex-container d-flex">
        <div class="d-flex-xs-block">           
            <div class="table-responsive">
            <table id="replen_request_table" class="display">
                <thead>
                    <tr>
                        <th class="m-w-240">@lang('messages.replen_request.product_info')</th>
                        <th class="m-w-80">@lang('messages.replen_request.stock_total')</th>
                        <th class="m-w-100">@lang('messages.replen_request.pick_loc')</th>                        
                        <th class="m-w-100">@lang('messages.replen_request.bulk_loc')</th>
                        <th class="m-w-80">@lang('messages.replen_request.allocated')</th>
                        <th class="m-w-60">@lang('messages.replen_request.ros')</th>
                        <th class="m-w-100">@lang('messages.replen_request.stock_hold_days')</th>
                        <th class="m-w-100">@lang('messages.replen_request.stock_hold_qty')</th>
                        <th class="m-w-100">@lang('messages.replen_request.replen_qty')</th>
                        <th class="m-w-70">@lang('messages.replen_request.priority')</th>
                        <th class="m-w-100">@lang('messages.replen_request.status')</th>
                        <th class="m-w-100">@lang('messages.replen_request.case_picked')</th>
                        <th data-class-name="action action-two">@lang('messages.table_label.action')</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            </div>
        </div>
    </div>
</div>
  @include('replen-request.edit-override')
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/replen-request/index.js?v='.CSS_JS_VERSION)}}"></script>
<!-- <script type="text/javascript" src="{{asset('js/location-assignment/empty-locations-view.js?v='.CSS_JS_VERSION)}}"></script> -->

@endsection