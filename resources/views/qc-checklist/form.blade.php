@extends('layouts.app')
@section('content')
@if($process=='add')
@section('title',__('messages.common.add_qc_checklist'))
@else
@section('title',__('messages.common.edit_qc_checklist'))
@endif
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@if($process=='add') @lang('messages.common.add_qc_checklist') @else @lang('messages.common.edit_qc_checklist')@endif</h3>
        <div class="right-items">
            <a href="{{ route('qc-checklist.index') }}"   class="btn btn-gray btn-header px-4 btn-cancel" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4"  title="@lang('messages.modules.button_save')" form="qc-checklist-form">@lang('messages.modules.button_save')</button>
        </div>
    </div>
    <div class="card-flex-container">
        @if($process=='add')
        <form class="form-horizontal form-flex" method="post" id="qc-checklist-form" action="{{route('api-qc-checklist.store')}}">
            @else
            <form class="form-horizontal form-flex" method="post" id="qc-checklist-form" action="{{route('api-qc-checklist.update',$result->id)}}">
                @method('PUT')
                <input type="hidden" name="remove_points_id[]" id="remove_points_id"/>
                <input type="hidden" name="id" value="{{$result->id}}" />
                @endif
                <div class="form-fields">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-lg-2 col-form-label">@lang('messages.qc.qc_checklist_name')<span class="asterisk">*</span></label>
                                    <div class="col-lg-6">
                                        
                                        <input type="text" name="name" value="{{ $result->name }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            @if($process=='add')
                            <input type="hidden" name="total_points" value="1">
                            <div class="col-lg-12">
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-lg-2 col-form-label">Checklist Points<span class="asterisk">*</span></label>
                                    <div class="col-lg-6">                                        
                                        <input type="text" name="checklist_points[]" class="form-control">
                                    </div>
                                    <div class="col-lg-2">
                                        <a class="btn-delete bg-light-red" href="javascript:void(0);" ><span class="icon-moon icon-Delete" id="point_1"></span></a>
                                    </div>
                                </div>
                            </div>
                            @else
                            @if(!empty($points))
                            <input type="hidden" name="total_points" value="{{ (count($points)>0) ? count($points) : 1 }}">
                            @else
                            <input type="hidden" name="total_points" value="1">
                            @endif
                            <!--  <input type="hidden" name="total_points" value="{{ count($points) }}"> -->
                            @php
                            $i=1;
                            @endphp
                            @forelse($points as $key=>$val)
                            <div class="col-lg-12 point_{{ $i }}">
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-lg-2 col-form-label">Checklist Points<span class="asterisk">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="hidden" name="checklist_pointsId[]" value="{{ $val->id }}">
                                        <input type="text" name="checklist_points[]" class="form-control" value="{{ $val->title }}">
                                    </div>
                                    <div class="col-lg-2">
                                        <a class="btn-delete bg-light-red" href="javascript:void(0);" attr-val="{{ $val->id }}" id="point_{{ $i }}"><span class="icon-moon icon-Delete" ></span></a>
                                        @php
                                        $i++;
                                        @endphp
                                    </div>
                                </div>
                            </div>
                            @empty
                           
                            <div class="col-lg-12 point_1">
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-lg-2 col-form-label">Checklist Points<span class="asterisk">*</span></label>
                                    <div class="col-lg-6">
                                        
                                        <input type="text" name="checklist_points[]" class="form-control">
                                    </div>
                                    <div class="col-lg-2">
                                        <a class="btn-delete bg-light-red" href="javascript:void(0);"  id="point_1"><span class="icon-moon icon-Delete"></span></a>
                                    </div>
                                </div>
                            </div>
                            
                            @endforelse
                            @endif
                            <div id="add_more_points" class="col-lg-12 ">
                            </div>
                            
                            <div class="col-lg-6 offset-lg-2 text-center">
                                <input type="hidden" name="add-more-cat-url" value="{{ route('add-more-cat') }}" id="addMoreCatURL">
                                <button type="button" class="btn btn-success addMore">@lang('messages.common.add_more')</button>
                            </div>
                            
                            
                        </div>
                        
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endsection
    @section('script')
    <script type="text/javascript" src="{{asset('js/qc-checklist/form.js?v='.CSS_JS_VERSION)}}"></script>
    @endsection