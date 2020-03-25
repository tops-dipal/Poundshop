@extends('layouts.app')
@section('content')
@section('title',__('messages.replen_request.assign_aisle'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.replen_request.assign_aisle')</h3>
        <div class="center-items">
            
        </div>
        <div class="right-items">
            <a href="{{route('replen-request.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>  
             <button class="btn btn-blue btn-header px-4" form="assign-aisle-form" title="@lang('messages.modules.button_save')">@lang('messages.replen_request.assign_aisle')</button>
        </div>
    </div>
    <div class="card-flex-container d-flex">                        
        <div class="d-flex-xs-block">
            <div class="container-fluid">
            <div class="form" >
                <form action="{{route('store-assign-aisle')}}" method="post" class="form-horizontal form-flex" id="assign-aisle-form">
                    <div class="row loadFormData">
                        
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-3 col-form-label">@lang('messages.table_label.select_site')<span class="asterisk">*</span></label>
                                <div class="col-lg-3">
                                    <select class="form-control" id="warehouse_id" name="warehouse_id">
                                        @forelse($siteData as $siteKey=>$siteVal)
                                        <option value="{{ $siteVal->id }}" attr-bulk-aisle="{{ $siteVal->bulkAisle }}" attr-assign-aisle-data="{{ $siteVal->assignAisleData }}" attr-site-users="{{ $siteVal->siteUsers }}" attr-priority-data="{{ $siteVal->priorityData }}" {{ ($siteVal->is_default=='1') ? 'selected':'' }}>{{ $siteVal->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                         <div class="col-lg-12 priorityAisle">
                            <div class="form-group row">
                                 <label for="inputPassword" class="col-lg-3 col-form-label">Priority E, 1 and 2 Jobs in  Aisles<span class="asterisk">*</span></label>
                                 <div class="col-lg-9 priority_load">
                                    @forelse($defaultSitePriorityData as $pk=>$pv)
                                    @if(!is_null($pv['aisle']))
                                         <button type="button" class="btn btn-blue mb-1">
                                            Aisle {{ $pv['aisle'] }}  
                                            <span class="badge badge-light aisles-cnt">
                                            {{ $pv['count_product'] }}</span>
                                        </button>
                                    @endif
                                    @empty
                                    @endforelse
                                   <!--  <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button>
                                    <button type="button" class="btn btn-blue mb-1">
                                      Aisle 02 <span class="badge badge-light">4</span>
                                    </button> -->
                                 </div>
                            </div>
                        </div>
                        <div class="col-lg-12 label_title">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-2 col-form-label">Bulk Aisle<span class="asterisk">*</span></label>
                                <label for="inputPassword" class="col-lg-6 col-form-label">Select User<span class="asterisk">*</span></label>
                            </div>

                            
                                @include('replen-request.include-assign-aisle')
                           
                            @if(count($defaultSiteAisleData)>0 || count($defaultSiteUsersData)>0)
                               
                                  <div id="addBtn" class="col-lg-12">

                                        <a class="btn btn-add btn-success btn-header addBtn_1" id="addMoreAisleAssign" href="javascript:void(0);" id="" >Add More</a>
                                  </div>
                                  
                            @endif

                        <!-- <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-lg-4">
                                 <select class="form-control" id="aisle" name="aisle">
                                        @forelse($siteData as $siteKey=>$siteVal)
                                        <option value="{{ $siteVal->id }}">{{ $siteVal->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                 <select class="form-control" id="user_id" name="user_id">
                                        @forelse($siteData as $siteKey=>$siteVal)
                                        <option value="{{ $siteVal->id }}">{{ $siteVal->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div> -->
                    
                    </div>
                </form>
            </div> 
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')

<script type="text/javascript" src="{{asset('js/replen-request/assign-aisle.js?v='.CSS_JS_VERSION)}}"></script>
@endsection