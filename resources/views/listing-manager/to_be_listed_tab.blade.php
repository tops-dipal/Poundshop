@extends('layouts.app')
@section('content')
@section('title',__("messages.modules.listing_manager"))
    <div class="content-card custom-scroll">
        <input type="hidden" name="active_tab" id="active_tab" value="{{ $active_tab }}">
        <div class="content-card-header">
            <h3 class="page-title">Magento Listing Manager</h3> 
            <div class="center-items">
                <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $active_tab == 'already-listed' ? 'active' : ''}}"  href="{{ route('magento-already-listed') }}">
                            Already Listed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $active_tab == 'to-be-listed' ? 'active' : ''}}" id="to-be-listed-tab" href="{{ route('magento-to-be-listed') }}">
                            To be Listed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  {{ $active_tab == 'inprogress' ? 'active' : ''}}" id="inprogress-tab"  href="{{ route('magento-in-progress') }}">
                             In Progress
                        </a>
                    </li>
                </ul>  
            </div>
            <div class="right-items">
                <div class="center-items p-0">
                       
                    <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_listing_manager')" />
                      <span class="refresh"></span>
                </div>
                <button id="btnFilter" class="btn btn-filter btn-header"><span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/></button>
                <div class="search-filter-dropdown">
                    <form method="post" id="listing-manager-form" enctype="multipart/form-data">
                        <select name="store_id" id="store_id"  class="form-control" hidden="">
                            @forelse($storeList as $key=>$val)
                            <option value="{{ $val->id }}" {{ $loop->first ? 'selected="selected"' : '' }}>{{ $val->store_name }}</option>
                            @empty
                            @endforelse
                            
                        </select>
                        <div class="form-fields">
                       
                            <div class="filter-container">
                                <h2 class="title">@lang('messages.modules.filter_by')</h2>
                                <div class="container-fluid p-0">
                                    
                                        <div class="col-lg-12">
                                            <div class="form-group row align-items-center">
                                                <input type="hidden" name="sort_by_season" id="sort_by_season" value="0">
                                                     <label class="fancy-checkbox">
                                                     <input type="checkbox" name="sort_by_seasonal" class="sort_seasonal"><span><i></i> Sort Products by Seasonal.</span>
                                                 </label>
                                            </div>
                                        </div>
                                    
                                </div>
                            </div>
                        </div>
                    </form>    
                </div>  
            </div>  
        </div>
         <div class="card-flex-container d-flex">
            <div class="d-flex-xs-block">
                <div class="table-responsive">
                    <table id="to_be_listed_table" class="display">
                        <thead>
                            <tr>
                                <th class="remove_sort">
                                    <div class="d-flex">
                                        <label class="fancy-checkbox">
                                            <input name="ids[]" type="checkbox" class="master">
                                            <span><i></i></span>
                                        </label>
                                        <div class="dropdown bulk-action-dropdown">
                                            <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="icon-moon icon-Drop-Down-1"/>
                                            </button>
                                            
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                                <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                                <button class="btn btn-add btn-light-green list-many">
                                                <span class="icon-moon green icon-Add"></span>
                                                @lang('messages.magento_listing.list')
                                                </button>
                                                <button class="btn btn-add live-on-magento">
                                                <span class="icon-moon red icon-Delete"></span>
                                               @lang('messages.magento_listing.go_live_magento')
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>@lang('messages.inventory.image')</th>
                                <th class="m-w-200">@lang('messages.inventory.title')</th>
                                <th class="w-150">@lang('messages.inventory.sku')</th>
                                <th class="w-130">@lang('messages.common.quantity')</th>
                                <th class="w-130 dt-head-align-right">
                                    <span class="dt-head-text">
                                        @lang('messages.common.price')
                                    </span>
                                </th>
                                <th class="w-130">@lang('messages.magento_listing.date_to_list')</th>
                                <th data-class-name="action action-one">@lang('messages.table_label.action')</th>              
                            </tr>
                        </thead>
                        
                        <tbody>
                            
                        </tbody>
                    </table>     
                </div>                
            </div>
        </div>  
    </div>

    <!-- Bulk action date to go live Modal -->
    <div class="modal fade" id="dateToGoLiveModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg custom-modal" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title" id="exampleModalLabel">
                        @lang('messages.magento_listing.go_live_magento')
                    </h5>
                    <div>
                        <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal" aria-label="Close">
                        @lang('messages.common.cancel')
                        </button>
                        <button type="button" class="btn btn-green font-12 px-3 ml-3" onclick="bulkDateToGoLive(this)">@lang('messages.common.save')</button>
                    </div>
                </div>
                <div class="modal-body p-4">
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">@lang('messages.magento_listing.date_to_go_live')
                                <span class="asterisk">*</span>
                            </label>
                            <div class="col-lg-4">
                                <input type="text" readonly="" class="form-control date_to_go_live" placeholder="" name="date_to_go_live" value="{{ date('d-M-Y h:i') }}">
                            </div>
                        </div>
                    </div>       
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/listing-manager/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection