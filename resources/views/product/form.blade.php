@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('css')
<style type="text/css">
.label-info {
background-color: #5bc0de;
}
</style>
@endsection
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>
        <div class="center-items">
            <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'buying-range' ? 'active' : ''}}" data-toggle="tab" href="#buying-range" role="tab" aria-controls="buying-range">
                        @lang('messages.inventory.buying_range')
                    </a>
                </li>
                @if(!empty($id))
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'stock-file' ? 'active' : ''}}" data-toggle="tab" href="#stock-file" role="tab" aria-controls="stock-file">
                        @lang('messages.inventory.stock_file')
                    </a>
                </li>
                <li class="nav-item" style="{{ ($result->product_type == 'parent') ? 'display:block' : 'display:none' }}" id="variation-li">
                    <a class="nav-link {{ $active_tab == 'variation' ? 'active' : ''}}" data-toggle="tab" href="#variation" role="tab" aria-controls="variation">
                        @lang('messages.inventory.product_type_variation')
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'barcodes' ? 'active' : ''}}" data-toggle="tab" href="#barcodes" role="tab" aria-controls="barcodes">
                        @lang('messages.inventory.barcodes')
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'images' ? 'active' : ''}}" data-toggle="tab" href="#images" role="tab" aria-controls="images">
                        @lang('messages.inventory.images')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'suppliers' ? 'active' : ''}}" data-toggle="tab" href="#suppliers" role="tab" aria-controls="suppliers">
                        @lang('messages.inventory.suppliers')
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'warehouse' ? 'active' : ''}}" data-toggle="tab" href="#warehouse" role="tab" aria-controls="warehouse">
                        @lang('messages.inventory.warehouse')
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <div class="right-items">
            <a href="{{route('product.index')}}" class="btn btn-gray btn-header px-4">@lang('messages.common.cancel') </a>
            <button type="submit" form="form-{{$active_tab}}" class="btn btn-blue btn-header px-4" id="head-submit"> @lang('messages.common.save') </button>
            <button type="button" class="btn btn-add btn-blue font-12" onclick="addBarcode(this)" id="add-barcode-btn">
            <span class="icon-moon icon-Add"></span>
            @lang('messages.inventory.add_barcode')
            </button>
        </div>
    </div>
    <div class="card-flex-container">
        
        <div class="form-fields">
            <div class="container-fluid">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show {{ $active_tab == 'buying-range' ? 'active' : ''}}" role="tabpanel" id="buying-range" aria-labelledby="buying-range">
                        
                        <form action="{{ url('api/api-product-save-buying-range') }}" method="POST" class="form-horizontal form-flex" id="form-buying-range" tab_switch_save enctype="multipart/form-data" role="form" refreash_url="{{ url('product/form-buying-range/'.$id) }}" refreash_url_stock="{{ url('product/form-stock-file/'.$id) }}">
                            @include('product.form_buying_range')
                        </form>
                    </div>
                    @if(!empty($id))
                    <div class="tab-pane fade show {{ $active_tab == 'stock-file' ? 'active' : ''}}" role="tabpanel" id="stock-file" aria-labelledby="stock-file">
                        <form action="{{ url('api/api-product-save-stock-file') }}" method="POST" class="form-horizontal form-flex" id="form-stock-file" tab_switch_save enctype="multipart/form-data" role="form" data-parsley-validate>
                            @include('product.form_stock_file')
                        </form>
                    </div>
                    <div class="tab-pane fade show {{ $active_tab == 'variation' ? 'active' : ''}}" role="tabpanel" id="variation" aria-labelledby="variation">
                        <form action="{{ url('api/api-product-save-variation') }}" method="POST" class="form-horizontal form-flex" id="form-variation" tab_switch_save enctype="multipart/form-data" role="form" refreash_url="{{ url('product/form-variations/'.$result->id) }}">
                            @include('product.form_variations')
                        </form>
                    </div>
                    <div class="tab-pane fade show {{ $active_tab == 'barcodes' ? 'active' : ''}}" role="tabpanel" id="barcodes" aria-labelledby="barcodes">
                        <form action="{{ url('api/api-product-save-barcodes') }}" method="POST" class="form-horizontal form-flex" id="form-barcodes" tab_switch_save enctype="multipart/form-data" role="form" refreash_url="{{ url('product/form-barcodes/'.$result->id) }}">
                            @include('product.form_barcode')
                        </form>
                    </div>
                    <div class="tab-pane fade show {{ $active_tab == 'images' ? 'active' : ''}}" role="tabpanel" id="images" aria-labelledby="images">
                        <form action="{{ url('api/api-product-save-images') }}" method="POST" class="form-horizontal form-flex" id="form-images" tab_switch_save enctype="multipart/form-data" role="form" refreash_url="{{ url('product/form-images/'.$result->id) }}">
                            @include('product.form_images')
                        </form>
                    </div>
                    <div class="tab-pane fade show {{ $active_tab == 'suppliers' ? 'active' : ''}}" role="tabpanel" id="suppliers" aria-labelledby="suppliers">
                        <form action="{{ url('api/api-product-update-suppliers') }}" method="POST" class="form-horizontal form-flex" id="form-suppliers" tab_switch_save enctype="multipart/form-data" role="form">
                            @include('product.form_suppliers')
                        </form>
                    </div>
                    <div class="tab-pane fade show {{ $active_tab == 'warehouse' ? 'active' : ''}}" role="tabpanel" id="warehouse" aria-labelledby="warehouse">
                        <form action="{{ url('api/api-product-save-warehouse') }}" method="POST" class="form-horizontal form-flex" id="form-warehouse" tab_switch_save enctype="multipart/form-data" role="form">
                            @include('product.form_warehouse')
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="barcodeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg custom-modal" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal" aria-label="Close">
                    @lang('messages.common.cancel')
                    </button>
                    <button type="submit" class="btn btn-green font-12 px-3 ml-3" form="form-barcodes">@lang('messages.common.submit')</button>
                </div>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="barcode_id" form="form-barcodes" value="">
                <div class="row">                        
                    @php
                    $sel_barcode_type = '1';
                    @endphp
                    @foreach(barcodeType() as $b_type_id => $b_type_name)
                    <label class="col-lg-4">
                        <label class="fancy-radio">
                            <input type="radio" name="barcode_type" value="{{$b_type_id}}" {{($sel_barcode_type == $b_type_id) ? 'checked="checked"' : ''}} form="form-barcodes">
                            <span><i></i>{{$b_type_name}}</span>
                        </label>
                    </label>
                    @endforeach                        
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-lg-12 col-form-label">@lang('messages.common.barcode') <span class="asterisk">*</span></label>
                            <div class="col-lg-12 mt-1">
                                <input type="text" class="form-control" placeholder="" name="barcode" form="form-barcodes">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="case_quantity">
                        <div class="form-group row">
                            <label class="col-lg-12 col-form-label" id="case_qty_label">@lang('messages.inventory.inner_case_qty') <span class="asterisk">*</span></label>
                            <div class="col-lg-12 mt-1">
                                <input type="text" only_digit class="form-control" placeholder="" name="case_quantity" form="form-barcodes">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="case_parent_barcode">
                        <div class="form-group row">
                            <label class="col-lg-12 col-form-label">@lang('messages.inventory.select_outer_barcode') <span class="asterisk">*</span></label>
                            <div class="col-lg-12 mt-1">
                                <select class="form-control" name="parent_id" form="form-barcodes">
                                    <option value="">@lang('messages.inventory.select_outer_barcode')</option>
                                    @if(!empty($outer_barcodes))
                                        @foreach($outer_barcodes as $barcode_id => $barcode)
                                            <option value="{{ $barcode_id }}">{{ $barcode }}</option>
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
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/product/form.js')}}"></script>

<script type="text/javascript" src="{{asset('js/product/warehouse-modal.js')}}"></script>

@endsection
@section('css')
<style type="text/css">
.disabledTab{
/*pointer-events: none;*/
}
</style>
@endsection