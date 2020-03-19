@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.range_management'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.modules.range_management')</h3>
        @if($process=='edit')
        <div class="right-items">
            <a class="btn btn-add btn-blue" href="{{ route('range.index') }}" title="@lang('messages.range_management.range_add')">
               <span class="icon-moon icon-Add"></span> 
                
            </a>
        </div>
        @endif
    </div>
    <div class="card-flex-container">
        <div class="container-fluid">
            <div class="row range-mgmt">
                 @if($process=='add')
                    @include('range.create')
                @else
                <div class="col-lg-6" style="border-right: 2px solid #e6e6e6;">
                    <form action="{{route('api-range.update',$editRange->id)}}" method="post" class="form-horizontal form-flex" id="create-range-form">
                         <input type="hidden" name="get_child" id="get_child" value="{{ url('get-child-category') }}">
                        <input type="hidden" name="csrf_token" value="{{ csrf_token() }}" class="token">
                        <input type="hidden" name="selected_parent" id="selected_parent" >
                        <input type="hidden" name="id" value="{{$editRange->id}}" />
                        <input type="hidden" name="parentIds" id="parentIds" >
                        @method('PUT')
                         <div class="add-category-form">
                            <h3 class="title">@lang('messages.range_management.range_edit')</h3>
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.range_management.parent_cat')</label>
                                <div class="col-lg-8">
                                    <select class="form-control parent_id" name="parent_id">
                                        <option>--@lang('messages.user_management.select') @lang('messages.range_management.parent_cat')--</option>

                                        @forelse($parent as $parentKey=>$parentVal)
                                            
                                        <option value="{{ $parentVal->id }}"  @if(in_array($parentVal->id,$parentIds) && $editRange->parent_id!=NULL) selected="selected" @endif>{{ $parentVal->category_name }}</option>
                                        @empty
                                        @endforelse
                                       
                                    </select>
                                </div>
                            </div>
                            <div id="child_category">
                            </div> 
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.range_management.cat_name')<span class="asterisk">*</span></label>

                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="" placeholder="" name="category_name" value="{{ $editRange->category_name }}">
                                    <div class="row mt-3">
                                        <div class="col-lg-6">
                                            <label class="fancy-radio">
                                                <input type="radio" name="seasonal_status" value="2" class="seasonal_status" @if($editRange->seasonal_status=="2")  checked="checked" @endif>
                                                <span><i></i>@lang('messages.range_management.non_seasonal')</span>
                                            </label>                                            
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="fancy-radio">
                                                <input type="radio" name="seasonal_status" value="1" class="seasonal_status" @if($editRange->seasonal_status=="1")  checked="checked" @endif>
                                                <span><i></i>@lang('messages.range_management.seasonal')</span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="mt-2 seasonal_show hidden">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">From</label>
                                            <div class="col-lg-8">
                                                <div class="d-flex input-select-group">
                                                    <input type="number" name="seasonal_range_fromdate" value="{{ $editRange->seasonal_range_fromdate }}" class="form-control seasonal_show" min="1" max="310">
                                                    <select name="seasonal_range_frommonth" class="form-control seasonal_show">
                                                        @php
                                                        $monthArr=array("1"=>'Jan',"2"=>'Feb',"3"=>'March',"4"=>'April',"5"=>'May',"6"=>'June',"7"=>'July',"8"=>'August',"9"=>'Sep',"10"=>'Oct',"11"=>'Nov',"12"=>'Dec');
                                                        @endphp
                                                        @foreach($monthArr as $monthKey=>$monthVal)
                                                        <option value="{{ $monthKey }}" @if($editRange->seasonal_range_frommonth==$monthKey)  selected="selected" @endif>{{ $monthVal }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="seasonal_show hidden">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">To</label>
                                            <div class="col-lg-8">
                                                <div class="d-flex input-select-group">
                                                    <input type="number" name="seasonal_range_todate" value="{{ $editRange->seasonal_range_todate }}" class="form-control seasonal_show" min="1" max="31">
                                                    <select name="seasonal_range_tomonth" class="form-control seasonal_show">
                                                        @php
                                                        $monthArr=array("1"=>'Jan',"2"=>'Feb',"3"=>'March',"4"=>'April',"5"=>'May',"6"=>'June',"7"=>'July',"8"=>'August',"9"=>'Sep',"10"=>'Oct',"11"=>'Nov',"12"=>'Dec');
                                                        @endphp
                                                        @foreach($monthArr as $monthKey=>$monthVal)
                                                        <option value="{{ $monthKey }}" @if($editRange->seasonal_range_tomonth==$monthKey)  selected="selected" @endif>{{ $monthVal }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="button-container">
                                <button class="btn btn-blue btn-form" title="@lang('messages.modules.button_save')">@lang('messages.range_management.range_edit')</button>
                                <a href="{{route('range.index')}}" class="btn btn-gray btn-form" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
                                
                            </div>
                        </div>
                      
                    </form>
                </div>
                @endif
                <div class="col-lg-6 range_data">
                    @include('range.range_list')
                </div>           
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/range/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection
