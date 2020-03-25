@foreach($purchaseOrders as $purchaseOrder)
<tr>
    <td><div class="d-flex">
            <label class="fancy-checkbox">
                <input name="ids[]" type="checkbox" value="{{$purchaseOrder->id}}" class="child-checkbox">
                <span><i></i></span>
            </label>
        </div></td>
    <td><a target="_blank" href="{{route('purchase-orders.edit',$purchaseOrder->id)}}#general" title="@lang('messages.purchase_order.edit')" >{{$purchaseOrder->po_number}}</a></td>
    <td>{{$purchaseOrder->supplier_order_number}}</td>
    <td>{{$purchaseOrder->exp_deli_date}}</td>
    <td>{{$purchaseOrder->total_skus}}</td>
    <td>{{$purchaseOrder->total_variant}}</td>
    <td>{{$purchaseOrder->essential_product}}</td>
    <td>{{$purchaseOrder->seasonal_product}}</td>
    <td>--</td>
    <td>{{$purchaseOrder->total_quantity}}</td>
    <td>@lang('messages.common.pound_sign'){{priceFormate($purchaseOrder->sub_total)}}</td>
    <td>    <span style="color:{{config('params.po_status_color_code')[$purchaseOrder->po_status]}}">{{array_search($purchaseOrder->po_status,config('params.po_status'))}}</span>
    </td>

</tr>
@endforeach