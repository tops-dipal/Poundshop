@extends('layouts.app')
@section('content')

@section('title',__('messages.settings.settings'))

<div class="content-card custom-scroll">
    <form name="setting-form" method="post" action="{{ route('api-setting.store') }}" id="setting-form" class="form-horizontal form-flex">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.settings.settings') > @lang('messages.common.general_setting')</h3>		
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
                        @php
                        $billArrtitle=0;
                        $poBookingtitle=0;
                         $othertitle=0;
                        @endphp
                        <div class="row">
                            @forelse($fields as $colKey=>$colVal)
                            @if($colVal->module_name=='billing_address' && $billArrtitle==0)
                            <div class="col-lg-12">
                                <h3 class="p-title mb-3 mt-4">@lang('messages.purchase_order.form.bill_address') - @lang('messages.common.configuration')</h3>
                                @php
                                $billArrtitle++;
                                $titleClass="col-lg-4";
                                $textboxClass="col-lg-8";
                                @endphp
                            </div>
                            @endif
                            @if($colVal->module_name=='po_bookings' && $poBookingtitle==0)
                            <div class="col-lg-12">
                                <h3 class="p-title mb-3 mt-4">@lang('messages.settings.po_booking') - @lang('messages.common.configuration')</h3>
                                @php
                                $billArrtitle++;
                                $titleClass="col-lg-12";
                                $textboxClass="col-lg-2";
                                @endphp
                            </div>
                            @endif
                             @if($colVal->module_name=='others' && $othertitle==0)
                            <div class="col-lg-12">
                                <h3 class="p-title mb-3 mt-4">Others - @lang('messages.common.configuration')</h3>
                                @php
                                $othertitle++;
                                $titleClass="col-lg-12";
                                $textboxClass="col-lg-2";
                                @endphp
                            </div>
                            @endif
                             <div class="col-lg-6">
                                <div class="form-group row">
                                    <label for="inputPassword" class="{{ $titleClass }} col-form-label">{{ $colVal->column_name }}<span class="asterisk">*</span></label>
                                    <div class="{{ $textboxClass }}">
                                        <input type="text" class="form-control" id="{{ $colVal->column_key }}" placeholder="" name="{{ $colVal->column_key }}" value="{{ $colVal->column_val }}" required="">
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