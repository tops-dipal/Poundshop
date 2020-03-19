@if(count($child)>0)
<div class="form-group row" id="{{ $parent_id }}" data-sort='{{ $parent_id }}'>
    <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.range_management.child_cat')</label>
   
    <div class="col-lg-8">
       <select class="form-control parent_id" name="parent_id">
          @if($process!='edit')
            <option value="">--@lang('messages.user_management.select') @lang('messages.range_management.child_cat')--</option>
            @forelse($child as $childKey=>$childVal)
                <option value="{{ $childVal->id }}" attr-child-nodes="{{ json_encode($childVal->children) }}">{{ $childVal->category_name }}</option>
           @empty
           @endforelse
          @else
             <option value="">--@lang('messages.user_management.select') @lang('messages.range_management.child_cat')--</option>
              @forelse($child as $childKey=>$childVal)
                  <option value="{{ $childVal->id }}" attr-child-nodes="{{ json_encode($childVal->children) }}" @if(in_array($childVal->id,$parentIds)) selected="selected" @endif  >{{ $childVal->category_name }}</option>
             @empty
             @endforelse
          @endif
       </select>
        
    </div>
</div>
@endif