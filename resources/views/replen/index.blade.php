@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.replen_job_queue'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.modules.replen_job_queue')</h3>  
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="scan-product-barcode-textbox" name="" placeholder="@lang('messages.common.search_replen')" />
            <span class="refresh"></span>
        </div>  
        <div class="right-items">
            <button id="btnFilter" class="btn btn-filter btn-header"><span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/></button>
            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex" id="location_advance_search">
                    <div class="form-fields">                        
                        <div class="filter-container">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @php
                                        $priorityTypes=priorityTypes();
                                        @endphp
                                        @if(!empty($priorityTypes))
                                            @foreach($priorityTypes as $key => $value)
                                            <label class="fancy-checkbox">
                                                <input class="adv_priority" type="checkbox" name="adv_priority[]" value="{{ $key}}" />
                                                <span><i></i> {{ $value }}</span>
                                            </label>
                                            @endforeach
                                        @endif                                        
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <input type="button" class="btn btn-gray cancle_fil" title="@lang('messages.modules.button_cancel')" value="@lang('messages.modules.button_cancel')">
                        <input type="button" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')">
                    </div>
                </form>
            </div>

            @if(!empty($pallet_pick_location) && !empty($pallet_pick_location->toArray()))
                <a class="btn btn-add btn-danger btn-header finish_job" href="javascript:void(0);" title="@lang('messages.replen.finish')">@lang('messages.replen.finish')</a>
            @else            
                <a class="btn btn-add btn-green btn-header" href="javascript:void(0);" onclick="select_pallet();" title="@lang('messages.replen.start')">@lang('messages.replen.start')</a>
            @endif
        </div>                  
    </div>  
    <input type="hidden" id="replen-product-url" value="{{route('api-replen.index')}}" />
    <input type="hidden" id="replen-select-pallet" value="{{route('api-replen-select-pallet')}}" />
    <input type="hidden" id="replen-finish-pallet" value="{{route('api-replen-finish-pallet')}}" />
    <input type="hidden" id="warehouse_id" value="{{ isset($default_warehouse_id)?$default_warehouse_id:'' }}">
    
    <div class="card-flex-container d-flex py-0">
        <div class="d-flex-xs-block flex-column">
            <div id="productListingID" class="inner-content-body p-2">
                <input type="hidden" id="sort_by" value="">
                <input type="hidden" id="sort_direction" value="">
                <input type="hidden" id="selected_priority" value="">  
                <input type="hidden" id="selected_pallet" value="{{ isset($pallet_pick_location[0]->location_id)?$pallet_pick_location[0]->location_id:'' }}">  
                @if(!empty($aisle_array))
                <p class="font-14-dark bold py-2 mb-2">Aisle Assign to Me: {{ implode(',',$aisle_array) }}</p> 
                @endif

                @if(!empty($pallet_pick_location) && !empty($pallet_pick_location->toArray()))
                    @php                    
                    $pallet_pick_location_final_data='';
                    $pallet_pick_location_final_data.= isset($pallet_pick_location[0]->location)?$pallet_pick_location[0]->location:'';
                    $pallet_pick_location_final_data.= ' ';
                    $pallet_pick_location_final_data.= isset($pallet_pick_location[0]->type_of_location)?LocationType($pallet_pick_location[0]->type_of_location):'';
                    @endphp
                    <p class="text-center bold py-2 mb-2">@lang('messages.replen.move_pro_blo_to') - {{ $pallet_pick_location_final_data }}</p>              
                @endif
                <div class="table-responsive">  
                    <table class="table custom-table cell-align-top table-striped" id="replen-id">
                        @include('replen._replen_products')
                    </table>               
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.replen.scan_pallet')</h5>                
            </div>
            <div class="modal-body p-4">
                <form id="locationsEditForm" method="post">
                    <input type="hidden" name="edit_record_id" value="" id="edit_record_id">
                    <div class="row">
                        <div class="col-lg-9">
                            <input type="text" id="select_pick_location" class="form-control" placeholder="@lang('messages.replen.scan_pick_pallet')" name="select_pick_location" />
                            <span class="location_type font-10-dark bold d-block mt-1"></span>
                        </div>
                        <div class="col-lg-3">
                            <input type="button" class="btn btn-blue btn-full-width startJob" value="Start Job" name="">
                            
                        </div>
                    </div>
                </form>
            </div>            
        </div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript" src="http://topsdemo.co.in/test_m/barcode_scanner.js"></script>
<script src="{{ asset('js/bootstrap-typeahead.js') }}"></script>
<script type="text/javascript" src="{{asset('js/replen/replen.js?v='.CSS_JS_VERSION)}}"></script>
@endsection