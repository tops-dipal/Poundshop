<ul class="action-btns">
        @can('product-edit')
        <li>
            <a class="btn-edit" href="{{url('product/form/'.$object->id.'?active_tab=stock-file')}}" ><span class="icon-moon icon-Edit" title="@lang('messages.common.edit')"></span></a>
        </li>
        @endcan
        
        @can('product-delete')
        <li>
            <a class="btn-delete" href="javascript:;" attr-id="{{$object->id}}" onclick="delete_record(this)"><span class="icon-moon icon-Delete" title="@lang('messages.common.delete')"></span></a>
        </li>
        @endcan
</ul>