@extends('layouts.app')
@section('content')
@if($process=='add')
@section('title',__('messages.commodity_code_master.add_commodity_code'))
@else
@section('title',__('messages.commodity_code_master.edit_commodity_code'))
@endif
<div class="content-card custom-scroll">
   
    <div class="content-card-header">
        <h3 class="page-title">@if($process=='add') @lang('messages.commodity_code_master.add_commodity_code') @else @lang('messages.commodity_code_master.edit_commodity_code')@endif</h3>		
        <div class="right-items">
            <a href="{{route('commodity-codes.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4"  title="@lang('messages.modules.button_save')" form="code-form">@lang('messages.modules.button_save')</button>
        </div>					
    </div>	
    <div class="card-flex-container">
        @if($process=='add')
            <form class="form-horizontal form-flex" method="post" id="code-form" action="{{route('api-commodity-codes.store')}}">   
        @else
            <form class="form-horizontal form-flex" method="post" id="code-form" action="{{route('api-commodity-codes.update',$code->id)}}">
            @method('PUT')    
                <input type="hidden" name="id" value="{{$code->id}}" />
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
                                            <input type="text" class="form-control" id="code" placeholder="" name="code" value="{{ $code->code }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.commodity_code_master.commodity_code_desc')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                              <input type="text" class="form-control"  id="" placeholder="" name="desc" id="desc"value="{{ $code->desc }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.commodity_code_master.is_default')</label>
                                        <div class="col-lg-8">

                                            <label class="fancy-radio">
                                                <input type="radio" name="is_default" value="1" @if($code->is_default=="1") checked="checked" @endif/>
                                                <span><i></i>Yes</span>
                                            </label>
                                            <label class="fancy-radio">
                                                <input type="radio" name="is_default" value="0" @if($code->is_default=="0") checked="checked" @endif  @if($process=='add') checked="checked" @endif/>
                                                <span><i></i>No</span>
                                            </label>
                                        
                                    
                                        </div>
                                    </div>
                                </div>

                                 

                                 <!-- <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.commodity_code_master.import_duty')(%)<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="" placeholder="" name="import_duty" value="{{ $code->import_duty }}">
                                        </div>
                                    </div>
                                </div> -->
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
<script type="text/javascript" src="{{asset('js/commodity-codes/form.js?v='.CSS_JS_VERSION)}}"></script>
@endsection