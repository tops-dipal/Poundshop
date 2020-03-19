<ul class="action-btns">
        @can('supplier-edit')
        <li>
            <a class="btn-edit" href="{{route('supplier.edit',$object->id)}}" ><span class="icon-moon icon-Edit"></span></a>
        </li>
        @endcan
        
        @can('supplier-delete')
        <li>
            <a class="btn-delete" href="javascript:;" id="{{$object->id}}"><span class="icon-moon icon-Delete"></span></a>
        </li>
        @endcan
</ul>