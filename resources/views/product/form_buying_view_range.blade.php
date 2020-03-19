@if(!empty($allRanges))
    <div class="reInitSclick">
        <div class="category-breadcrumbs">
            <ul id="category-breadcrumbs-ul">
                 
            </ul>
         </div>
         <div class="category-list-holder" id="categoryLevelDiv">
            <div class="category-level">
                <ul>
                    @foreach($allRanges as $range)
                    <li>
                         <a href="javascript:void(0)" attr-child-nodes="{{ !empty($range['children']) ? json_encode($range['children']) : '' }}" attr-id="{{$range['id']}}" attr-cat-name="{{ !empty($range['category_name']) ? $range['category_name'] : '' }}" onclick="get_buying_category_nodes(this)">
                            {{ !empty($range['category_name']) ? $range['category_name'] : '' }}

                            @if(!empty($range['children']))
                                <span class="icon-moon icon-Right-Arrow"></span>
                            @endif
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>        
        </div> 

        <div id="magento_range_content">
           @include('product.magento_range_content')
        </div>    
    </div>
@else
    @lang('messages.common.no_records_found')
@endif