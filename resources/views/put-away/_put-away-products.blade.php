<thead>
    <tr>
        @php
        $sort_icon = "sorting";

        if($params['sortBy'] == 'title' && $params['sortDirection'] == 'asc')
        {
        $sort_icon = "sorting_asc";
        $nxt_sort_dir = "desc";
        }
        elseif($params['sortBy'] == 'title' && $params['sortDirection'] == 'desc')
        {
        $sort_icon = "sorting_desc";
        $nxt_sort_dir = "asc";
        }else{
        $nxt_sort_dir = "asc";
        }
        @endphp
        <td class="w-30 {{$sort_icon}}" sort-by="title"  sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Product Info.</td>

        @php
        $sort_icon = "sorting";

        if($params['sortBy'] == 'qty' && $params['sortDirection'] == 'asc')
        {
        $sort_icon = "sorting_asc";
        $nxt_sort_dir = "desc";
        }
        elseif($params['sortBy'] == 'qty' && $params['sortDirection'] == 'desc')
        {
        $sort_icon = "sorting_desc";
        $nxt_sort_dir = "asc";
        }else{
        $nxt_sort_dir = "asc";
        }
        @endphp
        <td class="w-15 {{$sort_icon}}" sort-by="qty" sort-order="{{ !empty($nxt_sort_dir) ? $nxt_sort_dir : ''}}">Pending Put Away Qty.</td>
        <td class="w-10">Aisle.</td>
        <td class="w-10"></td>
    </tr>
</thead>
@foreach($productData as $product)
<tbody>
    <tr>

        <td class="w-30 border-none">
            <p class="product-title font-14-dark bold mb-2">{{$product->title}}</p>
            <div class="d-flex mr-product-detail pl-0">
                @if (!empty($product->main_image_internal_thumb))
                <a href="{{url('/storage/uploads') . '/' .$product->main_image_internal}}" data-rel="lightcase">
                    <img src="{{url('/img/img-loading.gif') }}" data-original="{{url('/storage/uploads') . '/' .$product->main_image_internal_thumb}}"  width="80" height="80" alt="">

                </a>
                @else
                <a href="{{url('/img/no-image.jpeg')}}" data-rel="lightcase">
                    <img src="{{url('/img/img-loading.gif') }}"  data-original="{{url('/img/no-image.jpeg')}}" width="80" height="80" alt=""> </a>

                @endif

                <div class="ml-2">
                    <div class="group-item">
                        <p class="title font-12-dark mb-1">Barcode: {{$product->barcode}}</p>
                        <p class="title font-12-dark mb-1">SKU: {{$product->sku}}</p>
                        <p class="title font-12-dark mb-1">Supplier SKU: {{$product->supplier_sku}}</p>
                        <span class="font-12-dark d-inline-block px-3 py-1 bold alert alert-success">In Stock</span>
                    </div>
                </div>
            </div>
        </td>
        <td class="w-15 border-none">
            <span class="font-14-dark bold d-inline-block p-2 alert alert-warning">{{$product->total_pending_qty}}</span>
        </td>
        <td class="w-10 border-none">
            <p class="font-14-dark bold">-</p>
        </td>
        <td class="w-10 border-none">
            <button class="btn btn-blue font-12 bold px-4 put-away-detail-btn" data-putawaytype="{{$putaway_type}}" data-warehouse="{{$product->warehouse_id}}" data-po="{{$product->po_id}}" data-pallet="{{$params['location']}}" data-searchtext="{{$params['productSearch']}}" data-productid="{{$product->product_id}}" data-booking="{{$product->booking_id}}" data-url="{{route('put-aways.products-detail')}}" >Select</button>
        </td>
    </tr>
</tbody>
@endforeach