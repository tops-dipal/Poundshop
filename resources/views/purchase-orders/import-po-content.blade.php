<div class="container-info po-item-data" style="@if(!empty($purchaseOrder->product) && @count($purchaseOrder->product) > 0 ) dispay:block; @else display:none; @endif">
    <h3 class="title">@lang('messages.purchase_order.items.tables.container_info')</h3>
    <div class="form">
        <div class="form-field">
            <label class="custom-lbl">@lang('messages.purchase_order.items.tables.delivery_charge') (@lang('messages.common.pound_sign')) </label>
            <input type="text"  value="{{$purchaseOrder->total_delivery_charge}}" type="text" id="total_delivery" name="total_delivery" maxlength="8" oninput="calculateCostPerCube(this)" />
        </div>
        <div class="form-field">
            <label class="custom-lbl">@lang('messages.purchase_order.items.tables.tot_space')</label>
            <input maxlength="10" value="{{$purchaseOrder->total_space}}" type="text" id="total_space" name="total_space" oninput="calculateCostPerCube(this)" />
        </div>
        <div class="form-field">
            <label class="custom-lbl">@lang('messages.purchase_order.items.tables.cost_cube') (@lang('messages.common.pound_sign'))</label>
            <span id="cost_per_cube">{{$purchaseOrder->cost_per_cube}}</span>
        </div>
        <div class="form-field">
            <label class="custom-lbl">@lang('messages.purchase_order.items.tables.tot_cube')</label>
            <span id="overall_total_no_cubes">{{$purchaseOrder->total_number_of_cubes}}</span>
        </div>
        <div class="form-field">
            <label class="custom-lbl">@lang('messages.purchase_order.items.tables.space_remaining')</label>
            <span id="remaining_space" >{{$purchaseOrder->remaining_space}}</span>
        </div>


    </div>
</div>

<div class="table-responsive">
    <table id="po-items-table" class="table custom-table cell-align-top">
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
                            </div>
                        </div>
                    </div>
                </td>
                <td class="w-20">@lang('messages.purchase_order.items.tables.product_information')</td>
                <td class="w-8">@lang('messages.purchase_order.items.tables.barcode')</td>
                <td class="w-17">@lang('messages.purchase_order.items.tables.box_detail')</td>
                <td class="w-10">@lang('messages.purchase_order.items.tables.unit_price')</td>
                <td class="w-10">@lang('messages.purchase_order.items.tables.total_pro_cost')</td>
                <td class="w-15">@lang('messages.purchase_order.items.tables.vat_duty_rate')</td>
                <td class="w-13">@lang('messages.purchase_order.items.tables.sel_info')</td>
                <td align="center">@lang('messages.purchase_order.items.tables.action')</td>
            </tr>
        </thead>
        <tbody id="po-items-container">
            @include('purchase-orders._import-po-items')
        </tbody>

        <tfoot class="po-item-data" style="@if(!empty($purchaseOrder->product) && @count($purchaseOrder->product) > 0 ) dispay:block; @else display:none; @endif">

            <tr>
                <td colspan="3"></td>
                <td>
                    <span class="title">@lang('messages.purchase_order.items.tables.sub_total') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td colspan="5" align="left">
                    <span class="desc" id="sub_total">{{priceFormate($purchaseOrder->sub_total)}}</span>
                </td>
            </tr>

            <tr>
                <td colspan="3"></td>
                <td>
                    <span class="title">@lang('messages.purchase_order.items.tables.import_duty') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td colspan="5" align="left">
                    <span class="desc" id="overall_import_duty" >{{$purchaseOrder->total_import_duty}}</span>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td>
                    <span class="title">@lang('messages.purchase_order.items.tables.delivery_charge') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td colspan="5" align="left">
                    <span class="desc" id="overall_total_delivery" >{{$purchaseOrder->total_delivery_charge}}</span>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td>
                    <span class="title">@lang('messages.purchase_order.items.tables.total_cost') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td colspan="5" align="left">
                    <span class="desc" id="total_cost" >{{priceFormate($purchaseOrder->total_cost)}}</span>
                </td>
            </tr>

            <tr>
                <td colspan="3"></td>
                <td>
                    <span class="title">@lang('messages.purchase_order.items.tables.margin')</span>
                </td>
                <td colspan="5" align="left">
                    <span class="desc" id="total_margin" >{{$purchaseOrder->total_margin}}%</span>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td>
                    <span class="title">@lang('messages.purchase_order.items.tables.supplier_amount') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td colspan="5" align="left">
                    <span class="desc" id="supplier_min_amount">{{isset($purchaseOrder->supplier->min_po_amt) ? priceFormate($purchaseOrder->supplier->min_po_amt) : 0 }}</span>
                </td>
            </tr>

            <tr>
                <td colspan="3"></td>
                <td>
                    <span class="title">@lang('messages.purchase_order.items.tables.remaining_amount') (@lang('messages.common.pound_sign'))</span>
                </td>
                <td colspan="5" align="left">
                    @php $min_amount = isset($purchaseOrder->supplier->min_po_amt) ? $purchaseOrder->supplier->min_po_amt : 0; @endphp
                    <span class="desc" id="remaining_amount">@if($purchaseOrder->sub_total > $min_amount) 0.00 @else {{priceFormate($min_amount-$purchaseOrder->sub_total)}}  @endif</span>
                </td>
            </tr>

        </tfoot>
    </table>
</div>