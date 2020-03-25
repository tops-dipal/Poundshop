
@if(@count($deliveryData) > 0 && $deliveryData[0] instanceof App\Booking)
@php
$bookingProducts=$deliveryData->bookingProducts;
$deliveryData=$deliveryData[0];
@endphp
<input type="hidden" name="booking_id" id="booking_id" value="{{$deliveryData->id}}" />
<div class="po-delivered">
    <div class="flex-none">
        <div class="d-flex align-items-center po-delivered-header">
            <span class="font-12-dark mr-5 color-blue">@lang('messages.bookings.form.booking_ref'): <a href="javascript:;">#{{$deliveryData->booking_ref_id}}</a></span>
            <div>
                <label class="fancy-checkbox ml-5">
                    <input type="checkbox" name="show_over_delivered" id="show_over_delivered" value="0">
                    <span class="font-12-dark"><i></i> Show Over Delivered.</span>
                </label>
                <label class="fancy-checkbox ml-5">
                    <input type="checkbox" name="show_under_delivered" id="show_under_delivered" value="0">
                    <span class="font-12-dark"><i></i> Show Under Delivered.</span>
                </label>
                <label class="fancy-checkbox ml-5">
                    <input type="checkbox" name="show_no_discrepancy" id="show_no_discrepancy" value="0">
                    <span class="font-12-dark"><i></i> Show No Discrepancy.</span>
                </label>
            </div>
            <div class="flex-one text-right">
                <button data-url="" class="btn btn-green font-12 px-4 ml-4" id="send-email-supplier-btn">@lang('messages.purchase_order.deliveries.send_email')</button>
            </div>

        </div>

    </div>


    <div class="flex-one overflow-auto custom_fix_header">
        <div class="table-responsive">
            <table class="table custom-table cell-align-top table_fix_header table-striped" id="delivery-content-table">
                <thead>
                    <tr>
                        <td class="w-25">@lang('messages.purchase_order.deliveries.table.info')</td>
                        <td class="w-10">@lang('messages.purchase_order.deliveries.table.barcode')</td>
                        <td class="w-8">@lang('messages.purchase_order.deliveries.table.qty_ord')</td>
<!--                        <td class="w-10">@lang('messages.purchase_order.deliveries.table.qty_note')</td>-->
                        <td class="w-8">@lang('messages.purchase_order.deliveries.table.qty_rec')</td>
                        <td class="w-8">@lang('messages.purchase_order.deliveries.table.diff')</td>
                        <td class="w-8">Stock to Pay For</td>
                        <td class="w-15">@lang('messages.purchase_order.deliveries.table.dis_detail')</td>
                        <td class="w-15">@lang('messages.purchase_order.deliveries.table.status')</td>
                    </tr>
                </thead>
                <tbody>
                    @include('purchase-orders._delivery-content')
                </tbody>
            </table>
        </div>
    </div>

    <input type="hidden" value="{{route('delivery.product-location-detail')}}" id="productLocationURL" />
    <input type="hidden" value="{{route('delivery.cancelled')}}" id="productCancelledURL" />
    <input type="hidden" value="{{route('delivery.move-new-po')}}" id="moveToNewPOURL" />
    <input type="hidden" value="{{route('delivery.delivery-filters')}}" id="deliveryData" />
    <div class="modal fade" id="deliveryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="custom-modal modal-dialog modal-lg" role="document">
            <form method="post" id="keep-return-supplier-form" action="{{route('delivery.updatediscrepancy')}}">
                <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h5 class="modal-title" id="exampleModalLabel">Keep It or Return to Supplier</h5>
                        <div>
                            <button type="button" class="btn btn-gray font-12 px-4" data-dismiss="modal" aria-label="Close">
                                @lang('messages.common.cancel')
                            </button>
                            <button type="submit" class="btn btn-green font-12 px-4 ml-3" id="keep-return-supplier-form-btn" title="@lang('messages.common.save')">@lang('messages.common.save')</button>
                        </div>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endif
