<ul class="action-btns">
    @can('locations-edit')
    <li>
        <a class="btn-edit inline-edit" href="javascript:void(0);" id="{{$object->id}}" title="@lang('messages.modules.button_save')" onclick="edit_location('{{$object->id}}','{{$object->not_allowed_edit_delete_status}}')"><span class="icon-moon icon-Edit"></span>
        </a>
    </li>
    @endcan
    @can('locations-delete')
    @if($object->type_of_location!='11')
    <li>
        <a class="btn-delete" href="javascript:void(0);" id="{{$object->id}}" title="@lang('messages.common.delete')"><span class="icon-moon icon-Delete"></span></a>
    </li>    
    @endif
    @endcan

    @can('locations-edit')
    @if($object->type_of_location!='11')
    <li>
        <a class="btn-edit" href="javascript:void(0);" id="{{$object->id}}" title="@lang('messages.modules.button_save')" onclick="copy_location('{{$object->id}}');"><span class="icon-moon icon-save"></span>
        </a>
    </li>
    @endif
    @endcan    
    <!--for inline edit-->
    <input type="hidden" id="hid_not_allowed_edit_delete_status_{{$object->id}}" value="{{$object->not_allowed_edit_delete_status}}">
    <input type="hidden" id="hid_location_type_{{$object->id}}" value="{{$object->type_of_location}}">
    <input type="hidden" id="hid_case_pack_{{$object->id}}" value="{{$object->case_pack}}">
    <input type="hidden" id="hid_length_{{$object->id}}" value="{{$object->length}}">
    <input type="hidden" id="hid_width_{{$object->id}}" value="{{$object->width}}">
    <input type="hidden" id="hid_height_{{$object->id}}" value="{{$object->height}}">
    <input type="hidden" id="hid_cbm_{{$object->id}}" value="{{$object->cbm}}">
    <input type="hidden" id="hid_stor_weight_{{$object->id}}" value="{{$object->storable_weight}}">
     <input type="hidden" id="hid_carton_id_{{$object->id}}" value="{{$object->carton_id}}">
    <!--for inline edit end-->
    <!--for copy data-->
    <input type="hidden" id="copy_order_column" value="{{$myparam['order_column']}}">
    <input type="hidden" id="copy_order_dir" value="{{$myparam['order_dir']}}">
    <input type="hidden" id="copy_search" value="{{$myparam['search']}}">
    <input type="hidden" id="copy_advance_search" value="{{ serialize($myparam['advance_search'])}}">
    <!--for copy data end-->
</ul>