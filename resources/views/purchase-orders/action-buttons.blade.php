<ul class="action-btns">
    
        <li>
            <a title="@lang('messages.purchase_order.edit')" class="btn-edit" href="{{route('purchase-orders.edit',$object->id)}}#general/"><span class="icon-moon icon-Edit"></span></a>
        </li>
    
       @if($object->po_status !== 10)
        <li>
                <a title="@lang('messages.purchase_order.delete')" class="btn-delete" href="javascript:;" id="{{$object->id}}"><span class="icon-moon icon-Delete"></span></a>
        </li>
       @endif
    
</ul>