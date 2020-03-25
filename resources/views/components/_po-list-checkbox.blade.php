@if($object->po_status == 1)
<div class="d-flex">
    <label class="fancy-checkbox">
        <input name="ids[]" type="checkbox" value="{{$object->id}}" class="child-checkbox">
        <span><i></i></span>
    </label>
</div>
@endif
