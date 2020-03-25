@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.slot_master'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.modules.slot_master')</h3>
        <div class="center-items">
            
        </div>
        <div class="right-items">  
             <button class="btn btn-blue btn-header px-4" form="create-slot-form" title="@lang('messages.modules.button_save')">@lang('messages.modules.add_slot')</button>
        </div>
    </div>
    <div class="card-flex-container d-flex">					    
		<div class="d-flex-xs-block">
			<div class="col-lg-12 form" >
                <form action="{{route('api-slot.store')}}" method="post" class="form-horizontal form-flex" id="create-slot-form">
                    <div class="add-category-form">                       
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        @php
                        $i=1;
                        @endphp
                        @if(count($slots)>0)
                        <input type="hidden" name="slot_num" value="{{ count($slots) }}">
                        @endif
                        @forelse($slots as $slotKey=>$slotVal)
                        <div class="mt-2 slot_{{$i}}">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-2 col-form-label">@lang('messages.modules.slot') {{$i}}</label>
                                <div class="col-lg-6">
                                    <div class="d-flex" id="slotGroup_{{$i}}">
                                        <input type="hidden" name="from_time[]" value="{{ $slotVal->from }}">
                                        <input type="hidden" name="to_time[]" value="{{ $slotVal->to }}">
                                        <input type="text" name="from[{{$i}}]" value="{{ $slotVal->from }}" class="form-control mr-2" placeholder="From" disabled="">
                                        <input type="text" name="to[{{$i}}]" value="{{ $slotVal->to }}" class="form-control" placeholder="To"  disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <a class="btn-delete bg-light-red" href="javascript:void(0);" id="slot_{{$i}}" attr-val="{{ $slotVal->id }}"><span class="icon-moon icon-Delete"></span></a>
                                </div>
                            </div>
                        </div>
                        @php $i++;  @endphp
                        @empty
                         <input type="hidden" name="slot_num" value="1">
                         <div class="mt-2 slot_1">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-2 col-form-label">@lang('messages.modules.slot') 1</label>
                                <div class="col-lg-6">
                                    <div class="d-flex" id="slotGroup_1">
                                        <input type="text" name="from[1]" value="" class="form-control timepicker mr-2" placeholder="From">
                                        <input type="text" name="to[1]" value="" class="form-control timepicker" placeholder="To">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                  <a class="btn-delete bg-light-red" href="javascript:void(0);" id="slot_1"><span class="icon-moon icon-Delete"></span></a>
                                </div>
                                
                            </div>
                        </div>
                        @endforelse
                       	<div id="add_more_slote">
                        </div>                        
                                                  
                        <div class="col-lg-6 offset-lg-2 text-center">
                            <input type="hidden" name="add-more-cat-url" value="{{ route('add-more-cat') }}" id="addMoreCatURL">
                            <button type="button" class="btn btn-success addMore">@lang('messages.common.add_more')</button>
                        </div>                                
                                                                   
                     
                    </div>
                </form>
            </div>			
		</div>
	</div>
</div>
@endsection
@section('script')

<script type="text/javascript" src="{{asset('js/slot/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection