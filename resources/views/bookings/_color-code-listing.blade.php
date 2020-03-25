@if($column == 'title')
<span class="status-color" style="background:{{config('params.po_status_color_code')[$object->po_status]}}"></span><a class="edit-po-items" id="{{$object->selected_booking_po_id}}" target="_blank" href="{{route('purchase-orders.edit',$object->id)}}#general" title="@lang('messages.purchase_order.edit')" >{{$object->po_number}}</a>
@if($object->is_drop_shipping == 1) <br><p class="mt-2" style="color:red">Dropshipping PO</p>@endif
@if($object->is_outstanding_po == 1) <br><p class="mt-2" style="color:green">Outstanding PO</p> @endif

@else
<span style="color:{{config('params.po_status_color_code')[$object->po_status]}}">{{array_search($object->po_status,config('params.po_status'))}}</span>
@endif