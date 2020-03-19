<ul class="action-btns">    
    @can('cartons-edit')
    <li>
        <a class="btn-edit" href="{{route('cartons.edit',$object->id)}}"><span class="icon-moon icon-Edit"></span></a>
    </li>
    @endcan
    @can('cartons-delete')
    <li>
        <a class="btn-delete" href="javascript:void(0);" id="{{$object->id}}"><span class="icon-moon icon-Delete"></span></a>
    </li>
    @endcan
</ul>