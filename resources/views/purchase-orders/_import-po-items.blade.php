@if(!empty($purchaseOrder->product) && @count($purchaseOrder->product) > 0 )
@foreach($purchaseOrder->product()->orderBy('id','desc')->get() as $product)

<tr>
    <td id="{{$product->id}}"><div class="d-flex">
            <label class="fancy-checkbox">
                <input name="ids[]" type="checkbox" class="child-checkbox" value="{{$product->product_id}}"
                       />
                <span><i></i></span>
            </label>
        </div>
    </td>
    <td>
        <div class="d-flex group-item">
            <span class="title color-light w-80">@lang('messages.purchase_order.items.tables.sku')</span>
            <span class="desc color-blue">{{isset($product->products->sku) ? $product->products->sku : '--'}}</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.title')</span>
            <span class="desc color-light po-product-title" title="{{isset($product->products->title) ? $product->products->title : '--'}}">{{isset($product->products->title) ? $product->products->title : '--'}}</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.status')</span>
            <span class="desc color-blue">{{isset($product->is_new_product) ? config('params.product_listed.'.$product->is_new_product) : '--'}}</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.supplier_sku')</span>
            <span class="desc">
                <input type="text" class="supplier_sku po_textbox color-blue w-120" name="supplier_sku_{{$product->product_id}}" maxlength="15" value="{{$product->supplier_sku}}" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.best_before')</span>
            <span class="desc">
                <input type="text" value="{{$product->best_before_date}}"  name="best_before_date_{{$product->product_id}}" class="best_before_date po_textbox w-120" autocomplete="off" />
            </span>
        </div>

    </td>
    <td>

        <input  data-barcode="{{$product->barcode}}"   maxlength="20"  type="text" name="barcode_{{$product->product_id}}" class="barcode po_textbox input-barcode" value="{{$product->barcode}}" />
        <div class="d-flex group-item mt-3">
            <span class="desc">
                <label class="fancy-checkbox">
                    <input type="checkbox" class="variant" name="variant" @if($product->is_variant ==1) checked="checked" @endif>
                           <span><i></i>@lang('messages.purchase_order.items.tables.variant')</span>

                </label>
            </span>
        </div>

    </td>
    <td>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.qty_per_box')</span>
            <span class="desc">
                <input value="{{$product->qty_per_box}}"  maxlength="8"   type="text" name="qty_per_box_{{$product->product_id}}" class="qty_per_box po_textbox" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.total_box')</span>
            <span class="desc">
                <input value="{{$product->total_box}}"  maxlength="8"  type="text" name="total_box_{{$product->product_id}}" class="total_box po_textbox"  />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.total_qty')</span>
            <span class="desc">
                <input value="{{$product->total_quantity}}"   maxlength="9"  type="text" name="total_quantity_{{$product->product_id}}" class="total_quantity po_textbox" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.cube_per_box')</span>
            <span class="desc">
                <input value="{{$product->cube_per_box}}"  maxlength="8"  type="text" name="cube_per_box_{{$product->product_id}}" class="cube_per_box po_textbox" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.total_cubes')</span>
            <span class="desc">
                <input value="{{$product->total_num_cubes}}"  maxlength="9"  type="text" name="total_num_cubes_{{$product->product_id}}" readonly="readonly" class="total_num_cubes po_textbox" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60"></span>
            <span class="desc">

            </span>
        </div>
    </td>
    <td>
        <div class="position-relative">
            <span class="pound-sign">@lang('messages.common.pound_sign')</span>
            <input value="{{$product->unit_price}}"  type="text" maxlength="9" name="unit_price_{{$product->product_id}}" class="unit_price po_textbox"  />
        </div>

    </td>
    <td>
        <span class="title">@lang('messages.common.pound_sign')</span>
        <span name="total_product_cost_{{$product->product_id}}" class="title total_product_cost po_textbox">   {{priceFormate($product->unit_price*$product->total_quantity)}}
        </span>
    </td>
    <td>

        @if($product->vat_type == 0)
        <div class="d-flex group-item">
            <span class="title w-80" >@lang('messages.purchase_order.items.tables.vat')</span>
            <span class="desc vat" data-value="{{$product->vat}}">{{$product->vat}}%</span>
        </div>
        @elseif($product->vat_type == 1)
        <div class="d-flex group-item">
            <span class="title w-80" >@lang('messages.purchase_order.items.tables.vat')</span>
            <span class="vat desc" data-value="{{$product->vat}}">{{$product->vat}}%</span>
        </div>
        @else
        <div class="d-flex group-item">
            <span class="title w-60" >@lang('messages.purchase_order.items.tables.std_rate')</span>
            <span class="desc"><input type="text" class="standard_rate po_textbox" value="{{$product->standard_rate_value}}"  oninput="calculateMixRate(this)" /></span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60" >@lang('messages.purchase_order.items.tables.zero_rate')</span>
            <span class="desc"><input type="text" class="zero_rate" value="{{$product->zero_rate_value}}" /></span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80" >@lang('messages.purchase_order.items.tables.vat')</span>
            <span class="vat desc" data-value="{{$product->vat}}">{{$product->vat}}%</span>
        </div>

        @endif


        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.import_duty')</span>
            <span class="desc">
                <span class="import_duty" data-value="{{$product->import_duty}}" >{{floatval($product->import_duty)}}%</span>
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.tot_del_charge')</span>
            <span class="desc">
                <div class="d-flex align-items-center">@lang('messages.common.pound_sign') <span class="total_delivery_charge"  >{{$product->total_delivery_charge}}</span></div>
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.landed_product_cost')</span>
            <span class="desc">
                <span class="landed_product_cost"  >{{$product->landed_product_cost}}</span>
            </span>
        </div>
    </td>
    <td>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.expected')</span>
            <span class="desc">
                <input type="text" readonly="readonly"  value="{{$product->expected_mros}}"  name="expected_mros_{{$product->product_id}}" class="expected_mros po_textbox w-60" maxlenth="5" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.selling_qty')</span>
            <span class="desc">
                <input type="text" value="{{$product->sel_qty}}"  name="sel_qty_{{$product->product_id}}" class="sel_qty po_textbox w-60" maxlenth="5" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.selling_price')</span>
            <span class="desc">
                <div class="position-relative">
                    <span class="pound-sign">@lang('messages.common.pound_sign')</span>
                    <input type="text" value="{{$product->sel_price}}"  name="sel_price_{{$product->product_id}}" class="sel_price po_textbox w-60" maxlength="9" />
                </div>
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.mros')</span>
            <span class="desc">
                <input readonly="readonly" type="text" value="{{$product->mros}}"  name="mros_{{$product->mros}}" class="mros po_textbox w-60" maxlength="9" />
            </span>
        </div>
    </td>
    <td align="center">
        <input type="hidden" class="vat_type" value="{{$product->vat_type}}" />
        <input value="{{$product->vat_in_amount}}" type="hidden" class="vat_in_amount" />
        <input type="hidden" value="{{$product->import_duty_in_amount}}" class="import_duty_in_cost" />
        <input value="{{$product->itd_vat}}" type="hidden" class="totalProductCostImportDutyDeliveryCharge" />
        <input value="{{$product->total_vat}}" type="hidden" class="total_vat" />
        <input value="{{$product->currency_exchange_rate}}" type="hidden" class="currency_exchange_rate" />
        <input value="{{$product->total_net_selling_price}}" type="hidden" class="landed_price_in_pound" />
        <input value="{{$product->total_quantity}}" type="hidden" class="total_net_selling_price" />
        <input value="{{$product->net_selling_price_excluding_vat}}" type="hidden" class="gross_sel_price_exc_vat" />
        <input value="{{$product->total_net_profit}}" type="hidden" class="total_net_profit" />
        <input value="{{$product->total_net_margin}}" type="hidden" class="total_net_margin" />
        <a style="@if($purchaseOrder->po_status < 6) display: block; @else display: none; @endif" href="javascript:;" data-id="{{$product->id}}" class="removeRow"><span class="icon-moon icon-Delete"></span></a>
    </td>
</tr>
@endforeach
@endif