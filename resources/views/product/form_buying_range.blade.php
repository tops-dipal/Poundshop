 <!-- Buying Range -->
 @php
    $sub_active_tab = !empty($sub_active_tab) ? $sub_active_tab : '#buying-search-range';
 @endphp
@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
<ul class="nav nav-tabs inner-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link buying-range-child {{$sub_active_tab == '#buying-search-range' ? 'active' : "" }}" data-toggle="tab" href="#buying-search-range" role="tab" aria-controls="buying-search-range" >
            @lang('messages.inventory.search_range')
        </a>
    </li>
     <li class="nav-item">
        <a class="nav-link buying-range-child {{$sub_active_tab == '#buying-view-range' ? 'active' : "" }}" data-toggle="tab" href="#buying-view-range" role="tab" aria-controls="buying-view-range">
            @lang('messages.inventory.view_range')
        </a>
    </li>
</ul>    

<div class="tab-content pt-4">
    
    <input type="hidden" name="buying_category_id" value="{{ !empty($result->buying_category_id) ? $result->buying_category_id : '' }}">

    <input type="hidden" name="sku" value="{{ !empty($result->sku) ? $result->sku : get_sku() }}">

    <input type="hidden" id="sel_buying_range_parent_ids" value="{{$sel_buying_range_parent_ids}}">

    <div class="tab-pane fade show buying-range-child {{$sub_active_tab == '#buying-search-range' ? 'active' : "" }}" role="tabpanel" id="buying-search-range" aria-labelledby="buying-search-range">
        @include('product.form_buying_search_range')
    </div>

    <div class="tab-pane fade show buying-range-child {{$sub_active_tab == '#buying-view-range' ? 'active' : "" }}" role="tabpanel" id="buying-view-range" aria-labelledby="buying-view-range">
            @include('product.form_buying_view_range')
    </div>
</div>  
   


