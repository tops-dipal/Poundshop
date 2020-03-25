@extends('layouts.app')
@section('content')
@section('title',__('messages.storage.location_assign'))

<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.storage.location_assign')</h3>
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.location_assign.search')" />
            <span class="refresh"></span>
        </div>
        <div class="right-items">
            @include('location-assignment.filter_index')
        </div>
    </div>
    <div class="card-flex-container d-flex">
        <div class="d-flex-xs-block">
           
            <div class="table-responsive">
            <table id="location_assign_table" class="display">
                <thead>
                    <tr>
                        <th class="m-w-280 remove_sort">@lang('messages.replen_request.product_info')</th>
                        <th class="m-w-80">@lang('messages.location_assign.ros')</th>
                        <th class="m-w-100">@lang('messages.range_management.day_stock')</th>
                        <th class="m-w-100">@lang('messages.location_assign.pick_stock_require')</th>
                        <th class="m-w-100">@lang('messages.location_assign.total_pick_location_qty')</th>
                        <th class="m-w-100">@lang('messages.location_assign.total_bulk_location_qty')</th>
                        <th class="m-w-100">@lang('messages.location_assign.box_turn')</th>                        
                        <th data-class-name="action action-two">@lang('messages.table_label.action')</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_assigned_location" class="modal-assigned-location">
    <div class="product-detail-header">
        <h4 class="product-name"></h4>
        <div>
            <a class="btn btn-add btn-green btn-header" id="assign_aisle_btn" title="Assign Location">
                <span class="icon-moon icon-Add m-0"></span>
            </a>
            <a id="close_assign_location" href="javascript:void(0)" class="btn btn-gray font-12 px-4">@lang('messages.common.cancel') </a>
            <button type="submit" form="assignedLocationForm" class="btn btn-blue font-12 px-4 saveBulkQtyFitInLocation" id="head-submit"> @lang('messages.common.save') </button>
        </div>
    </div>
    <div class="product-tags">
        <h4 class="font-14-dark bold mb-2" id="product_tags_title">Product Tags:</h4>
        <div class="tags">
        </div>
        <!-- <span class="badge badge-primary p-2">Heavy Essential</span>
        <span class="badge badge-primary p-2">Flammable</span>
        <span class="badge badge-primary p-2">Liquid</span> -->
    </div>
    <div class="product-location-detail">
        <form id="assignedLocationForm" method="post" name="assignedLocationForm">
            <input type="hidden" name="product_id" id="product_id">
       
        <table class="assigned_location_table display dataTable no-footer" id="assigned_location_table">
            <thead>
                <th>@lang('messages.location_assign.locations_allocated')</th>
                <th>@lang('messages.location_assign.pick_qty_per_location')</th>
                <th>@lang('messages.location_assign.qty_that_will_fit_location')</th>
                <th>@lang('messages.table_label.action')</th>

            </thead>
        </table>

        </form>
    </div>
</div>
@include('location-assignment.assign-location-model')
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/location-assignment/index.js?v='.CSS_JS_VERSION)}}"></script>
<!-- <script type="text/javascript" src="{{asset('js/location-assignment/empty-locations-view.js?v='.CSS_JS_VERSION)}}"></script> -->

@endsection