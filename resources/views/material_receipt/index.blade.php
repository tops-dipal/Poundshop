@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))

@section('content')
<style type="text/css">
    .location_hide {
        display: none;
    }
    .dropdown-menu{
        padding:0px !important;
    }
</style>
<script type="text/javascript">
    var SCAN_START_DATE = '{{ !empty($booking_details->start_date) ? system_date($booking_details->start_date) : system_date_time() }}';
</script>

<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3> 
        <div class="center-items">
            <div class="d-flex flex-one align-items-center mr-4">
            	<span class="font-12-dark text-nowrap mr-3">Arrived Date:</span>	
            	<input type="text" class="form-control header-form-control w-180 datepicker_disbale_future" readonly="" id="arrived_date" value="{{!empty($booking_details->arrived_date) ? system_date($booking_details->arrived_date) : system_date()}}" onchange="setArrivedDate(this)" />
            </div>
            <div class="d-flex flex-one ml-4">
            	<p class="mr-4">
            		<span class="font-12-dark d-block">Start Date:</span>
            		<span class="font-12-dark bold">{{ !empty($booking_details->start_date) ? system_date_time($booking_details->start_date) : system_date_time() }}</span>
                </p>
            	<p>
            		<span class="font-12-dark d-block">Complete Date:</span>
            		<span class="font-12-dark bold">{{ !empty($booking_details->completed_date) ? system_date_time($booking_details->completed_date) : '' }}</span>
            	</p>             	
            </div>
        </div>    
        <div class="right-items">
			<button class="btn btn-light-blue btn-header" onclick="send_email(this);" data-placement="bottom" data-toggle="tooltip" title="@lang('messages.material_receipt.send_email_to_supplier')">
				<span class="icon-moon icon-Mail font-10 ml-0"></span>
			</button>           
        	<button class="btn btn-green btn-header" onclick="setBookingCompleted(this)">
        		<span class="icon-moon icon-Select font-10 mr-2"></span>
        		Completed
        	</button>           
            <button id="btnFilter" class="btn btn-filter btn-header">
            	<span class="icon-moon icon-Filter"></span>
            	@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/>
            </button>

            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex">
                    <div class="form-fields">                        
                        <div class="filter-container" id="custom_advance_search_fields">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.material_receipt.filter_select_po')</label>
                                            <div class="col-lg-7">
                                                <select class="form-control" name="filter_by_po">
                                                    <option value="">@lang('messages.material_receipt.filter_select_all_po')</option>
                                                    @if(!$booking_pos->isEmpty())
                                                        @foreach($booking_pos as $booking_po)
                                                            <option value="{{ $booking_po->purchaseOrder->id }}">{{ $booking_po->purchaseOrder->po_number }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>    
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                            </div>    
                        </div>
                    </div>
                    <div class="form-buttons">
                        <input type="button" class="btn btn-gray cancle_fil" title="@lang('messages.common.reset_filter')" value="@lang('messages.common.reset_filter')">
                        <input type="button" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch(event);">
                    </div>
                </form>
            </div>
        </div>                  
    </div>  
    
    <div class="card-flex-container d-flex py-0">                        
        <div class="d-flex-xs-block flex-column">
            <div class="material-receipt-header pb-2">
            	<div class="bg-gray d-flex align-items-center">
	            	@php
                        $pending_products = '-';

                        if(!empty($booking_details->total_products))
                        {
                            $completed_products = !empty($booking_details->total_completed_products) ? 

                            $booking_details->total_completed_products : 0;

                            $pending_products =  $booking_details->total_products - $completed_products;

                            $pending_products = ($pending_products > 0) ? $pending_products : '0';
                        }

                    @endphp
                    <span class="font-18-dark mr-5 color-red">Pending Product: <span class="font-18-dark bold color-red" id="pendingCountLabel"> {{ $pending_products }} </span></span>
	            	<span class="font-12-dark mr-5 color-blue">Booking In Ref. No. <a href="{{route('booking-in.edit', $booking_details->id )}}" class="bookin-ref font-12-dark bold color-blue">{{ $booking_details->booking_ref_id }}</a></span>
	            	<span class="font-12-dark mr-5">Supplier: <span class="font-12-dark bold">{{ ucwords($booking_details->supplierDetails->name) }}</span></span>
	            	<span class="font-14-dark">Receiving Warehouse: <span class="font-12-dark bold">{{ !empty($booking_details->wareHouseDetails) ? ucwords($booking_details->wareHouseDetails->name) : '-' }}</span></span>
                    @if(!empty($booking_details->completed_date))
                    <button class="btn btn-green font-12 bold px-3 py-1 ml-5 float-right" data-toggle="collapse" data-target="#goods_in_summary">
                        <span class="icon-moon icon-Reporting mr-2"></span>
                        View Summary
                    </button>
                    @endif
	            </div>                
                <!--Summary of goods In Starts-->
                @if(!empty($booking_details->completed_date))
                <div id="goods_in_summary" class="goods-in-summary collapse" class="goods-in-summary">
                    <div class="row">
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_qty_rec')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_qty_received)?$booking_details->total_qty_received:'0' }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_val_rec')</span>
                                <span class="font-12-dark mr-1">&#163;</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_value_received)?$booking_details->total_value_received:'0' }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_vari_rec')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_variants)?$booking_details->total_variants:'0' }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_new_rec')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_new_products)?$booking_details->total_new_products:'0' }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_dam_rec')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_damage_trade_qty)?$booking_details->total_damage_trade_qty:'0' }}</span>
                            </div>
                            
                        </div>
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_shor_rec')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_short_qty)?$booking_details->total_short_qty:'0' }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_over_rec')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_over_qty)?$booking_details->total_over_qty:'0' }}</span>
                            </div>
                        </div>
                        
                        <!-- <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.summ_tot_diff_rec')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_diff_po_note)?$booking_details->total_diff_po_note:'0' }}</span>
                            </div>                            
                        </div> -->

                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.total_qty_instock')</span>
                                <span class="font-12-dark">{{ !empty($booking_details->total_qty_instock)?$booking_details->total_qty_instock:'0' }}</span>
                            </div>                            
                        </div>
                        
                        <div class="col-lg-3 mb-1">
                            <div class="summary-item">
                                <span class="title font-12-dark mr-5">@lang('messages.material_receipt.total_value_payable')</span>
                                <span class="font-12-dark mr-1">&#163;</span>
                                <span class="font-12-dark"> {{ !empty($booking_details->total_value_payable)?$booking_details->total_value_payable : '0' }}</span>
                            </div>                            
                        </div>

                    </div>                    
                </div>
                @endif
                <!--Summary of goods In Ends-->
	            <div class="d-flex align-items-center">
	            	<div class="search-product-container mr-3">
	            		<select class="form-control select-product-filter clear_except" id="search_type" onchange="setSearchType(this)">
	            			<option value="all_products" {{ ($search_type == 'all_products')  ? 'selected="selected"'  :  "" }}>
                                @lang('messages.material_receipt.filter_all_products')            
                            </option>
                            <option value="pending_products" {{ ($search_type == 'pending_products')  ? 'selected="selected"'  :  "" }}>
                                @lang('messages.material_receipt.filter_pending_products')
                            </option>
                            <option value="completed_products" {{ ($search_type == 'completed_products')  ? 'selected="selected"'  :  "" }}>
                                @lang('messages.material_receipt.filter_completed_products')
                            </option>
	            		</select>
	            		<input type="text" class="form-control" placeholder="@lang('messages.material_receipt.search_placeholder')" id="txt_search" value="{{ $search }}"/>
	            	</div>
	            	<div>
	            		<label class="fancy-checkbox sm">
	            			<input type="checkbox" id="show_discrepancies" value="1" {{ (@$show_discrepancies == 1) ? 'checked="checked"' : '' }}>
	            			<span class="font-14-dark bold"><i></i>
                                @lang('messages.discrepancy.show_discri')</span>
	            		</label>
	            	</div>
	            </div>
            </div>
            
            <input type="hidden" id="pagination_url" value="{{ $pagination_url }}">
            
            <input type="hidden" id="per_page_value" value="{{ !empty($per_page_value) ? $per_page_value : '' }}">

            <input type="hidden" id="pagination_page" value="{{ !empty($pagination_page) ? $pagination_page : '' }}">
            
            <input type="hidden" id="pagination_sort_by" value="{{ !empty($pagination_sort_by) ? $pagination_sort_by : '' }}">

            <input type="hidden" id="pagination_sort_direction" value="{{ !empty($pagination_sort_direction) ? $pagination_sort_direction : '' }}">

            <input type="hidden" id="booking_id" value="{{ !empty($booking_details->id) ? $booking_details->id : '' }}">

            <input type="hidden" id="warehouse_id" value="{{ !empty($booking_details->warehouse_id) ? $booking_details->warehouse_id : '' }}">

                          
            <!-- <div class="text-right px-3">                    
                <button id="debit_note_status" class="btn btn-blue font-12 px-4" onclick="change_status_disc(1);">@lang('messages.material_receipt.debit_note_button')</button>
                <button id="keep_it_status" class="btn btn-blue font-12 px-4" onclick="change_status_disc(2);">@lang('messages.material_receipt.keep_it_button')</button>
                <button id="dispose_of_status" class="btn btn-blue font-12 px-4" onclick="change_status_disc(3);">@lang('messages.material_receipt.dispose_of_button')</button>
                <button id="return_supplier_status" class="btn btn-blue font-12 px-4" onclick="change_status_disc(4);">@lang('messages.material_receipt.return_supplier_button')</button>
            </div> -->
            
            <div class="material-receipt-body p-2 d-flex">
                <div id="load-ajax-table" class="d-flex flex-column flex-one">	
    			</div>	
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal" id="manageVariation">
    </div>
     <input type="hidden" name="sidebar_access_url" id="sidebar_access_url" value="{{ route('material_receipt.sidebar-view') }}">
    <div class="checklist-container">

        <a href="javascript:void(0)" class="btn-checklist-toggle">
            <span class="icon-moon icon-Spot-Check font-18"></span>
        </a> 
    </div>
   {{--  @include('material_receipt.booking-qc-list') --}}
    @include('material_receipt.discrepancy-items')
</div>
@endsection

@section('script')
    <script type="text/javascript" src="http://topsdemo.co.in/test_m/barcode_scanner.js"></script>
    <script src="{{ asset('js/bootstrap-typeahead.js') }}"></script>
    <script type="text/javascript" src="{{asset('js/material_receipt/delievery_note_qc.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/material_receipt/index.js?v='.time())}}"></script>
@endsection
