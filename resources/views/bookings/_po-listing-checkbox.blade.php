@if($object->po_status != 10)
<div class="d-flex">
    <label class="fancy-checkbox">
        @if(isset($poIds))
        @if(in_array($object->id,$poIds))
        <input name="added_ids[]" type="checkbox" value="{{array_search($object->id, $poIds)}}" class="child-checkbox" checked="checked"/>
        @else
        @if($object->po_status != 5)
        <input name="ids[]" type="checkbox" value="{{$object->id}}" class="child-checkbox"/>
        @endif
        @endif
        @else
        @if($object->po_status != 5)
        <input name="ids[]" type="checkbox" value="{{$object->id}}" class="child-checkbox"/>
        @endif
        @endif

        <span><i></i></span>
    </label>
</div>
@endif
