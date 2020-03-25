<!-- Suppliers -->
@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
<div class="row">
    <div class="col-lg-6 row">
        <div class="col-lg-6 col-8">
            <select id="supplier_id" class="form-control">
                <option value="">@lang('messages.common.select') @lang('messages.common.supplier')</option>
                @foreach($suppliers as $supplier)
                @if(!in_array($supplier->id, $already_assigned_suppliers))
                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                @endif
                @endforeach
            </select>
            <span id="invalid-supplier" class="invalid-feedback" style="display: none;">This field is required.</span>
        </div>
        <div class="col-lg-6 col-4">            
            <button type="button" id="addSupplierId" class="btn btn-add btn-blue btn-h-32" onclick="addSupplier(this)">
            @lang('messages.common.add')
            </button>
        </div>
    </div>
    @php
    /*
    <div class="col-lg-6" align="right">
        <button type="button" class="btn btn-add btn-red" onclick="deleteSupplier(this)">
        <span class="icon-moon icon-Supplier"></span>
        @lang('messages.inventory.delete_supplier')
        </button>
    </div>
    */
    @endphp
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive mt-3">
            <table id="supplier_contact_person" class="table border-less display table-striped custom-table">
                <thead>
                    <tr>
                        <th class="checkbox-container">
                            <div class="d-flex">
                                <label class="fancy-checkbox">
                                    <input type="checkbox" class="master-checkbox" child-checkbox-class = "child-checkbox-supplier">
                                    <span><i></i></span>
                                </label>
                                <div class="dropdown bulk-action-dropdown">
                                    <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                                    <span class="icon-moon icon-Drop-Down-1"/>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                            <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                            <button type="button" class="btn btn-add" onclick="deleteSupplier(this)">
                                            <span class="icon-moon red icon-Delete"></span>
                                            @lang('messages.common.delete')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>@lang('messages.common.supplier')</th>
                            <th>@lang('messages.inventory.supplier_sku')</th>
                            <th>@lang('messages.inventory.supplier_price_per_case')</th>
                            <th>@lang('messages.common.quantity')</th>
                            <th>@lang('messages.inventory.supplier_qty_per_case')</th>
                            <th>@lang('messages.inventory.supplier_min_order_qty')</th>
                            <th>@lang('messages.inventory.supplier_default')</th>
                            <th class="m-w-200">@lang('messages.inventory.supplier_note')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!$product_suppliers->isEmpty())
                        @foreach($product_suppliers as $product_supplier)
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <label class="fancy-checkbox">
                                        <input type="checkbox" value="{{$product_supplier->id}}" class="child-checkbox-supplier">
                                        <span><i></i></span>
                                    </label>
                                </div>
                                <input type="hidden" name="product_supplier_id[]" value="{{$product_supplier->id}}">
                            </td>
                            <td>
                                {{ucwords($product_supplier->supplier->name)}}
                            </td>
                            <td>
                                <input type="text" class="form-control w-100" name="supplier_sku[{{$product_supplier->id}}]" value="{{$product_supplier->supplier_sku}}">
                            </td>
                            <td>
                                <input type="text" class="form-control w-100" only_numeric name="price_per_case[{{$product_supplier->id}}]" value="{{$product_supplier->price_per_case}}">
                                
                            </td>
                            <td>
                                <input type="text" class="form-control w-100" only_digit name="quantity[{{$product_supplier->id}}]" value="{{$product_supplier->quantity}}">
                            </td>
                            <td>
                                <input type="text" class="form-control w-100" only_digit name="quantity_per_case[{{$product_supplier->id}}]" value="{{$product_supplier->quantity_per_case}}">
                            </td>
                            <td>
                                <input type="text" class="form-control w-100" only_digit name="min_order_quantity[{{$product_supplier->id}}]" value="{{$product_supplier->min_order_quantity}}">
                            </td>
                            <td>
                                <label class="fancy-radio">
                                    <input type="radio" name="default" value="{{$product_supplier->id}}" {{ ($product_supplier->is_default == '1') ? 'checked="checked"' : '' }}>
                                    <span><i></i></span>
                                </label>
                            </td>
                            <td>
                                <textarea class="form-control" name="note[{{$product_supplier->id}}]">{{$product_supplier->note}}</textarea>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="100%" align="center">
                                @lang('messages.common.no_records_found')
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>