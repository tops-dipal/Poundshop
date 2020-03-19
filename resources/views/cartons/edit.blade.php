@extends('layouts.app')
@section('content')
@section('title',__('messages.box_master.edit_box'))
<div class="content-card custom-scroll">
   
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.box_master.edit_box')</h3>	
     	
        <div class="right-items">
            <a href="{{route('cartons.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4" title="@lang('messages.modules.button_save')" form="create-carton-form">@lang('messages.modules.button_save')</button>
        </div>					
    </div>	
    <div class="card-flex-container">
        <form class="form-horizontal form-flex" method="post" id="create-carton-form" action="{{route('api-cartons.update',$carton->id)}}" >
            @method('PUT')
            <div class="form-fields">
                <input type="hidden" name="id" value="{{$carton->id}}" />           
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.box_master.box_name')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="" placeholder="" name="name" value="{{$carton->name}}"> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.length')<small> (@lang('messages.totes.cm'))</small><span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="length" placeholder="" name="length" value="{{$carton->length}}" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.width')<small> (@lang('messages.totes.cm'))</small><span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="width" placeholder="" name="width" value="{{$carton->width}}" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.stck_height')<small> (@lang('messages.totes.cm'))</small><span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="height" placeholder="" name="height" value="{{$carton->height}}" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.max_vol')<small> (@lang('messages.totes.meter3'))</small></label>
                                        <div class="col-lg-8">
                                            <input readonly="readonly" disabled="disabled" type="text" class="form-control" id="max_volume" placeholder="" name="max_volume" value="{{$carton->max_volume}}" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.max_weight')<small> (@lang('messages.totes.kg'))</small><span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="" placeholder="" name="max_weight_carry" value="{{$carton->max_weight}}" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.qty')<span class="asterisk">*</span></label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="qty" placeholder="" name="qty" value="{{$carton->quantity}}">
                                        </div>
                                    </div>
                                </div>
                                
                                {{--<div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.common.barcode')</label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="qty" placeholder="" name="barcode" value="{{ $carton->barcode }}">
                                        </div>
                                    </div>
                                </div>--}}
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.box_master.cost')</label>
                                        <div class="col-lg-8">
                                          <input type="text" class="form-control" id="cost" placeholder="" name="cost" value="{{ $carton->cost }}" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.box_master.recycle_box')</label>
                                        <div class="col-lg-8">
                                            <label class="fancy-radio">
                                                <input type="radio" name="is_recycled" value="1"  @if($carton->recycle_carton ==1) checked="checked" @endif/>
                                                <span><i></i>Yes</span>
                                            </label>
                                            <label class="fancy-radio">
                                                <input type="radio" name="is_recycled" value="0"  @if($carton->recycle_carton ==0) checked="checked" @endif/>
                                                <span><i></i>No</span>
                                            </label>
                                           
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
<script type="text/javascript" src="{{asset('js/cartons/edit.js?v='.CSS_JS_VERSION)}}"></script>
@endsection