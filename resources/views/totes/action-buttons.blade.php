<ul class="action-btns">
        <li>
            <a class="btn-edit" href="{{route('totes.edit',$object->id)}}" title="@lang('messages.common.edit')"><span class="icon-moon icon-Edit"></span></a>
        </li>
        <li>
            <a class="btn-delete" href="javascript:;" id="{{$object->id}}"><span class="icon-moon icon-Delete" title="@lang('messages.common.delete')"></span></a>
        </li>
</ul>