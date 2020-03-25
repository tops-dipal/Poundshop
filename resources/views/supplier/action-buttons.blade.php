<ul class="action-btns">        
        <li>
            <a class="btn-edit send_email" href="javascript:void(0);" attr-id="{{$object->id}}" onclick="send_email(this)" title="@lang('messages.purchase_order.send_email')"><span class="icon-moon icon-Mail"></span></a>
        </li>

        @can('supplier-edit')
        <li>
            <a class="btn-edit" href="{{route('supplier.form',$object->id)}}#general" title="@lang('messages.common.edit')"><span class="icon-moon icon-Edit"></span></a>
        </li>
        @endcan
        
        @can('supplier-delete')
        <li>
            <a class="btn-delete" href="javascript:;" attr-id="{{$object->id}}" onclick="delete_record(this)" title="@lang('messages.common.delete')"><span class="icon-moon icon-Delete"></span></a>
        </li>
        @endcan
</ul>