<style>
    .error{color: red;}
</style>
<input type="hidden" value="{{$purchaseOrder->supplier_id}}" id="supplier_id" />
<input type="hidden" value="{{$purchaseOrder->po_import_type}}" id="po_import_type" />
<input type="hidden" value="{{route('products.search')}}" id="search_product_url" />
<input type="hidden" value="{{$purchaseOrder->id}}" id="po_id" />
<!--<div class="row">-->
@if($purchaseOrder->po_status < 6)
<!--    <div class="col-md-12" align="right">
  <a class="btn btn-add btn-red delete-many" href="javascript:;">
    <span class="icon-moon icon-Supplier"></span>
    @lang('messages.purchase_order.items.remove_item')
  </a>
</div>    -->
@endif
<!--</div>  -->
<form class="save-po-form" id="save-po-form" method="post" >
    <fieldset id="field-set" @if($purchaseOrder->po_status > 5 ) disabled="disabled" @endif>
              @if($purchaseOrder->po_import_type == 1)
              @include('purchase-orders.uk-po-content')
              @else
              @include('purchase-orders.import-po-content')
              @endif
</fieldset>
</form>
<!-- modal -->
<div class="modal fade" id="item-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.purchase_order.items.add_items')</h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-4" data-dismiss="modal" aria-label="Close">
                        @lang('messages.common.cancel')
                    </button>
                    <button type="button" class="btn btn-green font-12 px-4 ml-3" id="addToPoButton" onclick="addToPO()" title="@lang('messages.purchase_order.items.add_po')">@lang('messages.purchase_order.items.add_po')</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <input type="text" id="search-product-textbox" class="form-control search-input" placeholder="@lang('messages.purchase_order.items.search_product')" name="search-product-textbox"  >
                            </div>
                        </div>
                    </div>
                </div>
                <table id="example" class="table border-less" style="width:100%">
                    <thead>
                        <tr>
                            <th><div class="d-flex">
                                    <label class="fancy-checkbox">
                                        <input name="ids[]" type="checkbox" class="master">
                                        <span><i></i></span>
                                    </label>
                                </div>
                            </th>
                            <th>@lang('messages.purchase_order.items.image')</th>
                            <th>@lang('messages.purchase_order.items.sku')</th>
                            <th>@lang('messages.purchase_order.items.supplier_sku')</th>
                            <th>@lang('messages.purchase_order.items.info')</th>
                            <th>@lang('messages.purchase_order.items.tables.product_tille')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>