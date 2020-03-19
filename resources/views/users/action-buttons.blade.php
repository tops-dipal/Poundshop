<ul class="action-btns">
    <li>
        <a class="btn-edit" href="{{route('users.edit',$object->id)}}"><span class="icon-moon icon-Edit"></span></a>
    </li>
    @if($object->id != $logUser->id)
		<li>
		    <a class="btn-delete" href="javascript:;" id="{{$object->id}}"><span class="icon-moon icon-Delete"></span></a>
		</li>
    @endif
</ul>