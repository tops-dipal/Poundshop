@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>		
        <div class="right-items"> 
            <a href="{{route('locations.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4" title="@lang('messages.modules.button_save')" form="create-locations-form">@lang('messages.modules.button_save')</button>
                           
        </div>					
    </div>	
    
    <div class="card-flex-container">
        <form class="form-horizontal form-flex" method="post" id="create-locations-form" action="{{route('api-locations.store')}}">        
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.select_site')</label>
                                        <div class="col-lg-6">
                                            <select name="site_id" class="form-control" id="site_id">
                                                @if(!empty($warehouses))
                                                    @foreach($warehouses as $row)
                                                        <option {{($row->is_default == '1') ? 'selected="selected"' : "" }}  value="{{$row->id}}">{{ucfirst($row->name)}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="form-field-title">@lang('messages.table_label.aisle')</h3>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.aisle_you_have')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" maxlength="3" class="form-control w-50-block" id="aisle" placeholder="" name="aisle">
                                                </div>
                                            </div>
                                        </div>                            
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.range_start_from')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control w-50-block" id="aisle_range" maxlength="3" placeholder="" name="aisle_range">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">                                    
                                    <h3 class="form-field-title">@lang('messages.table_label.rack')</h3>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.rack_in_aisle')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" maxlength="3" class="form-control w-50-block" id="rack" placeholder="" name="rack">
                                                </div>
                                            </div>
                                        </div>                            
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.range_start_from')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" maxlength="3" class="form-control w-50-block" id="rack_range" placeholder="" name="rack_range">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="form-field-title">@lang('messages.table_label.floor')</h3>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.floor_in_rack')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" maxlength="3" class="form-control w-50-block" id="floor" placeholder="" name="floor">
                                                </div>
                                            </div>
                                        </div>                            
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.range_start_from')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" maxlength="3" class="form-control w-50-block" id="floor_range" placeholder="" name="floor_range">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h3 class="form-field-title">@lang('messages.table_label.box')</h3>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.box_in_floor')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" maxlength="3" class="form-control w-50-block" id="box" placeholder="" name="box">
                                                </div>
                                            </div>
                                        </div>                            
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.table_label.range_start_from')<span class="asterisk">*</span></label>
                                                <div class="col-lg-6">
                                                    <input type="text" maxlength="3" class="form-control w-50-block" id="box_range" placeholder="" name="box_range">
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
            <!-- <div class="content-card-footer">
                <div class="button-container">
                    
                </div>
            </div>          -->
        </form>
    </div>   
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/locations/form.js?v='.CSS_JS_VERSION)}}"></script>
@endsection