@extends('layouts.app')
@section('content')

@section('title',__('messages.purchase_order.terms'))

<div class="content-card custom-scroll">   
    <div class="content-card-header">
        <h3 class="page-title"> @lang('messages.modules.terms_and_condition')</h3>		
        <div class="right-items">
            <a href="{{route('user-dashboard')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4"  form="terms-form" title="@lang('messages.modules.button_save')">@lang('messages.modules.button_save')</button>
        </div>					
    </div>	
    <div class="card-flex-container">
             @if(isset($data->terms))
            <form name="terms-form" method="post" action="api-setting-update-terms" id="terms-form" class="form-horizontal form-flex">
                <!-- <input name="_method" type="hidden" value="PUT">    -->
                <input type="hidden" name="id" id="id" value="{{$data->terms->id}}" />
                    
            @else
             <form name="terms-form" method="post" action="api-setting-store-terms" id="terms-form" class="form-horizontal form-flex">
            @endif
                    <div class="form-fields">
                        <div class="container-fluid">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                    <div class="row">
                                       
                                         <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label class="col-lg-2 col-form-label">
                                                    @lang('messages.purchase_order.terms') @lang('messages.settings.for_uk_supp')
                                                </label>
                                                <div class="col-lg-10">
                                                    <textarea name="terms_pound_uk" id="terms_pound_uk" class="form-control ckeditor">{{ (isset($data->terms)) ?  $data->terms->terms_pound_uk : ''}}</textarea>
                                                    @if(isset($data->terms))
                                                        <p class="font-12 mt-2">@lang('messages.settings.last_modified') : {{ $data->terms_uk_updated_at }} @lang('messages.settings.by') {{ $data->terms->updated_by }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label class="col-lg-2 col-form-label">
                                                    @lang('messages.purchase_order.terms')
                                                    @lang('messages.settings.from_import')
                                                    (@lang('messages.settings.non_uk_supp'))
                                                </label>
                                                <div class="col-lg-10">
                                                    <textarea name="terms_pound_non_uk" id="terms_pound_non_uk" class="form-control ckeditor">{{ (isset($data->terms)) ? $data->terms->terms_pound_non_uk : ''}}</textarea>
                                                    @if(isset($data->terms))
                                                        <p  class="font-12 mt-2">@lang('messages.settings.last_modified') : {{ $data->terms_import_updated_at }} @lang('messages.settings.by') {{ $data->terms->updated_by }}</p>
                                                    @endif
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
<script type="text/javascript" src="{{asset('js/setting/terms-condition.js?v='.CSS_JS_VERSION)}}"></script>
@endsection