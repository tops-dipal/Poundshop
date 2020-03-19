<ul class="child-ul">
@forelse($childCategoryList as $childCatKey=>$childCatVal)
<li>
	<div class="parent-category" id="parentCatId_{{ $childCatVal->parent_id }}">
        @if($childCatVal->child_status)
        <a id="expandParent_{{ $childCatVal->id }}" href="javascript:void(0);" class="expand">+</a>
        @endif
        <span class="name"><a id="editParent_{{ $childCatVal->id }}"  class="edit-btn" title="Edit Category">{{ $childCatVal->category_name }}</a></span>
        <div class="category-action">
        	<input type="hidden" class="editParent_{{ $childCatVal->id }}" value="{{route('range.edit',$childCatVal->id)}}">
	        <a  id="editParent_{{ $childCatVal->id }}"  class="btn btn-blue edit-btn" >
	        	@lang('messages.range_management.edit')
	        </a>
	        <a href="#" id="deleteParent_{{ $childCatVal->id }}" class="btn btn-red btn-delete">
	        	@lang('messages.range_management.delete')
	        </a>
	    </div>
        
    </div>
    <div class="expandChildList_{{ $childCatVal->id }}">
    </div>
</li>
@empty
@endforelse
</ul>