@if(!empty($purchaseOrder) && @count($purchaseOrder)>0 && @count($purchaseOrder->product)>0)
<html>
    <head>
        <style>
            /**
            Set the margins of the page to 0, so the footer and the header
            can be of the full height and width !
            **/
            @page {
                margin: 0cm 0cm;
            }
            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 0.3cm;
                margin-left: 0.3cm;
                margin-right: 0.3cm;
                margin-bottom: 0.3cm;
                font-family: 'Open Sans', sans-serif;
            }
            .page-break {
                page-break-after: always;
            }
            /** Define the header rules **/
        </style>
    </head>
    <body>
        @php $productsData=$purchaseOrder->product;@endphp
        <!-- Define header and footer blocks before your content -->

        <!-- Wrap the content of your PDF inside a main tag -->

        <main>
            @php $i=0; $perPage=10; $n=$perPage; $totalRecord=count($productsData);  $pageNo=1;@endphp


            @for($j=0;$j< $totalRecord; $j++)

            @if($i==0 || $i%$perPage == 0)
            <header>
                <table width="100%" style="border-bottom: 2px solid #222;">
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 5px;">
                                <tr>
                                    <td width="50%">
                                        <img src="http://topsdemo.co.in/app/poundshop/public/img/logo-new.png" />
                                    </td>
                                    <td width="50%" align="right">
                                        <p style="font-size: 14px; margin: 0 0 5px 0;">

                                            @php isset($purchaseOrder->po_updated_at) && !empty($purchaseOrder->po_updated_at) ? 'Updated ' : ''; @endphp @lang('messages.po_pdf.pur_ord')
                                            : {{ isset($purchaseOrder->po_number)?$purchaseOrder->po_number:'' }}
                                        </p>
                                        <img src="http://www.barcoder.net.au/v/vspfiles/photos/photos/sample-code93.gif" height="30">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </header>
            @include('purchase-orders._download-pdf-header')
            @endif

            @if($i==0 || $i%$perPage == 0)
            <table cellpadding="5" cellspacing="0" width="100%" border="1" style="margin-top: 5px;border-collapse: collapse;">
                <thead style="font-size: 11px; font-weight: 600">
                    <tr>
                        <td width="22%">@lang('messages.po_pdf.product_information')</td>
                        <td width="24%">@lang('messages.po_pdf.pro_title')</td>
                        <td width="8%" style="text-align: center;">@lang('messages.po_pdf.total_qty')</td>
                        <td width="8%" style="text-align: center;">@lang('messages.po_pdf.qty_per_box')</td>
                        <td width="8%" style="text-align: center;">@lang('messages.purchase_order.items.tables.total_box')</td>
                        <td width="10%" style="text-align: right;">@lang('messages.po_pdf.unit_price')</td>
                        <td width="10%" style="text-align: right;">@lang('messages.po_pdf.total_val')</td>
                        <td width="10%" >@lang('messages.po_pdf.vat_rate')</td>
                    </tr>
                </thead>
                @endif


                <tbody style="font-size: 10px;">
                    @for($k=$j;($k<$n) && isset($productsData[$k]); $k++)

                    <tr>
                        <td>
                             <p style="margin:0 0 3px 0;">
                                <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.sku') :</strong> @if(isset($productsData[$k]->products->sku)) {{$productsData[$k]->products->sku}} @endif 
                            </p>
                            <p style="margin:0 0 3px 0;">
                                <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.supplier_sku') : </strong>
                                {{$productsData[$k]->supplier_sku}}</p>
                            <p style="margin:0 0 3px 0;">
                                <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.barcode'):</strong>{{$productsData[$k]->barcode}}</p>
                            <p style="margin:0 0 3px 0;">
                                <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.best_before') : </strong>{{$productsData[$k]->best_before_date}}</p>
                        </td>
                        <td>@if(isset($productsData[$k]->products->title)) {{$productsData[$k]->products->title}} @endif</td>
                        <td style="text-align: center;"><strong style="font-weight: 600;">{{$productsData[$k]->total_quantity}}</strong></td>
                        <td style="text-align: center;">{{$productsData[$k]->qty_per_box}}</td>
                        <td style="text-align: center;">{{$productsData[$k]->total_box}}</td>
                        <td style="text-align: right;">@lang('messages.common.pound_sign'){{priceFormate($productsData[$k]->unit_price)}}</td>
                        <td style="text-align: right;">
                            <strong style="font-weight: 600;">@lang('messages.common.pound_sign'){{priceFormate($productsData[$k]->unit_price*$productsData[$k]->total_quantity)}}</strong>
                        </td>
                        <td>@lang('messages.purchase_order.items.tables.vat') : {{$productsData[$k]->vat}}%</td>
                    </tr>
                    @php $i++; @endphp
                    @endfor
                    @php $j=$k-1;
                    $n=$n+$perPage; @endphp
                </tbody>
                @if($k == $totalRecord)
                <tfoot style="font-size: 12px;">
                    <tr>
                        <td colspan="6" align="right">
                            <span class="title">
                                <strong style="font-weight: 600;">@lang('messages.purchase_order.items.tables.sub_total')
                                </strong>
                            </span>
                        </td>
                        <td colspan="1" align="right">
                            <span class="desc"><strong style="font-weight: 600;">@lang('messages.common.pound_sign'){{priceFormate($purchaseOrder->sub_total)}}</strong></span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
            @php $pageNo++; @endphp
            @if($k !== $totalRecord)
            <div class="page-break"></div>
            @endif
            @endfor
        </main>
    </body>
</html>
@else
@lang('messages.common.no_records_found')
@endif


