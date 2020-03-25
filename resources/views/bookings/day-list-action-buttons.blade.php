<ul class="action-btns">    
    @if(!empty($object->comment))
    <li>
        <a tabindex="0" class="btn-edit" data-placement="left"  data-toggle="popover" data-trigger="focus" title="Comment" data-content="{{$object->comment}}"><span class="icon-moon icon-Information"></span></a>
    </li>
    @endif        
    <?php
    $delete_allowed_status=array('1','2','3');
    ?>    
    @if(!empty($object->status) && in_array($object->status,$delete_allowed_status))
    <li>
        <a class="btn-edit" href="{{route('booking-in.edit',$object->id)}}" title="@lang('messages.common.edit')"><span class="icon-moon icon-Edit"></span></a>
    </li>
    <li>
        <a class="btn-delete" href="javascript:void(0);" id="{{$object->id}}" title="@lang('messages.common.delete')"><span class="icon-moon icon-Delete"></span></a>
    </li> 
    @endif
    @if(!empty($object->status) && $object->status!='2' && !empty($object->po_list) && !empty($object->warehouse_id))
    <li>
        <a class="btn-edit" href="{{route('material_receipt.index',$object->id)}}" title="@lang('messages.material_receipt.material_receipt')"><span class="icon-moon size-sm icon-Active"></span></a>
    </li> 
    @endif   
</ul>