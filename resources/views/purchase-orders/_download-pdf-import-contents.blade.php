<table cellpadding="5" cellspacing="0" width="100%" border="1" style="margin-top: 5px;border-collapse: collapse;">
    <thead style="font-size: 11px; font-weight: 600">
        <tr>                                    
            <td width="22%">@lang('messages.purchase_order.items.tables.product_information')</td>
            <td width="15%">@lang('messages.purchase_order.items.tables.title')</td>
            <td width="8%" style="text-align: center;">@lang('messages.po_pdf.total_qty')</td>
            <td width="8%" style="text-align: center;">@lang('messages.po_pdf.qty_per_box')</td>
            <td width="8%" style="text-align: center;">@lang('messages.purchase_order.items.tables.total_box')</td>
            <td>@lang('messages.purchase_order.items.tables.cube_detail')</td>
            <td>@lang('messages.purchase_order.items.tables.unit_price')</td>
            <td>@lang('messages.purchase_order.items.tables.total_pro_cost')</td>
            <td>@lang('messages.purchase_order.items.tables.vat_rate')</td>
        </tr>
    </thead>
    @if(!empty($purchaseOrder->product) && @count($purchaseOrder->product) > 0 )
    <tbody style="font-size: 10px;">
        @if(!empty($purchaseOrder->product) && @count($purchaseOrder->product) > 0 )
        @foreach($purchaseOrder->product as $product)

        <tr>                                    
            <td>
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.sku') :</strong> {{$product->products->sku}}
                </p>                                        
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.supplier_sku') :</strong>
                    {{$product->supplier_sku}}</p>
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.barcode'): </strong>{{$product->barcode}}</p>
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.best_before') : </strong>{{$product->best_before_date}}</p>
            </td>
            <td>{{$product->products->title}}</td>
            <td style="text-align: center;">{{$product->total_quantity}}</td>
            <td style="text-align: center;">{{$product->qty_per_box}}</td>
            <td style="text-align: center;">{{$product->total_box}}</td>
            <td>
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.po_pdf.cube_per_box')</strong>
                    <p style="margin: 0">{{$product->cube_per_box}}</p>
                </p>
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.po_pdf.cube_total')</strong>
                    <p style="margin: 0">{{$product->total_num_cubes}}</p>
                </p>
            </td>
            <td style="text-align: right;">{{$product->unit_price}}</td>
            <td style="text-align: right;">{{floatval($product->unit_price*$product->total_quantity)}}</td>
            <td>
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.vat')</strong>
                    <p style="margin: 0">{{$product->vat}}%</p>
                </p>
                <p style="margin:0 0 3px 0;">
                    <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.import_duty')</strong>
                    <p style="margin: 0">{{$product->import_duty}}%</p>
                </p>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
    <tfoot style="font-size: 12px;">
        <tr>
            <td colspan="7" align="right">
                <span class="title">
                    <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.sub_total')
                    </strong>
                </span>
            </td>
            <td colspan="1" align="right">
                <span class="desc">{{$purchaseOrder->sub_total}}</span>
            </td>
            <td></td>
        </tr>                               
    </tfoot>
    @else
    <tr>
        <td>@lang('messages.common.no_records_found')</td>
    </tr>
    @endif
</table>