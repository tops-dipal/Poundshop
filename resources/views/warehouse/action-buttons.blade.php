<ul class="action-btns">
    @can('warehouse-edit')
    <li>
        <a class="btn-edit" href="{{route('warehouse.edit',$object->id)}}" title="@lang('messages.common.edit')"><span class="icon-moon icon-Edit"></span></a>
    </li>
    @endcan
    @can('warehouse-delete')
    <li>
        <a class="btn-delete" href="javascript:void(0);" id="{{$object->id}}" title="@lang('messages.common.delete')"><span class="icon-moon icon-Delete"></span></a>
    </li>
    @endcan
</ul>