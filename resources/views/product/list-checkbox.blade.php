<div class="d-flex variation-product">
    <label class="fancy-checkbox">
        <input name="ids[]" type="checkbox" value="{{$object->id}}" class="child-checkbox">
        <span><i></i></span>
    </label>
    @if($object->product_type == 'parent')
    	<span class="icon-moon icon-Arrow-Down size-sm close" onclick="get_variation(this)"></span>
    @endif
</div>

