@foreach($revisionData->purchase_order_content['product'] as $product)
<tr>
    <td>
        <div class="d-flex group-item">
            <span class="title color-light w-60">@lang('messages.purchase_order.items.tables.sku')</span>
            <span class="desc color-blue">{{$product['products']['sku']}}</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.title')</span>
            <span class="desc color-light">{{$product['products']['title']}}</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.status')</span>
            <span class="desc">--</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.supplier_sku')</span>
            <span class="desc">
                <span class="desc">{{$product['supplier_sku']}}</span>
            </span>
        </div>
    </td>
    <td>
        <div class="d-flex group-item">
            <span class="desc">
                <span class="desc">{{$product['barcode']}}</span>
            </span>
        </div>
    </td>
    <td>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.qty_per_box')</span>
            <span class="desc">
                <span class="desc">{{$product['qty_per_box']}}</span>
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.total_box')</span>
            <span class="desc">
                <span class="desc">{{$product['total_box']}}</span>
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.total_qty')</span>
            <span class="desc">
                <span class="desc">{{$product['total_quantity']}}</span>
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.unit_price')</span>
            <span class="desc">
                <span class="desc">{{$product['unit_price']}}</span>
            </span>
        </div>
    </td>
    <td>
        <span class="title">{{$product['total_product_cost']}}</span>
    </td>
    <td>
        <span class="title">{{$product['vat']}}%</span>
    </td>
    <td>
        <div class="d-flex group-item">
            <span class="desc">
                <span class="desc">{{$product['best_before_date']}}</span>
            </span>
        </div>

    </td>
    <td>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.expected')</span>
            <span class="desc">
                {{$product['expected_mros']}}
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.selling_qty')</span>
            <span class="desc">
                {{$product['sel_qty']}}
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.selling_price')</span>
            <span class="desc">
                {{$product['sel_price']}}
            </span>
        </div>       
    </td>   
</tr>
@endforeach
