<div class="table-responsive">
    <table id="po-items-table" class="table custom-table cell-align-top table-striped table_fix_header">
        <thead>
            <tr>
                <td style="width: 50px;">
                    <div class="d-flex">
                        <label class="fancy-checkbox">
                            <input type="checkbox" name="ids[]"  class="po_item_master">
                            <span><i></i></span>
                        </label>
                        <div class="dropdown bulk-action-dropdown">
                            <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                                <span class="icon-moon icon-Drop-Down-1"/>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                <a class="btn btn-add delete-many" title="@lang('messages.purchase_order.items.del_items')">
                                    <span class="icon-moon red icon-Delete"></span>
                                    @lang('messages.purchase_order.items.del_items')
                                </a>
                                <button type="button" class="btn btn-move-to-new-po" id="addToPoButton" onclick="MoveToNewPO()" title="@lang('messages.purchase_order.move_to_new_po')"><span class="icon-moon red icon-Purchse-Order"></span>@lang('messages.purchase_order.move_to_new_po')</button>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="w-23">@lang('messages.purchase_order.items.tables.product_information')</td>
                <td class="w-8">@lang('messages.purchase_order.items.tables.barcode')</td>
                <td class="w-18">@lang('messages.purchase_order.items.tables.box_detail')</td>
                <td class="w-10">@lang('messages.purchase_order.items.tables.unit_price') </td>
                <td class="w-10">@lang('messages.purchase_order.items.tables.total_pro_cost')</td>
                <td class="w-15">@lang('messages.purchase_order.items.tables.vat_rate')</td>
                <td class="w-14">@lang('messages.purchase_order.items.tables.sel_info')</td>
                <td align="center">@lang('messages.purchase_order.items.tables.action')</td>
            </tr>
        </thead>
        <tbody id="po-items-container">
            @include('purchase-orders._uk-po-items')
        </tbody>

        <tfoot class="po-item-data" style="@if(!empty($purchaseOrder->product) && @count($purchaseOrder->product) > 0 ) dispay:block; @else display:none; @endif" >

            <tr>
                <td colspan="5" align="right">
                    <span class="title">@lang('messages.purchase_order.items.tables.sub_total') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td colspan="4" align="left">
                    <span class="desc" id="sub_total">{{priceFormate($purchaseOrder->sub_total)}}</span>                                </td>
            </tr>
            <tr>
                <td colspan="5" align="right">
                    <span class="title">@lang('messages.purchase_order.items.tables.margin')</span>
                </td>
                <td colspan="4" align="left">
                    <span class="desc" id="total_margin" >{{floatval($purchaseOrder->total_margin)}}%</span>
                </td>
            </tr>
            <tr class="remainingDiv" style="@if($purchaseOrder->sub_total >= $purchaseOrder->supplier->min_po_amt) display: none; @endif">
                <td colspan="5" align="right">
                    <span class="title">@lang('messages.purchase_order.items.tables.supplier_amount') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td  colspan="4" >
                    <span class="desc" id="supplier_min_amount">{{isset($purchaseOrder->supplier->min_po_amt) ? priceFormate($purchaseOrder->supplier->min_po_amt) : 0 }}</span>
                </td>
            </tr>
            <tr class="remainingDiv" style="@if($purchaseOrder->sub_total >= $purchaseOrder->supplier->min_po_amt) display: none; @endif">
                <td colspan="5" align="right">
                    <span class="title">@lang('messages.purchase_order.items.tables.remaining_amount') <span class="color-blue">(@lang('messages.common.pound_sign'))</span></span>
                </td>
                <td  colspan="4">
                    @php $min_amount = isset($purchaseOrder->supplier->min_po_amt) ? $purchaseOrder->supplier->min_po_amt : 0; @endphp
                    <span class="desc color-red" id="remaining_amount">@if($purchaseOrder->sub_total > $min_amount) 0.00 @else {{priceFormate($min_amount-$purchaseOrder->sub_total)}}  @endif</span>
                </td>
            </tr>

        </tfoot>
    </table>
</div>

