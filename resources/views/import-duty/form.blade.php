@extends('layouts.app')
@section('content')
@if($process=='add')
@section('title',__('messages.import_duty_master.add_duty'))
@else
@section('title',__('messages.import_duty_master.add_duty'))
@endif
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@if($process=='add') @lang('messages.import_duty_master.add_duty') @else @lang('messages.import_duty_master.edit_duty')@endif</h3>		
        <div class="right-items">
             <a href="{{route('import-duty.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4"  title="@lang('messages.modules.button_save')" form="duty-form">@lang('messages.modules.button_save')</button>
        </div>					
    </div>	
    <div class="card-flex-container">
        @if($process=='add')
            <form class="form-horizontal form-flex" method="post" id="duty-form" action="{{route('api-import-duty.store')}}">   
        @else
            <form class="form-horizontal form-flex" method="post" id="duty-form" action="{{route('api-import-duty.update',$result->id)}}">
            @method('PUT')    
            <input type="hidden" name="id" value="{{$result->id}}" />
        @endif    
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.commodity_code_master.commodity_code')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <select name="commodity_code_id" class="form-control" onchange="getDescForCode(this.value)" id="commodity_code_id">
                                                <option value="">---Select Code---</option>
                                                @forelse($codes as $codeKey=>$codeVal)
                                                    <option value="{{ $codeVal->id }}" attr-val='{{$codeVal->desc}}'  @if($codeVal->id==$result->commodity_code_id) selected="selected" @endif >{{ $codeVal->code }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 desc" style="display: none;">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.commodity_code_master.commodity_code_desc')</label>
                                        <div class="col-lg-8">
                                            <textarea class="form-control" disabled="" id="cc_desc"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.import_duty_master.rate_per') (%)<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                           
                                            <input type="text"  id="rate" placeholder="" name="rate" value="{{ $result->rate }}" class="form-control" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.user_management.country")<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                              <select class="form-control" name="country_id" id="country_id">
                                                @php
                                                    $sel_country_id = !empty(old('country_id')) ? old('country_id') : @$result->country_id;
                                                @endphp

                                                <option value="">---Select @lang('messages.supplier.country')---</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}" {{($sel_country_id == $country->id) ? 'selected="selected"': '' }}>{{$country->name}}</option>
                                                @endforeach
                                            </select>
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
<script type="text/javascript" src="{{asset('js/import-duty/form.js?v='.CSS_JS_VERSION)}}"></script>
@endsection