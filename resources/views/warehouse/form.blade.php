@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <form class="form-horizontal form-flex" method="post" id="create-warehouse-form" action="{{route('api-warehouse.store')}}">            
        <div class="content-card-header">
            <h3 class="page-title">{{$page_title}}</h3>		
            <div class="right-items"> 
                <a href="{{route('warehouse.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
                <button class="btn btn-blue btn-header px-4" title="@lang('messages.modules.button_save')">@lang('messages.modules.button_save')</button>                           
            </div>					
        </div>    
        <div class="card-flex-container">            
            <input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-2 col-form-label">@lang('messages.table_label.warehouse_name')<span class="asterisk">*</span></label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control" id="name" placeholder="" name="name" value="{{!empty($result->name) ? $result->name : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.contact_person')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="contact_person" placeholder="" name="contact_person" value="{{!empty($result->contact_person) ? $result->contact_person : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.phone_no')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="phone_no" placeholder="" name="phone_no" maxlength="13" value="{{!empty($result->phone_no) ? $result->phone_no : '' }}">
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
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.address_line_1')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="address_line1" placeholder="" name="address_line1" value="{{!empty($result->address_line1) ? $result->address_line1 : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.address_line_2')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="address_line2" placeholder="" name="address_line2" value="{{!empty($result->address_line2) ? $result->address_line2 : '' }}"> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.country')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-control country_id" id="country" name="country" onchange="PoundShopApp.commonClass.getStateList(this)">
                                                @php
                                                    $sel_country_id = !empty($result->country)?$result->country:'230';
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
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.state')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-control" name="state" {{empty($country_states[$sel_country_id]) ? 'disabled' : "" }} id="stateDropdown" onchange="PoundShopApp.commonClass.getCity(this);">

                                                @php
                                                print_r($$result);exit;
                                                   $select_state_id = !empty($result)? $result->state:'';
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
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.city')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-control" name="city" {{empty($state_cities[$select_state_id]) ? "disabled" : ""}}  id="cityDropdown">
                                                @php
                                                   $select_city_id = !empty($result->city)?$result->city:'';
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
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.state")</label>
                                        <div class="col-lg-8">
                                           
                                             <input type="text" name="state_id" id="state_id" class="form-control state_id" list="stateDropdown" onchange="PoundShopApp.commonClass.getCityList(this)"  value="{{ $state_name }}" autocomplete="off">
                                             @php
                                                   $select_state_id =  !empty( $result) ? $result->state : '';
                                                @endphp
                                             <datalist id="stateDropdown" >
                                                @if(!empty($country_states[$sel_country_id]))
                                                    @foreach($country_states[$sel_country_id] as $states)
                                                        <option value="{{$states['name']}}" > {{$states['name']}} </option>
                                                    @endforeach
                                                @endif
                                             </datalist>
                                          
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.city")</label>
                                        <div class="col-lg-8">
                                           <input type="text" name="city_id" id="city_id" class="form-control city_id" list="cityDropdown"  value="{{ $city_name }}" autocomplete="off">
                                                <datalist id="cityDropdown" >
                                                    @php
                                                   $select_city_id =  !empty($result) ? $result->city : '';
                                                @endphp
                                                   @if(!empty($state_cities[$select_state_id]))
                                                        @foreach($state_cities[$select_state_id] as $city)
                                                            <option value="{{$city['name']}}" > {{$city['name']}} </option>
                                                        @endforeach
                                                    @endif
                                                 </datalist>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.zipcode')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="zipcode" placeholder="" name="zipcode" maxlength="8" value="{{!empty($result->zipcode) ? $result->zipcode : '' }}">
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $warehouse_type = !empty($result->type) ? $result->type : '';
                                @endphp
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.warehouse_type')</label>
                                        <div class="col-lg-8 mt-2">
                                            <label class="fancy-radio mr-3">
                                                <input type="radio" id="type" name="type" value="1" checked="checked" >
                                                <span><i></i>@lang('messages.table_label.ware_type_wh')</span>                                                
                                            </label>
                                            <label class="fancy-radio mr-3">
                                                <input type="radio" name="type" value="2"  {{($warehouse_type == '2') ? 'checked="checked"' : "" }}>
                                                <span><i></i>@lang('messages.table_label.ware_type_off')</span>                                               
                                            </label>
                                            <!-- <label class="fancy-radio mr-3">
                                                <input type="radio" name="type" value="3" {{($warehouse_type == '3') ? 'checked="checked"' : "" }}> 
                                                <span><i></i>@lang('messages.table_label.ware_type_hq')</span>
                                            </label> -->
                                            <label class="fancy-radio">
                                                <input type="radio" name="type" value="4" {{($warehouse_type == '4') ? 'checked="checked"' : "" }}>
                                                <span><i></i>@lang('messages.table_label.ware_type_shop')</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $warehouse_is_default = !empty($result->is_default) ? $result->is_default : '';
                                @endphp
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.is_default')</label>
                                        <div class="col-lg-8 mt-2">
                                            <label class="fancy-radio mr-3">
                                                <input type="radio" name="is_default" value="0" checked="checked" >
                                                <span><i></i>@lang('messages.table_label.default_no')</span>                                                
                                            </label>
                                            <label class="fancy-radio mr-3">
                                                <input type="radio" name="is_default" value="1"  {{($warehouse_is_default == '1') ? 'checked="checked"' : "" }}>
                                                <span><i></i>@lang('messages.table_label.default_yes')</span>                                               
                                            </label>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($location_type))
                            <div class="row">
                                <div class="col-lg-12">
                                    <h3 class="p-title mb-4 mt-4">@lang('messages.user_management.set_ware_loc_prefix')</h3>                                
                                </div>
                            </div>
                            <div class="row">
                                @foreach($location_type as $key=>$row)
                                @php
                                $value_exist=0;
                                $value=isset($warehouse_location_trans_data[$key])?$warehouse_location_trans_data[$key]:'';
                                if(!empty($value))
                                {
                                    $value_exist=1;
                                }

                                if($key==7)
                                {
                                    $row='Aerosol';
                                }
                                else if($key==6)
                                {
                                    $row='Dropshipping';
                                }
                                else if($key==12)
                                {
                                    $row='Aerosol Cage Bulk';
                                }
                                else if($key==8)
                                {
                                    $row='Quarantine';
                                }

                                @endphp

                                    <div class="col-lg-6">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">{{$row}} @lang('messages.table_label.prefix')<span class="asterisk">*</span></label>
                                            <div class="col-lg-2">
                                                <input type="text"  class="form-control" id="loc_prefix_{{$key}}" placeholder="" name="loc_prefix_{{$key}}" value="{{ $value }}" required="" maxlength="5" @php if(!empty($value_exist)){ echo "readonly";} @endphp>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-12 col-form-label">@lang('messages.purchase_order.form.notes')</label>
                                        <div class="col-lg-12">
                                           <textarea class="form-control" placeholder="@lang('messages.purchase_order.form.notes')" name="notes">{{ !empty($result->notes) ? $result->notes : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>						
                </div>
            </div>
            <!-- <div class="content-card-footer">
                <div class="button-container">
                    
                </div>
            </div> -->            
        </div>   
    </form>
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCqLRWPCHXYGR_xA8CvkeinflInRCwrwpQ"></script>

<script type="text/javascript" src="{{asset('js/warehouse/form.js?v='.CSS_JS_VERSION)}}"></script>
@endsection