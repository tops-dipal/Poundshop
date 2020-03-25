<thead>
    <tr>
        @php
        $sort_icon = "sorting";

        if(!empty($params) && $params['sortBy'] == 'title' && $params['sortDirection'] == 'asc')
        {
            $sort_icon = "sorting_asc";
            $nxt_sort_dir = "desc";
        }
        elseif(!empty($params) &&  $params['sortBy'] == 'title' && $params['sortDirection'] == 'desc')
        {
            $sort_icon = "sorting_desc";
            $nxt_sort_dir = "asc";
        }
        else
        {
            $nxt_sort_dir = "asc";
        }
        @endphp
        <td class="w-30 {{$sort_icon}}" sort-by="title"  sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">@lang('messages.replen.product_info')</td>

        @php
        $sort_icon = "sorting";

        if(!empty($params) && $params['sortBy'] == 'priority' && $params['sortDirection'] == 'asc')
        {
            $sort_icon = "sorting_asc";
            $nxt_sort_dir = "desc";
        }
        elseif(!empty($params) && $params['sortBy'] == 'priority' && $params['sortDirection'] == 'desc')
        {
            $sort_icon = "sorting_desc";
            $nxt_sort_dir = "asc";
        }else{
            $nxt_sort_dir = "asc";
        }
        @endphp
        <td class="w-10 {{$sort_icon}}" sort-by="priority" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">@lang('messages.replen.priority')</td>
        
        @php
        $sort_icon = "sorting";

        if(!empty($params) && $params['sortBy'] == 'aisle' && $params['sortDirection'] == 'asc')
        {
            $sort_icon = "sorting_asc";
            $nxt_sort_dir = "desc";
        }
        elseif(!empty($params) && $params['sortBy'] == 'aisle' && $params['sortDirection'] == 'desc')
        {
            $sort_icon = "sorting_desc";
            $nxt_sort_dir = "asc";
        }
        else
        {
            $nxt_sort_dir = "asc";
        }
        @endphp
        <td class="w-10 {{$sort_icon}}" sort-by="aisle" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">@lang('messages.replen.aisle')</td>

        @php
        $sort_icon = "sorting";

        if(!empty($params) && $params['sortBy'] == 'location' && $params['sortDirection'] == 'asc')
        {
            $sort_icon = "sorting_asc";
            $nxt_sort_dir = "desc";
        }
        elseif(!empty($params) && $params['sortBy'] == 'location' && $params['sortDirection'] == 'desc')
        {
            $sort_icon = "sorting_desc";
            $nxt_sort_dir = "asc";
        }
        else
        {
            $nxt_sort_dir = "asc";
        }
        @endphp
        <td class="w-10 {{$sort_icon}}" sort-by="location" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">@lang('messages.replen.locations')</td>
        
        <td class="w-10"></td>
    </tr>
</thead>
@if(!empty($productData))
    <tbody>
    @foreach($productData as $product)    
        <tr>
            <td class="w-35 border-none">
                <p class="product-title font-14-dark bold mb-2">{{!empty($product->title)?$product->title:''}}</p>
                <div class="d-flex mr-product-detail pl-0">                    
                    @if (!empty($product->main_image_internal_thumb))
                    <a href="{{url('/storage/uploads') . '/'.$product->main_image_internal_thumb}}" data-rel="lightcase">
                        <img src="{{url('/storage/uploads') . '/'.$product->main_image_internal_thumb}}" width="80" height="80" alt="">
                    </a>
                    @else
                    <a href="{{url('/img/no-image.jpeg')}}" data-rel="lightcase">
                        <img src="{{url('/img/no-image.jpeg')}}" width="80" height="80" alt="">
                    </a>

                    @endif

                    <div class="ml-2">
                        <div class="group-item mt-3">
                            <p class="title font-12-dark mb-1">@lang('messages.replen.barcode'): {{ !empty($product->product_identifier)?$product->product_identifier:''}}</p>
                            <p class="title font-12-dark mb-1">@lang('messages.replen.sku'): {{ !empty($product->sku)?$product->sku:'' }}</p>
                            <p class="title font-12-dark mb-1">@lang('messages.replen.supp_sku'): {{ !empty($product->supplier_sku)?$product->supplier_sku:'' }}</p>
                        </div>
                    </div>
                </div>
            </td>
            <td class="w-10 border-none">
                <span class="font-14-dark bold d-inline-block p-2 alert alert-warning">{{ !empty($product->priority)?priorityTypes($product->priority):''}}</span>
            </td>
            <td class="w-10 border-none">
                <span class="font-14-dark bold d-inline-block p-2 alert alert-warning">{{ !empty($product->aisle)?$product->aisle:''}}</span>
            </td>
            <td class="w-25 border-none">
                <span class="font-14-dark bold d-inline-block p-2 alert alert-warning">{{ !empty($product->location)?$product->location:''}} - {{ !empty($product->type_of_location)?LocationType($product->type_of_location):''}}</span>
            </td>            
            <td class="w-10 border-none">
                @php
                $href='javascript:void(0)';                
                if(!empty($pallet_pick_location) && !empty($pallet_pick_location->toArray()))
                {
                    $href=route('replen.edit',$product->id);
                }
                @endphp
                <!-- <button class="btn btn-blue font-12 bold px-4">@lang('messages.replen.select')</button> -->
                <a class="btn btn-blue font-12 bold px-4" onclick="return check_pallet_select();" href="{{$href}}">@lang('messages.replen.select')</a>
            </td>
        </tr>    
    @endforeach
    </tbody>
@else
<tbody>
        <tr>
            <td colspan="5" class="text-center"> No Records Found </td>
        </tr>
</tbody>
@endif