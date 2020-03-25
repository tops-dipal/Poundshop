<ul class="action-btns">
    <li>
        <a class="btn-edit" href="{{route('users.edit',$object->id)}}" title="@lang('messages.common.edit')"><span class="icon-moon icon-Edit"></span></a>
    </li>
    @if($object->id != $logUser->id)
		<li>
		    <a class="btn-delete" href="javascript:;" id="{{$object->id}}" title="@lang('messages.common.delete')"><span class="icon-moon icon-Delete"></span></a>
		</li>
    @endif
</ul>