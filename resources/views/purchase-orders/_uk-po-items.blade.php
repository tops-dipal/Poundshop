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
                           <span><i></i>
                        @lang('messages.purchase_order.items.tables.variant')</span>

                </label>
            </span>
        </div>
    </td>
    <td>
        <div class="d-flex group-item">
            <span class="title w-80">@lang('messages.purchase_order.items.tables.qty_per_box')</span>
            <span class="desc">
                <input value="{{$product->qty_per_box}}"   maxlength="8"  type="text" name="qty_per_box_{{$product->product_id}}" class="qty_per_box po_textbox" />
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


    </td>
    <td>
        <div class="position-relative">
            <span class="pound-sign">@lang('messages.common.pound_sign')</span>
            <input value="{{$product->unit_price}}"  type="text" maxlength="9" name="unit_price_{{$product->product_id}}" class="unit_price po_textbox"  /></div>
    </td>
    <td>
        <span class="title">@lang('messages.common.pound_sign')</span>
        <span name="total_product_cost_{{$product->product_id}}" class="title total_product_cost po_textbox">   {{priceFormate($product->unit_price*$product->total_quantity)}}
        </span>
    </td>
    <td>


        @if($product->vat_type == 0)
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.vat')</span>
            <span class="desc vat" data-value="{{$product->vat}}">{{$product->vat}}%</span>
        </div>
        @elseif($product->vat_type == 1)
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.vat')</span>
            <span class="vat desc" data-value="{{$product->vat}}">{{$product->vat}}%</span>
        </div>
        @else
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.std_rate')</span>
            <span class="desc">
                <input type="text" class="standard_rate po_textbox" value="{{floatval($product->standard_rate_value)}}"  oninput="calculateMixRate(this)" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.zero_rate')</span>
            <span class="desc">
                <input type="text" class="zero_rate" value="{{floatval($product->zero_rate_value)}}" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.vat')</span>
            <span class="vat desc" data-value="{{$product->vat}}">{{$product->vat}}%</span>
        </div>
        @endif

    </td>

    <td>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.expected')</span>
            <span class="desc">
                <input type="text" readonly="readonly" value="{{$product->expected_mros}}"  name="expected_mros_{{$product->product_id}}" class="expected_mros po_textbox" maxlenth="5" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.selling_qty')</span>
            <span class="desc">
                <input type="text" value="{{$product->sel_qty}}"  name="sel_qty_{{$product->product_id}}" class="sel_qty po_textbox" maxlenth="5" />
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.selling_price')</span>
            <span class="desc">
                <div class="position-relative">
                    <span class="pound-sign">@lang('messages.common.pound_sign')</span>
                    <input type="text" value="{{$product->sel_price}}"  name="sel_price_{{$product->product_id}}" class="sel_price po_textbox" maxlength="9" /></div>
            </span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">@lang('messages.purchase_order.items.tables.mros')</span>
            <span class="desc">
                <input readonly="readonly" type="text" value="{{ isset($product->mros)?floatval($product->mros):''}}"  name="mros_{{isset($product->mros)?$product->mros:''}}" class="mros po_textbox" maxlength="9" />
            </span>
        </div>
    </td>
    <td align="center">
        <input type="hidden" class="vat_type" value="{{$product->vat_type}}" />
        <input type="hidden" class="landed_product_cost" value="{{$product->landed_product_cost}}" />
        <input type="hidden" class="net_selling_price_excluding_vat" value="{{$product->net_selling_price_excluding_vat}}" />
        <input type="hidden" class="total_net_selling_price" value="{{$product->total_net_selling_price}}" />
        <input type="hidden" value="{{$product->total_net_profit}}" class="total_net_profit" />
        <input value="{{$product->total_net_margin}}" type="hidden" class="total_net_margin" />
        <a style="@if($purchaseOrder->po_status < 6) display: block; @else display: none; @endif" href="javascript:;" data-id="{{$product->id}}" class="removeRow"><span class="icon-moon icon-Delete"></span></a>
    </td>
</tr>
@endforeach
@endif