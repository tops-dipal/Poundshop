@php use Illuminate\Http\Request; @endphp
@extends('layouts.app')
@if(isset($booking))
@section('title', __('messages.bookings.edit'))
@else
@section('title', __('messages.bookings.create'))
@endif
@section('content')
@section('css')
@endsection
<div class="content-card custom-scroll">
    <input type="hidden" id="searchUrl" value="{{route('bookings.search-po')}}" />
    <input type="hidden" id="weekBookingURL" value="{{route('booking-weekly')}}" />
    <form method="post" id="create-boooking-form" action="{{route('api-booking.store')}}" >
        @csrf
        <input type="hidden" id="booking_id" name="booking_id" value="@isset($booking){{$booking->id}}@endisset" />
        @if(isset($booking->id))
        <input type="hidden" id="edit_booking" name="edit_booking" value="true" />
        <input type="hidden" id="po_add_edit" name="po_add_edit" value="true" />
        @endif
        <div class="content-card-header">
            @if(isset($booking))
            <h3 class="page-title"> @lang('messages.bookings.edit') : #{{$booking->booking_ref_id}}</h3>
            @else
            <h3 class="page-title">@lang('messages.bookings.create')</h3>
            @endif
            <div class="right-items">
                <a class="btn btn-gray btn-form btn-header px-4"  href="{{route('booking-in.index')}}">@lang('messages.common.cancel')</a>
                @if(isset($booking))
                <button id="create-boooking-button" class="btn btn-blue btn-header px-4">@lang('messages.common.save')</button>
                @else
                <button id="create-boooking-button" class="btn btn-blue btn-header px-4">@lang('messages.common.booked_selected_po')</button>
                @endif
            </div>
        </div>
        <div class="card-flex-container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-3 col-form-label">@lang('messages.purchase_order.form.supplier')<span class="asterisk">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control custom-select-search" id="supplier" name="supplier" @if(isset($booking->supplier_id)) disabled="disabled" @endif>
                                        @if(isset($booking->supplier_id))
                                        <option value="{{$booking->supplier->id}}">{{$booking->supplier->name}}</option>
                                    @else
                                    <option value="">Select Supplier Name</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 pt-3">
                        @if(isset($booking))
                        <label class="fancy-radio sm">
                            <input type="radio" name="status" class="status"  value="1" @if(isset($booking) && $radioOption == 1) checked="checked" @endif />
                                   <span class="font-14-dark"><i></i>@lang('messages.bookings.form.reserve_slot_with')</span>
                        </label> &nbsp;
                        <label class="fancy-radio sm">
                            <input type="radio" name="status" class="status" value="3" @if(isset($booking)) @if($radioOption == 3) checked="checked" @endif @else checked="checked" @endif />
                                   <span class="font-14-dark"><i></i>@lang('messages.bookings.form.confirm')</span>
                        </label>
                        @else
                        <label class="fancy-radio sm">
                            <input type="radio" name="status" class="status" value="2" @if(isset($booking) && $booking->status == 2) checked="checked" @endif />
                                   <span class="font-14-dark"><i></i>@lang('messages.bookings.form.reserve_slot_without')</span>
                        </label> &nbsp;
                        <label class="fancy-radio sm">
                            <input type="radio" name="status" class="status"  value="1" @if(isset($booking) && $booking->status == 1) checked="checked" @endif />
                                   <span class="font-14-dark"><i></i>@lang('messages.bookings.form.reserve_slot_with')</span>
                        </label> &nbsp;
                        <label class="fancy-radio sm">
                            <input type="radio" name="status" class="status" value="3" @if(isset($booking)) @if($booking->status == 3) checked="checked" @endif @else checked="checked" @endif />
                                   <span class="font-14-dark"><i></i>@lang('messages.bookings.form.confirm')</span>
                        </label>
                        @endif
                    </div>
                </div>

                <!-- <label for="inputPassword" class="col-lg-2 col-form-label">@lang('messages.bookings.form.status')</label> -->

                @if(isset($booking) && $radioOption == array_search('Reserve Slot Without PO', config("params.booking_status")))
                <div class="row" id="search-table-container" style="display: none;">
                    @else
                    <div class="row" id="search-table-container">
                        @endif
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-3 col-form-label">@lang('messages.bookings.form.search')</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="search" placeholder="Search By PO Number, Supplier Order Number" name="search" >
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 pt-2">
                            <label class="fancy-checkbox mt-2">
                                <input type="checkbox" name="cancelled" id="cancelled" value="0">
                                <span class="font-14-dark"><i></i> @lang('messages.bookings.form.show_cancelled')</span>
                            </label>
                        </div>
                    </div>
                    <div  id="booking-slot-container" >
                        @include("bookings._add-booking-slot-form")
                    </div>
                    <!-- Booking Container -->
                    @if(isset($booking) && $radioOption == array_search('Reserve Slot Without PO', config("params.booking_status")))
                    <div class="d-flex-xs-block" id="po-booking-container" style="display:none;">
                        @else
                        <div class="d-flex-xs-block" id="po-booking-container">
                            @endif

                            <table id="po_listing_table" class="display">
                                <thead>
                                    <tr>
                                        <th>
                                        </th>
                                        <th>@lang('messages.bookings.po_table.po_no')</th>
                                        <th>@lang('messages.bookings.po_table.sup_no')</th>
                                        <th>@lang('messages.bookings.po_table.es_del')</th>
                                        <th>@lang('messages.bookings.po_table.skus')</th>
                                        <th>@lang('messages.bookings.po_table.es_var')</th>
                                        <th>@lang('messages.bookings.po_table.es_pr')</th>
                                        <th>@lang('messages.bookings.po_table.sea_pro')</th>
                                        <th>@lang('messages.bookings.po_table.st_dt')</th>
                                        <th>@lang('messages.bookings.po_table.t_qty')</th>
                                        <th>@lang('messages.bookings.po_table.po_val')</th>
                                        <th>@lang('messages.bookings.po_table.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- Booking Container -->

                    </div>
                    <div class="container-fluid">
                        @if(isset($booking))
                        <h3 class="p-title mb-2 mt-5 view-bookingIns">View Bookings-ins: </h3>
                        <div class="d-flex-xs-block mt-4 " id="week-booking-container">
                            @else
                            <h3 class="p-title mb-2 mt-5 view-bookingIns" style="display: none;">View Bookings-ins: </h3>
                            <div class="d-flex-xs-block mt-4" id="week-booking-container" style="display: none;">
                                @endif
                                @include('bookings._week-booking')
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                @endsection
                @section('script')
                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
                <script type="text/javascript" src="{{asset('js/bookings/create.js?v='.CSS_JS_VERSION)}}"></script>
                <script type="text/javascript" src="{{asset('js/bookings/week_listing.js?v='.CSS_JS_VERSION)}}"></script>
                @endsection