 <!-- General Content -->
    <form action="{{url('api/api-supplier-save-general-info')}}" method="POST" class="form-horizontal form-flex" role="form" tab_switch_save id="form-general">
        @csrf
        <input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.supplier_name') <span class="asterisk">*</span></label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" placeholder="" name="name" value="{{ !empty(old('name')) ? old('name') : @$result->name }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.account_no')</label>
                    <div class="col-lg-8">
                        <input type="text" only_digit class="form-control" placeholder="" value="{{ !empty(old('account_no')) ? old('account_no') : @$result->account_no }}" name="account_no">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.min_po_amt')</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" placeholder="" name="min_po_amt" only_numeric_dimension value="{{ !empty(old('min_po_amt')) ? old('min_po_amt') : @$result->min_po_amt }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.avg_lead_time')</label></label>
                    <div class="col-lg-8">
                        <input type="text" only_digit class="form-control" placeholder="" name="avg_lead_time" value="{{ !empty(old('avg_lead_time')) ? old('avg_lead_time') : @$result->avg_lead_time }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.supplier_category')</label>
                    <div class="col-lg-8">
                        <select class="form-control" name="supplier_category">
                            @php
                            $select_sup_category_id = !empty(old('supplier_category')) ? old('supplier_category') : @$result->supplier_category;
                            @endphp
                            
                            @foreach(supplierCategory() as $supplier_category_id =>
                            $supplier_category)
                            <option value="{{$supplier_category_id}}" {{($select_sup_category_id == $supplier_category_id) ? 'selected="selected"' : ""}}>{{$supplier_category}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.credit_limit_allowed')</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" placeholder="" name="credit_limit_allowed" value="{{ !empty(old('credit_limit_allowed')) ? old('credit_limit_allowed') : @$result->credit_limit_allowed }}" only_numeric_dimension>
                    </div>
                </div>
            </div>
             <div class="col-lg-12">
                <div class="form-group row">
                    <label for="inputPassword" class="col-lg-2 col-form-label">@lang("messages.user_management.address")</label>
                    <div class="col-lg-10">
                        <input  type="text" class="form-control" id="address" placeholder="@lang('messages.common.search_google_address')" name="address_line" >
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.address_line_1')</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" placeholder="" name="address_line1" id="address_line1" value="{{ !empty(old('address_line1')) ? old('address_line1') : @$result->address_line1 }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.address_line_2')</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" placeholder="" name="address_line2" id="address_line2" value="{{ !empty(old('address_line2')) ? old('address_line2') : @$result->address_line2 }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.country')<span class="asterisk">*</span></label>
                    <div class="col-lg-8">
                        <select class="form-control country_id" name="country_id" onchange="PoundShopApp.commonClass.getStateList(this)">
                            @php
                            $sel_country_id = !empty($result->country_id) ? $result->country_id : '230';
                            @endphp
                            <option value="">Select @lang('messages.supplier.country')</option>
                            @foreach($countries as $country)
                            <option value="{{$country->id}}" {{($sel_country_id == $country->id) ? 'selected="selected"': '' }}>{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            {{--<div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.state')</label>
                    <div class="col-lg-8">
                        <select class="form-control" name="state_id" {{empty($country_states[$sel_country_id]) ? 'disabled' : "" }} id="stateDropdown" onchange="PoundShopApp.commonClass.getCity(this)">
                            @php
                            $select_state_id =  !empty(old('state_id')) ? old('state_id') : @$result->state_id;
                            @endphp
                            
                            <option value="">Select @lang('messages.supplier.state')</option>
                            
                            @if(!empty($country_states[$sel_country_id]))
                            @foreach($country_states[$sel_country_id] as $states)
                            <option value="{{$states['id']}}" {{($select_state_id == $states['id']) ? 'selected = "selected"' : "" }} > {{$states['name']}} </option>
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
                        <select class="form-control" name="city_id" {{empty($state_cities[$select_state_id]) ? "disabled" : ""}}  id="cityDropdown">
                            @php
                            $select_city_id =  !empty(old('city_id')) ? old('city_id') : @$result->city_id;
                            @endphp
                            
                            <option value="">Select @lang('messages.supplier.city')</option>
                            @if(!empty($state_cities[$select_state_id]))
                            @foreach($state_cities[$select_state_id] as $city)
                            <option value="{{$city['id']}}" {{($select_city_id == $city['id']) ? 'selected = "selected"' : "" }}> {{$city['name']}} </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>--}}
            <div class="col-lg-6">
                <div class="form-group row">
                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.state")<span class="asterisk">*</span></label>
                    <div class="col-lg-8">
                       
                         <input type="text" name="state_id" id="state_id" class="form-control state_id" list="stateDropdown" onchange="PoundShopApp.commonClass.getCityList(this)"  value="{{ $state_name }}" autocomplete="off">
                          @if(!empty($id))
                            @php
                               $select_state_id =  !empty(old('state_id')) ? old('state_id') : $result->state_id;
                            @endphp
                         <datalist id="stateDropdown" >
                            @if(!empty($country_states[$sel_country_id]))
                                @foreach($country_states[$sel_country_id] as $states)
                                    <option value="{{$states['name']}}" {{($select_state_id == $states['id']) ? 'selected = "selected"' : "" }}> {{$states['name']}} </option>
                                @endforeach
                            @endif
                         </datalist>
                         @else
                          @php
                               $select_state_id = !empty($result->state)?$result->state:'';
                            @endphp
                          <datalist id="stateDropdown" >
                            @if(!empty($country_states[$sel_country_id]))
                                @foreach($country_states[$sel_country_id] as $states)
                                    <option value="{{$states['name']}}" > {{$states['name']}} </option>
                                @endforeach
                            @endif
                         </datalist>
                         @endif
                      
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.city")<span class="asterisk">*</span></label>
                    <div class="col-lg-8">
                       <input type="text" name="city_id" id="city_id" class="form-control city_id" list="cityDropdown"  value="{{ $city_name }}" autocomplete="off">
                            <datalist id="cityDropdown" >
                                @if(!empty($id))
                                @php
                                   $select_city_id =  !empty(old('city_id')) ? old('city_id') : $result->city_id;
                                @endphp
                               @if(!empty($state_cities[$select_state_id]))
                                    @foreach($state_cities[$select_state_id] as $city)
                                        <option value="{{$city['name']}}" {{($select_city_id == $city['id']) ? 'selected = "selected"' : "" }}> {{$city['name']}} </option>
                                    @endforeach
                                @endif
                            @else
                            
                               @if(!empty($state_cities[$select_state_id]))
                                    @foreach($state_cities[$select_state_id] as $city)
                                        <option value="{{$city['name']}}"> {{$city['name']}} </option>
                                    @endforeach
                                @endif
                            @endif
                             </datalist>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.zipcode')</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" placeholder="" name="zipcode" value="{{!empty(old('zipcode')) ? old('zipcode') : @$result->zipcode}}" id="zipcode">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.date_relationship_started')</label>
                    <div class="col-lg-8">
                        @php
                        $sel_date_rel_start = !empty(old('date_rel_start')) ? old('date_rel_start') : @$result->date_rel_start;
                        @endphp
                        <input type="text" class="form-control datepicker" placeholder="" name="date_rel_start" value="{{!empty($sel_date_rel_start) ? system_date($sel_date_rel_start) : system_date()}}" readonly="">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.date_created')</label>
                    <div class="col-lg-8">
                        @php
                        $sel_created_at = !empty(old('created_at')) ? old('created_at') : @$result->created_at;
                        @endphp
                        <input type="text" class="form-control" placeholder="" name="created_at" readonly="" value="{{!empty($sel_created_at) ? system_date($sel_created_at) : system_date()}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">@lang('messages.supplier.comment')</label>
                    <div class="col-lg-8">
                        <textarea class="form-control"  placeholder="" name="comment">{{!empty(old('comment')) ? old('comment') : @$result->comment}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>    