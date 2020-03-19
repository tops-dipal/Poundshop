@extends('layouts.app')
@section('title',__('messages.modules.references'))
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
            <h3 class="page-title">@lang('messages.modules.references')</h3>       
            <div class="right-items">
                <a href="{{route('reference.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
                <button class="btn btn-blue btn-header px-4" form="create-reference-form" title="@lang('messages.modules.button_save')">@lang('messages.modules.button_save')</button>
            </div>                  
    </div>     
    <div class="card-flex-container">    
        <form class="form-horizontal form-flex" method="post" id="create-reference-form" action="{{route('api-reference.store')}}">        
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">  
                            <div class="row">
                                <div class="col-lg-8">                         
                                    @for ($i = 1; $i < 4; $i++)
                                        <div class="references-card repeat_{{ $i }}">
                                            <h4 class="title">@lang('messages.references.references') {{ $i }}</h4>
                                            <input type="hidden" name="id[]" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->id:'' }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group row">
                                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.references.supp_name')<span class="asterisk">*</span></label>
                                                        <div class="col-lg-8">
                                                            <input type="text" class="form-control supp_name" name="supp_name[]" id="supp_name_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->supplier_name:'' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group row">
                                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.references.cont_per')<span class="asterisk">*</span></label>
                                                        <div class="col-lg-8">
                                                            <input type="text" class="form-control cont_per" name="cont_per[]" id="cont_per_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->contact_person:'' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group row">
                                                        <label for="inputPassword" class="col-lg-4 col-form-label number">@lang('messages.references.cont_nu')<span class="asterisk">*</span></label>
                                                        <div class="col-lg-8">
                                                            <input type="text" class="form-control number cont_numb" name="cont_numb[]" id="cont_numb_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->contact_no:'' }}" maxlength="12">
                                                        </div>
                                                    </div>
                                                </div>                                
                                                <div class="col-lg-12">
                                                    <div class="form-group row">
                                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.references.cont_email')<span class="asterisk">*</span></label>
                                                        <div class="col-lg-8">
                                                            <input type="email" class="form-control cont_email" name="cont_email[]" id="cont_email_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->contact_email:'' }}">
                                                        </div>
                                                    </div>
                                                </div>                                                              
                                            </div>
                                        </div>
                                    @endfor
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
        </form>          
    </div>   
    
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/reference/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection