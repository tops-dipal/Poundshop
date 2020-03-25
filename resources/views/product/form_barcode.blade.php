<!-- Barcodes -->
@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
	
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="supplier_contact_person" class="table border-less display table-striped custom-table">
                <thead>
                    <tr>
                        <th class="checkbox-container">
                            <div class="d-flex">
                                <label class="fancy-checkbox">
                                    <input type="checkbox" class="master-checkbox" child-checkbox-class = "child-checkbox-barcode">
                                    <span><i></i></span>
                                </label>
                                <div class="dropdown bulk-action-dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                                        <span class="icon-moon icon-Drop-Down-1"/>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                        <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                        <button type="button" class="btn btn-add" onclick="deleteBarcode(this)">
                                            <span class="icon-moon red icon-Delete"></span>
                                            @lang('messages.common.delete')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>@lang('messages.inventory.baracode_type')</th>
                        <th>@lang('messages.inventory.baracode_number')</th>
                        <th>@lang('messages.common.quantity')</th>
                        <th>@lang('messages.common.date_addded')</th>
                        <th data-class-name="action">@lang('messages.common.action')</th>              
                    </tr>
                </thead>
                <tbody>
                    @if(!$result->barCodes->isEmpty())
                		@foreach($result->barCodes as $product_bardcode)
                			<tr>
                				<td>
                					<div class="d-flex">
                						<label class="fancy-checkbox">
                							<input type="checkbox" value="{{$product_bardcode->id}}" class="child-checkbox-barcode">
                							<span><i></i></span>
                						</label>
                					</div>
                				</td>
                				
                                <td>
                                    {{barcodeType($product_bardcode->barcode_type)}}
                                </td>   

                                <td>
                                    {{$product_bardcode->barcode}}
                                </td>
                                
                				<td>
                                    @if($product_bardcode->barcode_type == 1)
                                        {{1}}
                                    @else
                                        {{$product_bardcode->case_quantity}}
                				    @endif
                                </td>
                				<td>
                					{{system_date($product_bardcode->created_at)}}
                				</td>	

                				<td>
                					<ul class="action-btns">
                						<li>
                							<a class="btn-edit" href="javascript:;" onclick="editBarcode(this)" attr-id="{{$product_bardcode->id}}" attr-barcode="{{$product_bardcode->barcode}}" attr-barcode_type="{{$product_bardcode->barcode_type}}" attr-case_quantity="{{$product_bardcode->case_quantity}}" attr-parent-id="{{ $product_bardcode->parent_id }}"> 
                								<span class="icon-moon icon-Edit"></span></a></li><li>

                							<a class="btn-delete" href="javascript:;" onclick="deleteBarcode(this)" attr-id="{{$product_bardcode->id}}"><span class="icon-moon icon-Delete"></span></a>
                						</li>
                					</ul>
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
                </tbody>
            </table>                
        </div>
    </div>
</div>


