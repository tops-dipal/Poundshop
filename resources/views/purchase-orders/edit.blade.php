@extends('layouts.app')
@section('title',__('messages.purchase_order.edit_po').': #'.$purchaseOrder->po_number)
@section('content')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
@endsection

<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.purchase_order.edit_po') : # {{$purchaseOrder->po_number}}</h3>
        <div class="center-items">
            <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                <li role="presentation" class="nav-item">
                    <a class="nav-link tab-click" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">
                        @lang('messages.purchase_order.tab.detail')
                    </a>
                </li>
                <li class="nav-item">
                    <a   class="nav-link tab-click" id="item-tab" data-toggle="tab" href="#items" role="tab" aria-controls="contact" aria-selected="false">
                        @lang('messages.purchase_order.tab.items')
                    </a>
                </li>
                <li class="nav-item">
                    <a   class="nav-link tab-click" id="delivery-tab" data-toggle="tab" href="#deliveries" role="tab" aria-controls="deliveries" aria-selected="false">
                        @lang('messages.purchase_order.tab.deliveries')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-click"  id="revision-tab" data-toggle="tab" href="#revise" role="tab" aria-controls="payment" aria-selected="false">
                        @lang('messages.purchase_order.tab.revision')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-click"  id="terms-tab" data-toggle="tab" href="#terms" role="tab" aria-controls="terms" aria-selected="false">
                        @lang('messages.purchase_order.tab.terms')
                    </a>
                </li>
            </ul>
        </div>
        <div class="right-items">

            <div class="dropdown more-action-dropdown">
                <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                    <span class="icon-moon icon-Drop-Down-1"/>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                    <h4 class="title">More Actions</h4>

                    <!--            @if($purchaseOrder->po_status == 10)
                                <button class="btn btn-add delete-many" title="@lang('messages.modules.pallets_delete')">
                                  <span class="icon-moon red icon-Delete"></span>Delete P.O
                                </button>
                                @endif-->

                    <div class="item-actions">
                        <button title="Revise P.O" class="btn btn-add" id="revise-po-btn">
                            <span class="icon-moon red icon-Reverse-Purchse-Order"></span>@lang('messages.purchase_order.revise')
                        </button>
                        <a href="{{route('purchase-order.download-pdf','purchase_order_id='.$purchaseOrder->id)}}" title="Download PO" class="btn btn-add">
                            <span class="icon-moon yellow icon-Download"></span>@lang('messages.purchase_order.download')
                        </a>
                        <a title="Send Mail" class="btn btn-add" id="sendEmailBtn">
                            <span class="icon-moon gray icon-Mail"></span>@lang('messages.purchase_order.send_email')
                        </a>
                    </div>
                </div>
            </div>
            <button style="@if($purchaseOrder->po_status < 6) display: block; @else display: none; @endif" class="btn btn-green btn-header" title="@lang('messages.purchase_order.items.add_item')" id="show-modal-btn">
                <span class="icon-moon icon-Add font-10 mr-2"></span>@lang('messages.purchase_order.items.add_item')
            </button>
            <a title="Cancel" class="btn btn-gray btn-header px-4" href="{{route('purchase-orders.index')}}">@lang('messages.common.cancel')</a>
            <button title="Save" type="submit" style="display: none;"  id="create-po-button"  class="btn btn-blue btn-header px-4">@lang('messages.common.save')</button>
            <button title="Save" type="submit" style="@if($purchaseOrder->po_status < 6) display: block; @else display: none; @endif"  id="save-po-btn"  class="btn btn-blue btn-header px-4 ">@lang('messages.common.save')</button>
            <button title="Save" type="submit" style="display: none;"  id="update-term-button"  class="btn btn-blue btn-header px-4 ">@lang('messages.common.save')</button>
        </div>
    </div>
    <div class="card-flex-container pt-0  custom_fix_header">
        <input type="hidden" id="hidden_standard_rate" value="{{floatval($purchaseOrder->standar_rate_value)}}" />
        <input type="hidden" id="hidden_zero_rate" value="{{floatval($purchaseOrder->zero_rate_value)}}" />
        <input type="hidden" id="hidden_country_id" value="{{$purchaseOrder->country_id}}" />
        <input type="hidden" id="hidden_po_status" value="{{$purchaseOrder->po_status}}" />
        <input type="hidden" id="hidden_min_po_amount" value="{{floatval($purchaseOrder->supplier->min_po_amt)}}" />

        <div class="tab-content h-100" id="myTabContent">
            <!--PO Detail -->
            <div class="tab-pane py-4 fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="form-fields">
                    <div class="container-fluid">
                        @include('purchase-orders.general-detail')
                    </div>
                </div>
            </div>
            <!--PO Detail -->
            <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="item-tab">                
                @include('purchase-orders.po-items')                    
            </div>
            <!-- PO Delivery-->
            <div class="tab-pane h-100 fade" id="deliveries" role="tabpanel" aria-labelledby="delivery-tab">
                @include('purchase-orders.po-delivered')
            </div>
            <div class="tab-pane h-100 fade" id="revise" role="tabpanel" aria-labelledby="revision-tab">
                @include('purchase-orders.po-revision')
            </div>
            <div class="tab-pane py-4 fade" id="terms" role="tabpanel" aria-labelledby="terms-tab">
                <div class="form-fields">
                    <div class="container-fluid">
                        @include('purchase-orders.terms')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="{{asset('js/po/edit.js?v='.CSS_JS_VERSION)}}"></script>
<script type="text/javascript" src="{{asset('js/po/revision.js?v='.CSS_JS_VERSION)}}"></script>
<script type="text/javascript" src="{{asset('js/po/delivery.js?v='.CSS_JS_VERSION)}}"></script>
<script type="text/javascript" src="{{asset('js/po/move_to_new_po.js?v='.CSS_JS_VERSION)}}"></script>
@endsection