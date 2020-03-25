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
                    <!-- <label class="fancy-checkbox">
                        <input type="checkbox" name="sort_by_seasonal" class="sort_seasonal"><span><i></i> Sort Products by Seasonal.</span>
                    </label> -->
                    <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_listing_manager')" />
                      <span class="refresh"></span>
                </div>
                <form method="post" id="listing-manager-form" enctype="multipart/form-data">

                <select name="store_id" id="store_id"  class="form-control" hidden="">
                    @forelse($storeList as $key=>$val)
                    <option value="{{ $val->id }}" {{ $loop->first ? 'selected="selected"' : '' }}>{{ $val->store_name }}</option>
                    @empty
                    @endforelse
                    
                </select>
                 </form>   
            </div>  
                          
        </div>  
        <div class="card-flex-container d-flex">
            <div class="d-flex-xs-block">
                <!-- <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="already-listed" role="tabpanel" aria-labelledby="already-listed-tab"> -->
                        <div class="table-responsive">
                        <table id="inprogress_table" class="display">
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
                                                    <!-- <span class="icon-moon icon-Drop-Down-1"/> -->
                                                </button>
                                                
                                               <!--  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                                    <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                                 
                                                </div> -->
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
                                    <th data-class-name="action action-two">@lang('messages.table_label.action')</th>              
                                </tr>
                            </thead>
                            
                            <tbody>
                                
                            </tbody>
                        </table>  
                    </div>
                    <!-- </div>
                </div> -->
            </div>
        </div>
    </div>

@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/listing-manager/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection
  