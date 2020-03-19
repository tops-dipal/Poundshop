@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <form class="form-horizontal form-flex" method="post" id="locations-settings-form" action="{{route('api-locations-setting-save')}}">
        <div class="content-card-header">
            <h3 class="page-title">{{$page_title}}</h3>		
            <div class="right-items"> 
                <a href="{{route('locations.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
                <button class="btn btn-blue btn-header px-4" title="@lang('messages.modules.button_save')">@lang('messages.modules.button_save')</button>               
            </div>					
        </div>	
    
        <div class="card-flex-container">        
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab"> 
                            <input type="hidden" name="id" id="id" value="{{ isset($location_settings->id)?$location_settings->id:''}}">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.locations_master.dist_aisle_rack')<span class="asterisk">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" maxlength="3" class="form-control" id="dist_aisle_rack" placeholder="" name="dist_aisle_rack" value="{{ isset($location_settings->dist_aisle_rack)?$location_settings->dist_aisle_rack:''}}">
                                        </div>
                                    </div>
                                </div>                            
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.locations_master.avg_walk_speed')<span class="asterisk">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="walk_speed" maxlength="3" placeholder="" name="walk_speed" value="{{ isset($location_settings->walk_speed)?$location_settings->walk_speed:''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.locations_master.add_time_multi_pic_item')<span class="asterisk">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="time_multipick" maxlength="3" placeholder="" name="time_multipick" value="{{ isset($location_settings->time_multipick)?$location_settings->time_multipick:''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.locations_master.time_pic_one')<span class="asterisk">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="time_singlepick" maxlength="3" placeholder="" name="time_singlepick" value="{{ isset($location_settings->time_singlepick)?$location_settings->time_singlepick:''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.locations_master.storage_buffer')<span class="asterisk">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" id="storage_buffer" maxlength="3" placeholder="" name="storage_buffer" value="{{ !empty($location_settings->storage_buffer)?$location_settings->storage_buffer:''}}">
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
            </div>          -->
        </div>   
    </form>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/locations/setting.js?v='.CSS_JS_VERSION)}}"></script>
@endsection