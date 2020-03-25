
@if(isset($booking->book_date))
<input type="hidden" id="prev_date" value="{{booking_prev_week_date(date('Y-m-d',strtotime($booking->book_date))) }}">
<input type="hidden" id="view_date" value="{{date('Y-m-d',strtotime($booking->book_date))}}">
<input type="hidden" id="next_date" value="{{booking_next_week_date(date('Y-m-d',strtotime($booking->book_date))) }}">
@else
<input type="hidden" id="prev_date" value="{{booking_prev_week_date($date)}}">
<input type="hidden" id="view_date" value="">
<input type="hidden" id="next_date" value="{{booking_next_week_date($date)}}">
@endif

<table id="weekly-booking-table" class="display">
    <thead>
        <tr>
            <th>@lang('messages.bookings.booking_table.bookin_date')</th>
            <th>@lang('messages.bookings.booking_table.no_of_booking')</th>
            <th>@lang('messages.bookings.booking_table.no_of_pallets')</th>
            <th>@lang('messages.bookings.booking_table.no_of_sku')</th>
            <th>@lang('messages.bookings.booking_table.total_varients')</th>
            <th>@lang('messages.bookings.booking_table.essential_product')</th>
            <th>@lang('messages.bookings.booking_table.seasonal_products')</th>
            <th>@lang('messages.bookings.booking_table.short_date')</th>
            <th>@lang('messages.bookings.booking_table.total_qty')</th>
            <th class="text-right">@lang('messages.bookings.booking_table.po_val')</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th id="weekNo"></th>
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
