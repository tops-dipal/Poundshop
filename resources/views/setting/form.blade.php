@extends('layouts.app')
@section('content')

@section('title',__('messages.settings.settings'))

<div class="content-card custom-scroll">
    <form name="setting-form" method="post" action="{{ route('api-setting.store') }}" id="setting-form" class="form-horizontal form-flex">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.settings.settings') > @lang('messages.settings.vat_rates')</h3>		
        <div class="right-items">
            <a href="{{route('user-dashboard')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4"  title="@lang('messages.modules.button_save')">@lang('messages.modules.button_save')</button>
        </div>					
    </div>	
    <div class="card-flex-container">
            
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            
                          
                                    <div class="row">
                                        @forelse($fields as $colKey=>$colVal)
                                         <div class="col-lg-6">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-4 col-form-label">{{ $colVal->column_name }}<span class="asterisk">*</span></label>
                                                <div class="col-lg-8">
                                                    <input type="text" class="form-control w-50-block" id="{{ $colVal->column_key }}" placeholder="" name="{{ $colVal->column_key }}" value="{{ $colVal->column_val }}" required="" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        @endforelse
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
<script type="text/javascript" src="{{asset('js/setting/form.js?v='.CSS_JS_VERSION)}}"></script>
@endsection