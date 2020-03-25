@if(isset($bookingProducts) && @count($bookingProducts)>0)
@foreach($bookingProducts as $bookingProduct)
<tr id="{{$bookingProduct->product_id}}">
    <td>
        <p class="font-12-dark bold mb-2">{{!empty($bookingProduct->product->title) ? $bookingProduct->product->title : '--'}}</p>
        <div class="d-flex group-item">
            <span class="title w-60">SKU</span>
            <span class="desc color-blue">{{!empty($bookingProduct->product->sku) ? $bookingProduct->product->sku : '--'}}</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">Status</span>
            <span class="desc">{{isset($bookingProduct->product->is_listed_on_magento) ? config('params.product_listed.'.$bookingProduct->product->is_listed_on_magento) : '--'}}</span>
        </div>
        <div class="d-flex group-item">
            <span class="title w-60">Supplier SKU</span>
            <span class="desc color-blue">
                <input type="text" name="" value="{{isset($bookingProduct->product->is_listed_on_magento) ? config('params.product_listed.'.$bookingProduct->product->is_listed_on_magento) : '--'}}" disabled>
            </span>
        </div>
    </td>
    <td>{{!empty($bookingProduct->barcode) ? $bookingProduct->barcode : '--'}}</td>
    <td>{{!empty($bookingProduct->purchaseOrderProduct->total_quantity) ? $bookingProduct->purchaseOrderProduct->total_quantity : '--'}}</td>

    <td>
        <input type="text" disabled value="{{!empty($bookingProduct->qty_received) ? $bookingProduct->qty_received : '0'}}" name="">
    </td>

    <td>
        @if($bookingProduct->difference < 0)
        <input type="text" disabled value="{{!empty($bookingProduct->difference) ? $bookingProduct->difference : '0'}}" name="" style="color:red;">
        @else
        <input type="text" disabled value="{{!empty($bookingProduct->difference) ? $bookingProduct->difference : '0'}}" name="" >
        @endif
    </td>
    <td>
        @if($bookingProduct->is_photobooth == 1)
        {{($bookingProduct->pick_pallet_qty + $bookingProduct->bulk_pallet_qty + 1)}}
        @else
        {{($bookingProduct->pick_pallet_qty + $bookingProduct->bulk_pallet_qty)}}
        @endif
    </td>
    <td>
        @if(isset($bookingProduct->bookingPODiscrepancy) && @count($bookingProduct->bookingPODiscrepancy)>0)
        @foreach($bookingProduct->bookingPODiscrepancy as $discrepancy)

        <div class="mb-2">
            @if($discrepancy->discrepancy_type == '2')
            {{config('params.discrepancy_type.'.$discrepancy->discrepancy_type)}}
            @if($discrepancy->status == 0 && $discrepancy->is_added_by_system == 1)
            &nbsp; {{'(Goods In Awaiting)'}}
            @elseif($discrepancy->is_added_by_system == 2 || $discrepancy->is_added_by_system == 3)
            &nbsp; {{'(Goods In Decided)'}}
            @else
            &nbsp; {{'(Buyer\'s Decided)'}}
            @endif
            &nbsp; {{$discrepancy->qty}}
            @else
            {{config('params.discrepancy_type.'.$discrepancy->discrepancy_type)}} &nbsp; {{$discrepancy->qty}}
            @endif
        </div>
        @endforeach
        @endif
    </td>
    <td>
        @if(isset($bookingProduct->bookingPODiscrepancy) && @count($bookingProduct->bookingPODiscrepancy)>0)
        @foreach($bookingProduct->bookingPODiscrepancy as $discrepancy)
        <div class="mb-2">
            @if($discrepancy->status !== 0)
            {{$discrepancy->qty}}&nbsp;{{config('params.booking_discrepancy_status.'.$discrepancy->status)}}
            @endif
        </div>
        @endforeach
        @endif
    </td>
    <td data-totalqty="0">

        @if(isset($bookingProduct->bookingPODiscrepancy) && @count($bookingProduct->bookingPODiscrepancy)>0)
        @foreach($bookingProduct->bookingPODiscrepancy as $discrepancy)
        <div class="mb-2">
            @if($discrepancy->discrepancy_type == 1 && $discrepancy->status == 0) <!-- short -->
            <div class="dropdown more-action-dropdown">
                <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                    <span class="icon-moon icon-Drop-Down-1"/>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                    <h4 class="title">More Actions</h4>
                    <div class="item-actions">
                        <button data-qty="{{$discrepancy->qty}}" data-disc="{{$discrepancy->id}}"  data-purchaseid="{{$purchaseOrder->id}}" data-productid="{{$bookingProduct->product_id}}" title="Cancel Items on PO" class="btn btn-add cancelled-item-po">
                            <span class="icon-moon red icon-Reverse-Purchse-Order"></span>Cancel Items on PO
                        </button>
                        <a href="javascript:;"  data-qty="{{$discrepancy->qty}}" data-disc="{{$discrepancy->id}}"  data-purchaseid="{{$purchaseOrder->id}}" data-productid="{{$bookingProduct->product_id}}" title="Move to New PO" class="btn btn-add move-new-po-btn">
                            <span class="icon-moon yellow icon-Download"></span>Move to New PO
                        </a>
                    </div>
                </div>
            </div>
            @elseif($discrepancy->discrepancy_type == 2 &&  $discrepancy->status == 0 ) <!-- over and awaited -->
            <div class="dropdown more-action-dropdown">
                <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                    <span class="icon-moon icon-Drop-Down-1"/>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                    <h4 class="title">More Actions</h4>
                    <div class="item-actions">
                        <a  href="javascript:;" data-disc="{{$discrepancy->id}}"  data-purchaseid="{{$purchaseOrder->id}}" data-productid="{{$bookingProduct->id}}"  title="Keep It/ Return to Supplier" class="btn btn-add returntoSupplierBtn">
                            <span class="icon-moon yellow icon-Download"></span>Keep It/ Return to Supplier
                        </a>
                    </div>
                </div>
            </div>
            @else
            --
            @endif

        </div>
        @endforeach
        @endif

    </td>
</tr>
@endforeach
@endif
