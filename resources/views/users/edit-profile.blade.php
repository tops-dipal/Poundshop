@extends('layouts.app')
@section('content')
@section('title',__("messages.user_management.user_profile"))
<style>
table, td, th {  
  border: 1px solid #ddd;
  text-align: left;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 15px;
}
</style>
<div class="content-card custom-scroll">
    
   <div class="content-card-header">
    <h3 class="page-title"> @lang("messages.user_management.edit_profile")</h3>     
     <div class="right-items">
       <a href="{{route('users.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
        <button class="btn btn-blue btn-header px-4" form="edit-user-form"  title="@lang('messages.modules.button_save')">@lang('messages.modules.button_save')</button>
    </div>                   
    </div>  
    <div class="card-flex-container">
        <form class="form-horizontal form-flex" method="post" id="edit-user-form" action="{{route('api-users.update',$user->id)}}" enctype="multipart/form-data">
            <div class="form-fields">
                <div class="container-fluid">

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                           
                                 @method('PUT')
                                <input type="hidden" name="id" value="{{$user->id}}" />

                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.first_name")<span class="asterisk">*</span></label>
                                                    <div class="col-lg-8">
                                                        <input type="text" class="form-control" id="first_name" placeholder="" name="first_name" value="{{ $user->first_name }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.last_name")<span class="asterisk">*</span></label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="last_name" placeholder="" name="last_name" value="{{ $user->last_name }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.email")<span class="asterisk">*</span></label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="email" placeholder="" name="email" value="{{ $user->email }}"> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.gender")</label>
                                                    <div class="col-lg-8 mt-2">
                                                        <label class="fancy-radio mr-3">
                                                            <input type="radio" name="gender" class="" value="1" @if($user->gender=="1")  checked="checked" @endif ><span><i></i>Male</span>
                                                        </label>
                                                        <label class="fancy-radio">
                                                            <input type="radio" name="gender" class="" value="2" @if($user->gender=="2")  checked='checked' @endif > 
                                                            <span><i></i>Female</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                             
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.contact_number")</label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="phone_no" placeholder="" name="phone_no" value="{{ $user->phone_no }}">
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.mobile_no")</label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="mobile_no" placeholder="" name="mobile_no" value="{{ $user->mobile_no }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.emergency_con_no")</label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="emergency_contact_num" placeholder="" name="emergency_contact_num" value="{{ $user->emergency_contact_num }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.emergency_con_name")</label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="emergency_contact_name" placeholder="" name="emergency_contact_name"  value="{{ $user->emergency_contact_name }}">
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
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.address1")</label>
                                                    <div class="col-lg-8">
                                                        <input  type="text" class="form-control" id="address_line1" placeholder="" name="address_line1" value="{{ $user->address_line1 }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.address2")</label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="address_line2" placeholder="" name="address_line2" value="{{ $user->address_line2 }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.country")</label>
                                                    <div class="col-lg-8">
                                                       <select class="form-control country_id" name="country_id" onchange="PoundShopApp.commonClass.getStateList(this)">
                                                            @php
                                                                $sel_country_id = !empty($user->country_id) ? $user->country_id : '230';
                                                            @endphp

                                                            <option value="">Select @lang('messages.supplier.country')</option>
                                                            @foreach($countries as $country)
                                                                <option value="{{$country->id}}" {{($sel_country_id == $country->id) ? 'selected="selected"': '' }}>{{$country->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.state")</label>
                                        <div class="col-lg-8">
                                           <!--  <select name="state_id" id="state_id" class="form-control" onchange="getCityStateWise()">
                                                <option value="0">---@lang("messages.user_management.select") @lang("messages.user_management.state")---</option>
                                            </select> -->
                                             <input type="text" name="state_id" id="state_id" class="form-control state_id" list="stateDropdown" onchange="PoundShopApp.commonClass.getCityList(this)"  value="{{ $state_name }}" autocomplete="off">
                                             @php
                                                   $select_state_id =  !empty(old('state_id')) ? old('state_id') : @$user->state_id;
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
                                                   $select_city_id =  !empty(old('city_id')) ? old('city_id') : @$user->city_id;
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
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.post_code")</label>
                                                    <div class="col-lg-8">
                                                      <input type="text" class="form-control" id="zipcode" placeholder="" name="zipcode" value="{{ $user->zipcode }}">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.image")</label>
                                                    <div class="col-lg-8">
                                                      <div class="fancy-file">
                                                            <input type="file" name="profile_pic" id="profile_pic" class="inputfile-custom" accept="image/*" />
                                                            <label for="profile_pic"><span></span> <strong>Choose a file</strong></label>
                                                        </div>
                                                      <span class="imageError invalid-feedback"></span>
                                                       <div class="imageDiv"> 
                                                           @if(!is_null($user->getOriginal('profile_pic')))
                                                          <a href="{{ $user->profile_pic }}" data-rel="lightcase">
                                                          <img src="{{$user->profile_pic }}" alt="Smiley face" width="100">
                                                            </a>
                                                          <button class="btn btn-danger remove-img" type="button" onclick="removeImage('{{ $user->id }}')">X</button>
                                                      @endif
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.common.attachments")</label>
                                                    <div class="col-lg-8">
                                                        <div class="fancy-file">
                                                            <input type="file" name="attachments[]" id="attachments" class="inputfile-custom" multiple="" data-multiple-caption="{count} files selected" accept="application/pdf,image/*" />
                                                            <label for="attachments"><span></span> <strong>@lang("messages.common.choose_files")</strong></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.supplier.comment")</label>
                                                    <div class="col-lg-8">
                                                       <textarea name="comment" class="form-control">{{ $user->comment }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 attachment_div">
                                        @if(!empty($attachments))
                                            <div class="form-group row">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                        
                                                        <th>@lang("messages.common.attached_files")</th>
                                                        <th>Action</th>
                                                        <tr>
                                                    </thead>
                                                    <tbody>
                                                       
                                                            @forelse($attachments as $ak=>$av)
                                                            <tr id="tr_{{ $av->id }}">
                                                                @php
                                                                    $fileArr=explode("/",$av->attachment);
                                                                    $filename=$fileArr[2];
                                                                @endphp
                                                                
                                                                <td><a href="{{ asset('storage/uploads/'.$av->attachment) }}">{{ $filename }}</a></td>
                                                                <td>
                                                                    <ul class="action-btns">
                                                                        <li>
                                                                        <a href="javascript(0);" id="{{ $av->id }}" class="btn btn-delete"><span class="icon-moon icon-Delete"></span></a>

                                                                    </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            
                                                            @empty
                                                            <tr>
                                                                <td colspan="2">No Record Found</td>
                                                            </tr>
                                                            @endforelse
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        
                                        @endif
                                    </div>
                                            <div class="col-lg-12">
                                                <div class="form-group row">
                                                    <div class="col-lg-8">
                                                         <label class="fancy-checkbox">
                                                            <input type="checkbox"  class="" name="check_to_change_password" id="enable_chage_password" onchange="enableChangePassword()" />
                                                            <span><i></i>@lang("messages.user_management.check_to_change_pwd")</span>
                                                        </label>
                                                    </div>
                                                    <!-- <input type="checkbox" class="" name="check_to_change_password" id="enable_chage_password" onchange="enableChangePassword()"> @lang("messages.user_management.check_to_change_pwd") -->
                                                </div>
                                            </div>
                                            <div class="col-lg-6 check_to_update_password">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.new") @lang("messages.user_management.pwd")</label>
                                                    <div class="col-lg-8">
                                                      <input type="password" class="form-control" id="password" placeholder="" name="password" autocomplete="false">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 check_to_update_password">
                                                <div class="form-group row">
                                                    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.confirm") @lang("messages.user_management.new") @lang("messages.user_management.pwd")</label>
                                                    <div class="col-lg-8">
                                                      <input type="password" class="form-control" id="c_password" placeholder="" name="c_password">
                                                    </div>
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
   
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCqLRWPCHXYGR_xA8CvkeinflInRCwrwpQ"></script>

<script type="text/javascript" src="{{asset('js/users/edit.js?v='.CSS_JS_VERSION)}}"></script>
@endsection