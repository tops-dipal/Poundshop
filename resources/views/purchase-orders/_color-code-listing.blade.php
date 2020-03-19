@if($column == 'title')
<span class="status-color" style="background:{{config('params.po_status_color_code')[$object->po_status]}}"></span> <a href="{{route('purchase-orders.edit',$object->id)}}#general" title="@lang('messages.purchase_order.edit')" >{{$object->po_number}}</a>
                                                                                                                          
@else
    <span style="color:{{config('params.po_status_color_code')[$object->po_status]}}">{{array_search($object->po_status,config('params.po_status'))}}</span>
@endif