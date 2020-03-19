<h3 class="title">@lang('messages.range_management.range_name')</h3>
                    <input type="hidden" name="process" id="process" value="{{ $process }}">
                    <input type="hidden" name="level" id="level" value="1">
                    <input type="hidden" name="childList" class="childList" id="childList" value="{{ route('get-child-list') }}">
                    <div class="parent_child_relation category-tree-view">
                        
                        <ul>                            
                            @forelse($parent as $parentKey=>$parentVal)
                            <li>
                                <div class="parent-category">
                                    @if($parentVal['child_status'])
                                    <a id="expandParent_{{ $parentVal['id'] }}" href="javascript:void(0);" class="expand" attr-child-nodes="{{ !empty($parentVal['children']) ? json_encode($parentVal['children']) : '' }}">+</a>
                                    @else
                                    <a id="expandParent_{{ $parentVal['id'] }}" href="javascript:void(0);" class="expand disabled"></a>
                                    @endif
                                    <span class="name"> <a id="editParent_{{ $parentVal['id'] }}"  class="edit-btn" title="Edit Category">{{ $parentVal['category_name'] }}</a></span>
                                   
                                    <div class="category-action">
                                         <span class="name"> {{ ($parentVal['seasonal_status']==1) ? 'Seasonal' : '' }}</span>
                                        <input type="hidden" class="editParent_{{ $parentVal['id'] }}" value="{{route('range.edit',$parentVal['id'])}}">
                                        <a class="btn btn-blue edit-btn" id="editParent_{{ $parentVal['id'] }}">
                                            @lang('messages.range_management.edit')
                                        </a>
                                        <a href="#" id="deleteParent_{{ $parentVal['id'] }}" class="btn btn-red btn-delete">
                                            @lang('messages.range_management.delete')
                                        </a>
                                    </div>
                                </div>
                                <div class="expandChildList_{{ $parentVal['id'] }}"></div>
                            </li>
                            @empty
                             <li>
                                <div class="parent-category">
                                    <span>No Records Found</span>
                                </div>
                            </li>
                            @endforelse
                            
                        </ul>
                        
                    </div>