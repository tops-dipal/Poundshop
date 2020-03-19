<table width="100%">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 5px">
                <tr>
                    <td width="30%">
                        <p style="line-height: 1;font-size: 13px; margin:0 0 5px 0; font-weight: 600;">@lang('messages.po_pdf.bill_to'):</p>
                        <p style="font-size: 12px; margin:0 0 0 0;">
                            {{config('app.name')}}<br/>
                            {{$purchaseOrder->billing_street_address1}},{{$purchaseOrder->billing_street_address2}},<br/>
                            {{$purchaseOrder->billing_city}}-{{$purchaseOrder->billing_zipcode}}<br/>
                            {{$purchaseOrder->billing_state}}, {{$purchaseOrder->billing_country}}
                        </p>
                        <p style="line-height: 1; font-size: 13px; margin:10px 0 5px 0; font-weight: 600;">@lang('messages.po_pdf.ship_to'):</p>
                        <p style="font-size: 12px; margin:0 0 10px 0;">
                            {{config('app.name')}}<br/>
                            {{$purchaseOrder->warehouse}}<br/>
                            {{$purchaseOrder->street_address1}},{{$purchaseOrder->street_address2}},<br/>
                            {{$purchaseOrder->city}}-{{$purchaseOrder->zipcode}}<br/>
                            {{$purchaseOrder->state}}, {{$purchaseOrder->country}}
                        </p>

                    </td>
                    <td width="2%"></td>

                    <td width="68%" style="vertical-align: top;">
                        <table cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #000; font-size: 12px;">
                            <tr>
                                <td style="width: 50%; border-right: 1px solid #000;padding: 10px; vertical-align: top">
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.purchase_order.form.supplier') :</strong>
                                        {{ isset($purchaseOrder->supplier->name)?$purchaseOrder->supplier->name :''}}
                                    </p>
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.purchase_order.form.supplier_contact') : </strong>
                                        {{ isset($purchaseOrder->supplierContact->name)?$purchaseOrder->supplierContact->name:''}}
                                    </p>
                                    @if($purchaseOrder->po_import_type == 2)
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.purchase_order.country') : </strong>
                                        {{ isset($purchaseOrder->wareHouse->getCountry->name)?$purchaseOrder->wareHouse->getCountry->name:'' }}
                                    </p>
                                    @endif
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.po_pdf.buy_name'): </strong>
                                    </p>
                                </td>
                                <td style="width: 50%; padding: 10px; vertical-align: top;">
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">Page:</strong>
                                        {{$pageNo}} of {{ceil($totalRecord/$perPage)}}
                                    </p>

                                    @if(isset($purchaseOrder->po_updated_at) && !empty($purchaseOrder->po_updated_at))
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.po_pdf.update_po'):</strong>
                                        {{$purchaseOrder->po_updated_at}}
                                    </p>
                                    @endif                                    
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.po_pdf.supp_ord_num'):</strong>
                                        {{$purchaseOrder->supplier_order_number}}
                                    </p>
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.po_pdf.ord_date'):</strong>
                                        {{$purchaseOrder->po_date}}
                                    </p>
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.po_pdf.exp_del_date'):</strong>
                                        {{$purchaseOrder->exp_deli_date}}
                                    </p>
                                    @if($purchaseOrder->po_import_type == 2)
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.purchase_order.form.incorterms') :</strong>
                                        {{$purchaseOrder->incoterms}}
                                    </p>
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.purchase_order.form.mode_of_shipment') : </strong>{{array_search($purchaseOrder->mode_of_shipment,config('params.shippment'))}}
                                    </p>
                                    @endif

                                </td>
                            </tr>
                        </table>
                        @if(!empty($purchaseOrder->supplier_comment))
                        <table cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #000; font-size: 12px;">
                            <tr>
                                <td style="width: 20%; border-right: 1px solid #000;padding: 10px; vertical-align: top">
                                    <p style="margin:0 0 3px 0;">
                                        <strong style="font-weight: 600;">@lang('messages.po_pdf.supplier_comments'): </strong>
                                    </p>
                                </td>
                                <td style="width: 80%; padding: 10px; vertical-align: top;">
                                    <p style="margin:0 0 3px 0;">
                                        {{$purchaseOrder->supplier_comment}}
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>