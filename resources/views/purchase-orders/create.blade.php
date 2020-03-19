@extends('layouts.app')
@section('title', __('messages.purchase_order.create_po'))
@section('content')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
@endsection
<div class="content-card custom-scroll">
    <form method="post" id="create-po-form" action="{{route('api-purchase-orders.store')}}">
        @csrf

        <input type="hidden" name="hidden_standard_rate" id="hidden_standard_rate" value="{{isset($settings[0]->column_val) ? $settings[0]->column_val : 0}}" />
        <input type="hidden" name="hidden_zero_rate" id="hidden_zero_rate" value="{{isset($settings[1]->column_val) ? $settings[1]->column_val : 0 }}" />
        <input type="hidden" name="hidden_country" id="hidden_country" />
        <div class="content-card-header">
            <h3 class="page-title">@lang('messages.purchase_order.create_po')</h3>
            <div class="center-items">
                <ul class="nav nav-tabs header-tab" role="tablist">
                    <li role="presentation" class="active nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">
                            PO Details
                        </a>
                    </li>
                </ul>
            </div>
            <div class="right-items">
                <a class="btn btn-gray btn-form btn-header px-4"  href="{{route('purchase-orders.index')}}">@lang('messages.common.cancel')</a>
                <button id="create-po-button" class="btn btn-blue btn-header px-4">@lang('messages.common.save')</button>
            </div>
        </div>
        <div class="card-flex-container">

            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <!--PO Detail -->
                        <input type="hidden" id="po_id" />
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.supplier')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-control custom-select-search " id="supplier" name="supplier">
                                                <option value="">Select Supplier Name</option>
                                                @foreach($suppliers as $supplier)
                                                <option data-country="{{$supplier->country_id}}" value="{{$supplier->id}}">{{$supplier->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.supplier_contact')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="supplier_contact" name="supplier_contact">
                                                <option value=""> Select Supplier Contact</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.po_number')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control"  placeholder="" value="{{$poNumber}}">
                                            <input type="hidden" id="po_number" name="po_number" value="{{$poNumber}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.status')</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="po_status" name="po_status">
                                                @foreach(config('params.po_status') as $key=>$value)
                                                <option value="{{$value}}">{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.po_type')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="po_import_type" name="po_import_type" disabled="disabled">
                                                <option value="">Select UK PO or Import PO</option>
                                                @foreach(config('params.po_import_type') as $key=>$value)
                                                <option value="{{$value}}">{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.country')</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="country_id" name="country_id">
                                                <option value="">Select Country</option>
                                                @foreach($countries as $country)
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 incorn_mode">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.incorterms')</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="incoterms" name="incoterms">
                                                <option value="">Select Incoterms</option>
                                                @foreach(config('params.incoterms') as $key=>$value)
                                                <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 incorn_mode">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.mode_of_shipment')</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="mode_of_shipment" name="mode_of_shipment">
                                                <option value="">Select Mode of Shipment</option>
                                                @foreach(config('params.shippment') as $key=>$value)
                                                <option value="{{$value}}">{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.supplier_order_number')</label>
                                        <div class="col-lg-8">
                                            <input type="text" name="supplier_order_number" class="form-control" id="supplier_order_number"  maxlength="12">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.ord_date')</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="po_date" name="po_date" placeholder="" autocomplete="off" value="{{\Carbon\Carbon::now()->format('d-M-Y')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.cancelled_date')</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" readonly="readonly" disabled="disabled" id="po_cancel_date" name="po_cancel_date" placeholder="" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.expected_date')</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="exp_deli_date" name="exp_deli_date"   placeholder="" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.notes')</label>
                                        <div class="col-lg-8">
                                            <textarea class="form-control" name="notes" id="notes"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.supplier_comment')</label>
                                        <div class="col-lg-8">
                                            <textarea class="form-control" name="supplier_comment" id="supplier_comment" maxlength="500"></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <h3 class="p-title mb-2 mt-4">@lang('messages.purchase_order.form.ship_address') </h3>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.warehouse')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="recev_warehouse" name="recev_warehouse">
                                                <option value="">Select Warehouse</option>
                                                @foreach($wareHouses as $wareHouse)
                                                <option {{($wareHouse->is_default == '1') ? 'selected="selected"' : "" }} data-city="{{$wareHouse->getCity}}" data-country="{{$wareHouse->getCountry}}" data-state="{{$wareHouse->getState}}" data-info="{{$wareHouse}}" value="{{$wareHouse->id}}">{{$wareHouse->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.add1')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="address1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.add2')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="address2">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.country')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="country">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.state')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="state">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.city')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="city">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.zipcode')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="pincode">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="p-title mb-2 mt-4">@lang('messages.purchase_order.form.bill_address') </h3>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.add1')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" type="text" class="form-control" name="setting_address1" value="{{isset($settings[2]->column_val) ? $settings[2]->column_val : ''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.add2')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" type="text" class="form-control" name="setting_address2" value="{{isset($settings[3]->column_val) ? $settings[3]->column_val : ''}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.country')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly"  type="text" class="form-control" name="setting_country" value="{{isset($settings[4]->column_val) ? $settings[4]->column_val : ''}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.state')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly"  type="text" class="form-control" name="setting_state" value="{{isset($settings[5]->column_val) ? $settings[5]->column_val : ''}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.city')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly"  type="text" class="form-control" name="setting_city" value="{{isset($settings[6]->column_val) ? $settings[6]->column_val : ''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.zipcode')</label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly"  type="text" class="form-control" name="setting_pincode" value="{{isset($settings[7]->column_val) ? $settings[7]->column_val : ''}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@section('script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="{{asset('js/po/create.js?v='.CSS_JS_VERSION)}}"></script>
@endsection