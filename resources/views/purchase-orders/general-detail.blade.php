<form method="post" id="create-po-form" action="{{route('api-purchase-orders.update',$purchaseOrder->id)}}">
    @csrf
    @method('PUT')
    <input type="hidden" id="id" name="id" value="{{$purchaseOrder->id}}" />
    <input type="hidden" id="countries_commodities" name="countries_commodities" value="{{isset($purchaseOrder->purchaseOrderCountry->commodityCodes) ? json_encode($purchaseOrder->purchaseOrderCountry->commodityCodes) : json_encode([])}}" />
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.supplier')<span class="asterisk">*</span></label>
                <div class="col-lg-8">
                    @php $supplierContacts=[]; @endphp
                    <select class="form-control" id="supplier" name="supplier"  readonly="readonly" disabled="disabled">
                        @php $supplierContacts = $purchaseOrder->supplier->SupplierContact; @endphp
                        <option selected="selected"value="{{$purchaseOrder->supplier->id}}">{{$purchaseOrder->supplier->name}}</option>
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
                        @foreach($supplierContacts as $contact)
                        @if($contact->id == $purchaseOrder->supplier_contact)
                        <option selected="selected" value="{{$contact->id}}">{{$contact->name}}</option>
                        @else
                        <option value="{{$contact->id}}">{{$contact->name}}</option>
                        @endif
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.po_number')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" disabled="disabled" type="text" class="form-control"  placeholder="" value="{{$purchaseOrder->po_number}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.status')</label>
                <div class="col-lg-8">
                    <select class="form-control" id="po_status" name="po_status" @if($purchaseOrder->po_status == 10) disabled="disabled" @endif>
                            @foreach(config('params.po_status') as $key=>$value)
                            <option  @if($value == $purchaseOrder->po_status) selected='selected' @endif value="{{$value}}">{{$key}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.po_type')<span class="asterisk">*</span></label>
                <div class="col-lg-8">
                    <select class="form-control" id="po_import_type" name="po_import_type"  disabled="disabled" readonly="readonly">
                        <option value="">Select UK PO or Import PO</option>
                        @foreach(config('params.po_import_type') as $key=>$value)
                        <option @if($value == $purchaseOrder->po_import_type) selected='selected' @endif value="{{$value}}">{{$key}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.country')<span class="asterisk">*</span></label>
                <div class="col-lg-8">
                    <select @if($purchaseOrder->po_import_type == 1) disabled="disabled" @endif class="form-control" id="country_id" name="country_id" @if($purchaseOrder->po_status > 5) disabled="disabled" @endif>
                             <option value="">Select Country</option>
                        @foreach($countries as $country)
                        @if($purchaseOrder->po_import_type == 2)
                        @if($country->id != 230)
                        <option @if($country->id == $purchaseOrder->country_id) selected='selected' @endif value="{{$country->id}}">{{$country->name}}</option>
                        @endif
                        @else
                        <option @if($country->id == $purchaseOrder->country_id) selected='selected' @endif value="{{$country->id}}">{{$country->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @if($purchaseOrder->po_import_type == 2)
        <div class="col-lg-6" >
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.incorterms')</label>
                <div class="col-lg-8">
                    <select class="form-control" id="incoterms" name="incoterms">
                        <option value="">Select Incoterms</option>
                        @foreach(config('params.incoterms') as $key=>$value)
                        <option @if($key == $purchaseOrder->incoterms) selected='selected' @endif value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.mode_of_shipment')</label>
                <div class="col-lg-8">
                    <select class="form-control" id="mode_of_shipment" name="mode_of_shipment">
                        <option value="">Select Mode of Shipment</option>
                        @foreach(config('params.shippment') as $key=>$value)
                        <option @if($value == $purchaseOrder->mode_of_shipment) selected='selected' @endif value="{{$value}}">{{$key}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endif
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.supplier_order_number')</label>
                <div class="col-lg-8">
                    <input type="text" name="supplier_order_number"  class="form-control" id="supplier_order_number"  maxlength="12" value="{{$purchaseOrder->supplier_order_number}}"  >
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.ord_date')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="po_date" name="po_date" placeholder="" value="{{$purchaseOrder->po_date}}" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">

                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.cancelled_date')</label>
                <div class="col-lg-8">
                    <input type="text" readonly="readonly" disabled="disabled"  class="form-control" id="po_cancel_date" name="po_cancel_date" placeholder="" value="{{$purchaseOrder->po_cancel_date}}" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.expected_date')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="exp_deli_date" name="exp_deli_date"   placeholder="" value="{{$purchaseOrder->exp_deli_date}}" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.notes')</label>
                <div class="col-lg-8">
                    <textarea class="form-control" name="notes" id="notes">{{$purchaseOrder->notes}} </textarea>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.supplier_comment')</label>
                <div class="col-lg-8">
                    <textarea class="form-control" name="supplier_comment" id="supplier_comment" maxlength="500">{{$purchaseOrder->supplier_comment}}</textarea>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.dropshipping')</label>
                <div class="col-lg-8">
                    <label class="fancy-radio">
                        <input type="radio" name="is_drop_shipping" value="1" @if($purchaseOrder->is_drop_shipping == 1) checked="checked" @endif />
                               <span><i></i>Yes</span>
                    </label>
                    <label class="fancy-radio">
                        <input type="radio" name="is_drop_shipping" value="0" @if($purchaseOrder->is_drop_shipping == 0) checked="checked" @endif/>
                               <span><i></i>No</span>
                    </label>
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
                    <select  readonly="readonly" class="form-control" id="recev_warehouse" name="recev_warehouse" disabled="disabled">
                        <option selected="selected">{{$purchaseOrder->warehouse}}</option>
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
                    <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="address1" value="{{$purchaseOrder->street_address1}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.add2')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="address2" value="{{$purchaseOrder->street_address2}}">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.country')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="country" value="{{$purchaseOrder->country}}">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.state')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="state" value="{{$purchaseOrder->state}}">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.city')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="city" value="{{$purchaseOrder->city}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.zipcode')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="pincode" value="{{$purchaseOrder->zipcode}}">
                </div>
            </div>
        </div>
    </div>

    <h3 class="p-title mb-2 mt-4">@lang('messages.purchase_order.form.bill_address')</h3>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.add1')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" type="text" class="form-control" name="setting_address1" value="{{$purchaseOrder->billing_street_address1}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.form.add2')</label>
                <div class="col-lg-8">
                    <input readonly="readonly" type="text" class="form-control" name="setting_address2" value="{{$purchaseOrder->billing_street_address2}}">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.country')</label>
                <div class="col-lg-8">
                    <input readonly="readonly"  type="text" class="form-control" name="setting_country" value="{{$purchaseOrder->billing_country}}">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.state')</label>
                <div class="col-lg-8">
                    <input readonly="readonly"  type="text" class="form-control" name="setting_state" value="{{$purchaseOrder->billing_state}}">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.city')</label>
                <div class="col-lg-8">
                    <input readonly="readonly"  type="text" class="form-control" name="setting_city" value="{{$purchaseOrder->billing_city}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.purchase_order.zipcode')</label>
                <div class="col-lg-8">
                    <input readonly="readonly"  type="text" class="form-control" name="setting_pincode" value="{{$purchaseOrder->billing_zipcode}}">
                </div>
            </div>
        </div>
    </div>
</form>