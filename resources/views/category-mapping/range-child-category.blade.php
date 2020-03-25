 @if(count($child)>0)
 <div class="form-group" id="{{ $selected_parent }}" data-sort="{{ $selected_parent }}">                                        
    <select class="form-control parent_id" name="range_cat_id">
    <option value="">--@lang('messages.user_management.select') @lang('messages.range_management.parent_cat')--</option>
    @forelse($child as $childKey=>$childVal)
        <option value="{{ $childVal['id'] }}" attr-child-nodes="{{ json_encode($childVal['children']) }}" {{ in_array($childVal['id'],$parentIds) ? "selected='selected'":'' }} >{{ $childVal['category_name'] }}</option>
    @empty 
    @endforelse
    </select>                                        
    </div>
    @endif