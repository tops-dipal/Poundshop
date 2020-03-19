@extends('layouts.app')
@section('content')

@section('title',__("messages.user_management.user_edit"))
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
            <h3 class="page-title">Edit User</h3> 
            <div class="center-items">
                
                <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                      
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#general" role="tab" aria-controls="general">
                            
                             @lang("messages.user_management.user_info")
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="statistics-tab" data-toggle="tab" href="#statistics" role="tab" aria-controls="statistics" aria-selected="false">
                             @lang("messages.user_management.statistics")
                        </a>
                    </li>
                </ul>
            </div>    
            <div class="right-items">
               <a href="{{route('users.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
                <button class="btn btn-blue btn-header px-4" form="edit-user-form"  title="@lang('messages.modules.button_save')">@lang('messages.modules.button_save')</button>
            </div>                  
        </div>  
        <div class="card-flex-container">
            <div class="container-fluid">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <form method="post" id="edit-user-form" action="{{route('api-users.update',$user->id)}}" enctype="multipart/form-data">
                             @method('PUT')
                            <input type="hidden" name="id" value="{{$user->id}}" />
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
                                {{--<div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.state")</label>
                                        <div class="col-lg-8">
                                          

                                            <select class="form-control state_id" name="state_id" {{empty($country_states[$sel_country_id]) ? 'disabled' : "" }} id="stateDropdown" onchange="PoundShopApp.commonClass.getCity(this)">

                                                @php
                                                   $select_state_id =  !empty(old('state_id')) ? old('state_id') : @$user->state_id;
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
                                </div>--}}
                                 {{--<div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.city")</label>
                                        <div class="col-lg-8">
                                           <select class="form-control city_id" name="city_id" {{empty($state_cities[$select_state_id]) ? "disabled" : ""}}  id="cityDropdown" >
                                                @php
                                                   $select_city_id =  !empty(old('city_id')) ? old('city_id') : @$user->city_id;
                                                @endphp
                                                   

                                                <option value="">Select @lang('messages.supplier.city')</option>
                                                 @if(!empty($state_cities[$select_state_id]))
                                                    @foreach($state_cities[$select_state_id] as $city)
                                                        <option value="{{$city['name']}}" > {{$city['name']}} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>--}}
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
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.user_role")</label>
                                        <div class="col-lg-8">
                                            <select name="user_role" class="form-control">
                                               
                                                @forelse($roles as $roleKey=>$roleVal)
                                                    <option value="{{ $roleVal->id }}" @if(in_array($roleVal->name,json_decode(json_encode($user->getRoleNames())))) selected="selected" @endif>{{ $roleVal->name }} </option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-lg-6">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.site")</label>
                                            <div class="col-lg-8">
                                             <select name="site_id" class="form-control">
                                                <option>@lang("messages.table_label.select_site")</option>
                                                 @forelse($sites as $siteKey=>$siteVal)
                                                    <option value="{{ $siteVal->id }}"  @if($user->site_id==$siteVal->id)  selected="selected" @endif >{{ $siteVal->name }}</option>
                                                 @empty
                                                 @endforelse
                                             </select>
                                            </div>
                                        </div>
                                    </div>
                                 <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.pwd")</label>
                                        <div class="col-lg-8">
                                          <input type="password" class="form-control" id="password" placeholder="" name="password">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.confirm_pwd")</label>
                                        <div class="col-lg-8">
                                          <input type="password" class="form-control" id="c_password" placeholder="" name="c_password">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.image")</label>
                                        <div class="col-lg-8">
                                          <div class="fancy-file">
                                                <input type="file" name="profile_pic" id="profile_pic" class="inputfile-custom" accept="image/*"/>
                                                <label for="profile_pic"><span></span> <strong>Choose a file</strong></label>
                                            </div>
                                          <span class="imageError invalid-feedback"></span>
                                          <div class="imageDiv"> 
                                              @if(!is_null($user->getOriginal('profile_pic')))
                                              <img src="{{$user->profile_pic }}" alt="Smiley face" height="100" width="100">
                                              <button class="btn btn-danger remove-img" type="button" onclick="removeImage('{{ $user->id }}')">X</button>
                                          @endif
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.active_sys_user")</label>
                                       
                                        <div class="col-lg-8 mt-2">
                                            <label class="fancy-radio mr-3">
                                                <input type="radio" name="status" value="1" @if($user->status=="1")  checked="checked" @endif ><span><i></i> Yes </span>
                                            </label>
                                        
                                        
                                            <label class="fancy-radio">
                                                <input type="radio" name="status" value="2" @if($user->status=="2")  checked="checked" @endif ><span><i></i>No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.date_pwd_changed")</label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="date_pass_change" placeholder="" name="date_pass_change" value="{{ $user->date_pass_change }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.date_enroll")</label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control datepicker" id="date_enroll" placeholder="" name="date_enroll" value="{{ date('d-M-Y',strtotime($user->date_enroll))}}">
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
                                    <div class="col-lg-6">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.common.attachments")</label>
                                            <div class="col-lg-8">
                                                <div class="fancy-file">
                                                    <input type="file" name="attachments[]" id="attachments" class="inputfile-custom" multiple="" accept="application/pdf,image/*" data-multiple-caption="{count} files selected"/>
                                                    <label for="attachments"><span></span> <strong>@lang("messages.common.choose_files")</strong></label>
                                                </div>
                                                <span class="file_error"></span>
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
                                </div>
                         </form>                
                    </div>
                    <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">   
                        <div class="row">
                            <center><h1>Comming Soon</h1></center>
                        </div>    
                    </div>   
                

                </div>
            </div>
        </div>
       
    </div>
@endsection
@section('script')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCqLRWPCHXYGR_xA8CvkeinflInRCwrwpQ"></script>

<script type="text/javascript" src="{{asset('js/users/edit.js?v='.CSS_JS_VERSION)}}"></script>
@endsection