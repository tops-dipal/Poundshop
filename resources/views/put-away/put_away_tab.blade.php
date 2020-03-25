@extends('layouts.app')
@section('content')
@section('title',__("messages.storage.put_away"))
<div class="content-card custom-scroll">
    <input type="hidden" name="active_tab" id="active_tab" value="{{ $active_tab }}">
    <div class="content-card-header">
        <h3 class="page-title putaway-fix-title">@lang('messages.storage.put_away')</h3>
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
        <div class="right-items">
            &nbsp;
        </div>
    </div>
    <div class="card-flex-container d-flex py-0">
        <div class="d-flex-xs-block flex-column">
            <div class="inner-content-header pb-2">
                <div class="bg-gray">
                    <div class="row m-0 align-items-center">
                        <div class="col-lg-3 col-6 pl-0">
                            <input type="hidden" id="putaway-product-url" value="{{route('put-aways.products')}}" />
                            <input type="text" class="form-control h-28" placeholder="Scan Pallet Location Barcode" name="" id="scan-pallet-barcode-textbox"  autocomplete="off">
                        </div>
                        <div  class="col-lg-3 col-6">
                            <input type="text" class="form-control h-28" placeholder="Scan Product Barcode or Case Barcode, Product Title, SKU, Supplier SKU" title="Scan Product Barcode or Case Barcode, Product Title, SKU, Supplier SKU" name="" id="scan-product-textbox" style="display:none;" />
                        </div>

                    </div>
                </div>
                <div class="put-away-filters" style="display: none;" >
                    <div class="goods-in-filter">
                        <a id="sortByGoodsIn" href="javascript:void(0)">
                            Sort by Goods-In Scan
                        </a>
                    </div>
                </div>
                <div id="pendingColumnDiv" class="col-lg-6 pt-2" style="display:none;">
                    <span  class="font-18-dark mr-5 color-red">Pending Product: <span class="font-18-dark bold color-red" id="pendingProduct">0</span></span>
                    <span class="font-18-dark mr-5 color-blue">Pending Qty. <span class="font-18-dark bold color-blue" id="pendingQty">0</span></span>
                </div>
            </div>
            <div id="productListingID" class="inner-content-body p-2" style="display:none;">
                <input type="hidden" id="sort_by" value="title">
                <input type="hidden" id="sort_direction" value="desc">
                <table class="table custom-table cell-align-top" id="putaway-id">
                </table>
                <!--Put Away Product Detail-->

                <!--Put Away Product Detail-->

                <!-- put away popup -->
                <div class="modal fade " id="putAwayDetailScreen1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="custom-modal modal-dialog modal-lg" style="max-width: 1200px;" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="container-fluid">
                                    <div class="row align-items-center">
                                        <div class="col-lg-3">
                                            <h5 class="modal-title" id="exampleModalLabel">Product Detail</h5>
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="text" placeholder="Scan Pallet Location Barcode" class="form-control" id="detail-pallet-textbox" />
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="text" placeholder="Scan Product/Case Barcode" class="form-control" id="detail-product-barcode-textbox" />
                                        </div>
                                    </div>

                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- put away popup -->

            </div>
            <input type="hidden" value="{{route('location.keyword-suggestion')}}" id="locationKeywordURL" />
            <div id="putAwayDetailScreen" style="display: none;">

            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="http://topsdemo.co.in/test_m/barcode_scanner.js"></script>
<script src="{{ asset('js/bootstrap-typeahead.js') }}"></script>
<script type="text/javascript" src="{{asset('js/put-away/put-away.js?v='.CSS_JS_VERSION)}}"></script>
@endsection