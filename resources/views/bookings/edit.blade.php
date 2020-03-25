@extends('layouts.app')
@section('title', __('messages.bookings.edit'))
@section('content')
@section('css')
@endsection
<div class="content-card custom-scroll">
    <input type="hidden" id="searchUrl" value="{{route('bookings.search-po')}}" />
    <input type="hidden" id="weekBookingURL" value="{{route('booking-weekly')}}" />
    <form method="post" id="create-boooking-form" action="{{route('api-booking.store')}}" >
        @csrf
        <input type="hidden" id="edit_booking" name="edit_booking" value="true" />
        <input type="hidden" id="booking_id" name="booking_id" value="{{$booking->id}}" />
        <input type="hidden" id="bookingPOUrl" value="{{route('booking-po')}}" />
        <div class="content-card-header">
            <h3 class="page-title"> @lang('messages.bookings.edit') : #{{$booking->booking_ref_id}}</h3>
            <div class="right-items">
                <a id="add-product-link" href="{{route('booking-in.create','id='.$booking->id.'&selected_option='.$booking->status)}}" class="btn btn-green btn-header" title="@lang('messages.bookings.add_item')">
                    <span class="icon-moon icon-Add font-10 mr-2"></span>@lang('messages.bookings.add_item')
                </a>
                <a class="btn btn-gray btn-form btn-header px-4"  href="{{route('booking-in.index')}}">@lang('messages.common.cancel')</a>
                <button id="create-boooking-button" class="btn btn-blue btn-header px-4">@lang('messages.common.booked_selected_po')</button>
            </div>
        </div>
        <div class="card-flex-container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.purchase_order.form.supplier') :</label>
                            <div class="col-lg-7 pt-2">
                                <p class="mt-1">{{$booking->supplier->name}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-6 col-form-label">@lang('messages.bookings.form.booking_ref') :</label>
                            <div class="col-lg-6 pt-2">
                                <p class="mt-1 bold">
                                    #{{$booking->booking_ref_id}}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group row">
                            <!-- <label for="inputPassword" class="col-lg-2 col-form-label">@lang('messages.bookings.form.status')</label> -->
                            @if($booking->status == 2)
                            <label class="fancy-radio mt-3 mr-3 sm">
                                <input type="radio" name="status" class="status" value="2" @if(isset($booking) && $booking->status == 2) checked="checked" @endif />
                                       <span class="font-14-dark"><i></i>@lang('messages.bookings.form.reserve_slot_without')</span>
                            </label> &nbsp;
                            @endif
                            <label class="fancy-radio mt-3 mr-3 sm">
                                <input type="radio" name="status" class="status"  value="1" @if(isset($booking) && $booking->status == 1) checked="checked" @endif />
                                       <span class="font-14-dark"><i></i>@lang('messages.bookings.form.reserve_slot_with')</span>
                            </label> &nbsp;
                            <label class="fancy-radio mt-3 mr-3 sm">
                                <input type="radio" name="status" class="status" value="3" @if(isset($booking)) @if($booking->status == 3) checked="checked" @endif @else checked="checked" @endif/>
                                       <span class="font-14-dark"><i></i>@lang('messages.bookings.form.confirm')</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="d-flex-xs-block mb-4" id="po-booking-container">
                    <table id="po_listing_table" class="display">
                        <thead>
                            <tr>
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
                                <th class="w-100">@lang('messages.bookings.po_table.status')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Total:</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div  id="booking-slot-container">
                    @include("bookings._add-booking-slot-form")
                </div>
                <h3 class="p-title mb-2 mt-5">View Bookings-ins: </h3>
                <div class="d-flex-xs-block  mt-4" id="week-booking-container">
                    @include('bookings._week-booking')
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="{{asset('js/bookings/edit.js?v='.CSS_JS_VERSION)}}"></script>
<script type="text/javascript" src="{{asset('js/bookings/week_listing.js?v='.CSS_JS_VERSION)}}"></script>
@endsection