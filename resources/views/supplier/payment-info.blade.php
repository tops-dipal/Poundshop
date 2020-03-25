<form action="{{ url('api/api-supplier-save-payment-info') }}" method="POST" class="form-horizontal form-flex" role="form" tab_switch_save id="form-payment">
    @csrf
    <input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
    <!-- Payment Tab -->                                
    <div class="row bb-2">
       <!--  <div class="col-lg-12">
      
            <div class="form-group">
                <label class="col-form-label">@lang('messages.supplier.select_payment_terms')</label>
            </div>
        </div> -->
        
        @php
            $sel_payment_terms = old('payment_term') ? old('payment_term') : @$result->payment_term;
                
            if(empty($sel_payment_terms))
            {
                $sel_payment_terms = '3';
            }    

        @endphp
        <div class="col-lg-5">
            <h3 class="page-title-inner mb-3">@lang('messages.supplier.payment_terms')</h3>
            
            <div class="form-group">
                <label class="col-form-label d-flex align-items-center">
                    <label class="fancy-radio sm">
                        <input type="radio" name="payment_term" {{($sel_payment_terms == '3') ? 'checked="checked"' : "" }} value="3">
                        <span><i></i></span>
                    </label>
                    <input type="text" class="form-control w-50 mr-3" only_digit name="payment_days" value="{{ !empty(old('payment_days')) ? old('payment_days') : @$result->payment_days }}" {{ ($sel_payment_terms == '3') ? 'required="required"' : '' }}>
                    @lang('messages.supplier.days_after_delivery')
                </label>
            </div>
            <div class="form-group">
                <label class="col-form-label">
                    <label class="fancy-radio sm">
                        <input type="radio" name="payment_term" {{($sel_payment_terms == '2') ? 'checked="checked"' : "" }} value="2">
                        <span><i></i>@lang('messages.supplier.proform_invoice')</span>
                    </label>
                </label>
            </div>                    
            <div class="form-group">
                <label class="col-form-label">
                    <label class="fancy-radio sm">
                        <input type="radio" name="payment_term" value="1" {{($sel_payment_terms == '1') ? 'checked="checked"' : "" }}>
                        <span><i></i>@lang('messages.supplier.cash_on_delivery')</span>
                    </label>
                </label>
            </div>
            <hr>
            <div class="form-group">
                <label class="col-form-label d-flex align-items-center">
                    @lang('messages.supplier.we_pay_this_supplier_on')
                    <input type="text" class="form-control w-50 mr-3 ml-3" only_digit name="pay_on_date_every_month" value="{{ !empty(old('pay_on_date_every_month')) ? old('pay_on_date_every_month') : @$result->pay_on_date_every_month }}">
                    @lang('messages.supplier.of_every_month')
                </label>
            </div>
                          
        </div>
        <div class="col-lg-7 bl-2"> 
            <h3 class="page-title-inner mb-3">Discounts</h3>           
            <div class="form-group" id="discountDaysGet" {{ ($sel_payment_terms != 3) ? 'style=display:none' : '' }}>
                <label class="col-form-label d-flex align-items-center">
                    @php
                    $sel_allow_period_discount = old('allow_period_discount') ? old('allow_period_discount') : @$result->allow_period_discount;
                    @endphp
                    <label class="fancy-checkbox sm">
                        <input type="checkbox" name="allow_period_discount" {{ ($sel_allow_period_discount == '1') ? 'checked="checked"' : ''  }} value="1">
                        <span><i></i></span>
                    </label>
                    @lang('messages.supplier.pay_in')
                    <input type="text" class="form-control w-50 mx-3" only_digit name="period_discount_days" value="{{ !empty(old('period_discount_days')) ? old('period_discount_days') : @$result->period_discount_days }}">
                    @lang('messages.supplier.days_and_get')
                    <input type="text" class="form-control w-50 mx-3" name="period_percent_discount" only_numeric value="{{ !empty(old('period_percent_discount')) ? old('period_percent_discount') : @$result->period_percent_discount }}">
                    @lang('messages.supplier.percent_discount')
                </label>
            </div>

            <div class="form-group">
                <label class="col-form-label d-flex align-items-center">
                    @php
                    $sel_allow_overall_discount = old('allow_overall_discount') ? old('allow_overall_discount') : @$result->allow_overall_discount;
                    @endphp
                    <label class="fancy-checkbox sm">
                        <input type="checkbox" name="allow_overall_discount" value="1" {{ ($sel_allow_overall_discount == '1') ? 'checked="checked"' : "" }}>
                        <span><i></i></span>
                    </label>
                    <input type="text" class="form-control w-50 mr-3" only_numeric name="overall_percent_discount" value="{{ !empty(old('overall_percent_discount')) ? old('overall_percent_discount') : @$result->overall_percent_discount }}">
                @lang('messages.supplier.percent_discount')</label>
            </div>

            <div class="form-group">
                @php
                $sel_allow_retro_discount = old('allow_retro_discount') ? old('allow_retro_discount') : @$result->allow_retro_discount;
                @endphp
                <label class="col-form-label">
                    <label class="fancy-checkbox sm">
                        <input type="checkbox" name="allow_retro_discount" {{ ($sel_allow_retro_discount == '1') ? 'checked="checked"' : ''}} value="1">
                        <span><i></i>@lang('messages.supplier.retro_discount')</span>
                    </label>
                </label>
            </div>
            
            <div class="form-group" id="retroOptions" {{ ($sel_allow_retro_discount != '1') ? 'style=display:none' : '' }}>
                <label class="col-form-label d-flex flex-wrap align-items-center">
                    @lang('messages.supplier.retro_if_poundshop_spend')
                    <div class="position-relative">
                        <span class="pound-sign-form-control mx-3 my-1">@lang('messages.common.pound_sign')</span>
                        <input type="text" class="form-control w-120 mx-3 my-1" only_digit name="retro_amount" value="{{ !empty(old('retro_amount')) ? old('retro_amount') : @$result->retro_amount }}">
                    </div>    
                    @lang('messages.supplier.retro_from')
                    @php
                    $sel_retro_from_date = !empty(old('retro_from_date')) ? old('retro_from_date') : @$result->retro_from_date;
                    @endphp
                    <input class="form-control w-120 mx-3 my-1 datepicker" type="text" readonly="" name="retro_from_date" readonly="" value="{{ !empty($sel_retro_from_date) ? system_date($sel_retro_from_date) : system_date()}}">
                    @lang('messages.supplier.retro_to')
                    <input class="form-control w-120 mx-3 my-1 datepicker" type="text" readonly="" name="retro_to_date" readonly="" value="{{ !empty(old('retro_to_date')) ? old('retro_to_date') : system_date(@$result->retro_to_date) }}">.
                    @lang('messages.supplier.retro_than_supplier_will_provide')
                    <input type="text" class="form-control w-50 mx-3 my-1" name="retro_percent_discount" value="{{ !empty(old('retro_percent_discount')) ? old('retro_percent_discount') : @$result->retro_percent_discount }}">
                    @lang('messages.supplier.percent_discount_excludes_vat')
                    
                    <label class="col-form-label d-flex align-items-center">
                        <label class="fancy-radio sm ml-3">
                            <input type="radio" name="retro_type" checked="checked" value="1">
                            <span><i></i></span>
                        </label>
                        @lang('messages.supplier.retro_type_order_placed')
                    </label>
                    <label class="col-form-label d-flex align-items-center ml-3">
                        <label class="fancy-radio sm">
                            <input type="radio" name="retro_type" {{ (@$result->retro_type == 2) ? 'checked="checked"' : "" }} value="2">
                            <span><i></i></span>
                        </label>
                        @lang('messages.supplier.retro_type_delivered')
                    </label>
                </label>
            </div>            
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-title-inner mt-5 mb-4">@lang('messages.supplier.wire_transfer_details')</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group row">
                <label class="col-lg-2 col-form-label">@lang('messages.supplier.beneficiary_name')</label>
                <div class="col-lg-10">
                    <input type="text" class="form-control" placeholder="" name="beneficiary_name" value="{{ !empty($result->beneficiary_name) ? $result->beneficiary_name : @$result->name}}">
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <p class="mt-3 mb-2 font-500">@lang('messages.supplier.beneficiary_address')</p>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.bene_street_address_1')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bene_address1" value="{{ !empty(old('bene_address1')) ? old('bene_address1') : @$result->bene_address1}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.bene_street_address_2')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bene_address2" value="{{ !empty(old('bene_address2')) ? old('bene_address2') : @$result->bene_address2}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.country')</label>
                <div class="col-lg-8">
                    @php
                    $sel_bene_country = !empty(old('bene_country')) ? old('bene_country') : @$result->bene_country;
                    @endphp
                    <select class="form-control" name="bene_country" onchange="PoundShopApp.commonClass.getState(this, 'beneStateDropdown', 'bene_cityDropdown')">
                        <option value="">Select @lang('messages.supplier.country')</option>
                        @foreach($countries as $country)
                        <option value="{{$country->id}}" {{($sel_bene_country == $country->id) ? 'selected="selected"' : '' }} >{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.state')</label>
                <div class="col-lg-8">
                    @php
                    $sel_bene_state = !empty(old('bene_state')) ? old('bene_state') : @$result->bene_state;
                    @endphp
                    <select class="form-control" name="bene_state" {{ empty($country_states[$sel_bene_country]) ? 'disabled="disabled"' : '' }}  id="beneStateDropdown" onchange="PoundShopApp.commonClass.getCity(this, 'bene_cityDropdown')" >
                        <option value="">Select @lang('messages.supplier.state')</option>
                        @if(!empty($country_states[$sel_bene_country]))
                        @foreach($country_states[$sel_bene_country] as $state)
                        <option value="{{$state['id']}}" {{ $sel_bene_state == $state['id'] ? 'selected="selected"' : "" }} > {{$state['name']}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.city')</label>
                <div class="col-lg-8">
                    @php
                    $sel_bene_city = !empty(old('bene_city')) ? old('bene_city') : @$result->bene_city;
                    @endphp
                    <select class="form-control" name="bene_city" {{empty($state_cities[$sel_bene_state]) ? 'disabled="disabled"' : '' }} id="bene_cityDropdown">
                        <option value="">Select @lang('messages.supplier.city')</option>
                        @if(!empty($state_cities[$sel_bene_state]))
                        @foreach($state_cities[$sel_bene_state] as $city)
                        <option value="{{$city['id']}}" {{ $sel_bene_city == $city['id'] ? 'selected="selected"' : "" }} > {{$city['name']}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.zipcode')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bene_zipcode" value="{{ !empty(old('bene_zipcode')) ? old('bene_zipcode') : @$result->bene_zipcode}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.account_no')</label>
                <div class="col-lg-8">
                    <input type="text" only_digit class="form-control" placeholder="" name="bene_account_no" value="{{ !empty(old('bene_account_no')) ? old('bene_account_no') : @$result->bene_account_no}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.bank_name')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bene_bank_name" value="{{ !empty(old('bene_bank_name')) ? old('bene_bank_name') : @$result->bene_bank_name}}">
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <p class="mt-3 mb-2 font-500">@lang('messages.supplier.bank_address')</p>
        </div>
        
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.bene_street_address_1')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bank_address1" value="{{ !empty(old('bank_address1')) ? old('bank_address1') : @$result->bank_address1}}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.bene_street_address_2')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bank_address2" value="{{ !empty(old('bank_address2')) ? old('bank_address2') : @$result->bank_address2 }}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.country')</label>
                <div class="col-lg-8">
                    @php
                    $sel_bank_country = !empty(old('bank_country')) ? old('bank_country') : @$result->bank_country;
                    @endphp
                    <select class="form-control" name="bank_country" onchange="PoundShopApp.commonClass.getState(this, 'bankStateDropdown', 'bankCityDropdown')">
                        <option value="">Select @lang('messages.supplier.country')</option>
                        @foreach($countries as $country)
                        <option value="{{$country->id}}" {{ ($sel_bank_country == $country->id) ? 'selected="selected"' : "" }}>{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.state')</label>
                <div class="col-lg-8">
                    @php
                    $sel_bank_state = !empty(old('bank_state')) ? old('bank_state') : @$result->bank_state;
                    @endphp
                    <select class="form-control" name="bank_state" {{ empty($country_states[$sel_bank_country]) ? 'disabled="disabled"' : ""}} id="bankStateDropdown" onchange="PoundShopApp.commonClass.getCity(this, 'bankCityDropdown')">
                        <option value="">Select @lang('messages.supplier.state')</option>
                        @if(!empty($country_states[$sel_bank_country]))
                        @foreach($country_states[$sel_bank_country] as $states)
                        <option value="{{$states['id']}}" {{ ($sel_bank_state == $states['id']) ? 'selected = "selected"' : "" }}>{{$states['name']}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.city')</label>
                <div class="col-lg-8">
                    @php
                    $sel_bank_city = !empty(old('bank_city')) ? old('bank_city') : @$result->bank_city;
                    @endphp
                    <select class="form-control" name="bank_city" {{empty($state_cities[$sel_bank_state]) ? 'disabled = "disabled"' : ''}} id="bankCityDropdown">
                        <option value="">Select @lang('messages.supplier.city')</option>
                        @if(!empty($state_cities[$sel_bank_state]))
                        @foreach($state_cities[$sel_bank_state] as $city)
                        <option value="{{$city['id']}}" {{ ($city['id'] == $sel_bank_city) ? 'selected="selected"' : '' }}>{{$city['name']}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.zipcode')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bank_zipcode" value="{{ !empty(old('bank_zipcode')) ? old('bank_zipcode') : @$result->bank_zipcode }}">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.bank_swift_code')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bank_swift_code" value="{{ !empty(old('bank_swift_code')) ? old('bank_swift_code') : @$result->bank_swift_code }}">
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="form-group row">
                <label class="col-lg-4 col-form-label">@lang('messages.supplier.bank_iban_no')</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" placeholder="" name="bank_iban_no" value="{{ !empty(old('bank_iban_no')) ? old('bank_iban_no') : @$result->bank_iban_no }}">
                </div>
            </div>
        </div>
    </div>
</form>    