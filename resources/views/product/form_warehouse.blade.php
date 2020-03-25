@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
<div class="form-row">
	<div class="col-lg-3 col-md-6">
		<div class="form-group form-row">
            <label for="inputPassword" class="col-lg-8 col-form-label">@lang('messages.range_management.day_stock'):<span class="asterisk">*</span></label>
            <div class="col-lg-4">
                <input type="text" class="form-control" id="stock_hold_days" placeholder="" name="stock_hold_days" value="{{ $result->stock_hold_days }}">
            </div>
        </div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="form-group form-row">
            <label for="inputPassword" class="col-lg-6 text-lg-right col-form-label">@lang('messages.range_management.override_status'):<span class="asterisk">*</span></label>
            <div class="col-lg-6 mt-3">
                <label class="fancy-radio sm mr-3">
                    <input type="radio" name="is_override" value="1"  @if($result->is_override=="1")  checked="checked" @endif  />
                    <span class="font-14-dark"><i></i>Yes</span>
                </label>
                <label class="fancy-radio sm">
                    <input type="radio" name="is_override" value="0"  @if($result->is_override=="0")  checked="checked" @endif @if(empty($result)) checked="checked" @endif />
                    <span class="font-14-dark"><i></i>No</span>
                </label>
            </div>
        </div>        
	</div>
	<div class="col-lg-2  col-md-6">
		<div class="form-group form-row">
            <label for="inputPassword" class="col-lg-4 text-lg-right col-form-label">@lang('messages.location_assign.ros'):<span class="asterisk">*</span></label>
            <div class="col-lg-8">
                <input type="text" class="form-control" id="ros" placeholder="Rate Of Sale" name="ros" value="{{ $result->ros }}">
            </div>
        </div>
	</div>
	

    <div class="col-lg-4  col-md-6">
        <div class="form-group form-row">
            <label for="inputPassword" class="col-lg-3 text-lg-right col-form-label">Site:</label>
            <div class="col-lg-9">
                
                <select id="select_warehouse" name="select_warehouse" class="form-control">
                    @forelse($warehouses as $key=>$val)
                        <option value="{{ $val->id }}" {{ ($val->is_default==1) ? 'selected' : '' }}>{{ $val->name }}</option>
                    @empty
                    <option value=" ">No Any Site</option>
                    @endforelse
                    
                </select>
            </div>
        </div>        
    </div>
</div>	
<div class="load_count_data">
@include('product.warehouse-tab-count')
</div>
@include('product.warehouse-modal')
	{{--<div class="col-lg-12">
    <div class="table-responsive">
        <table id="supplier_contact_person" class="table border-less display">
			<thead>
				<tr align="center">
				    <th>QTY</th>
				    <th>Location</th> 
				    <th>Warehouse</th>
				</tr>
			</thead>
			<tbody>
				@forelse($result->locations as $location)
					<tr align="center">
						<td>
							<input type="number" disabled="disabled" only_digit name="qty" value="{{ $location->available_quantity }}">
						</td>
						<td>
							<input type="text" disabled="disabled" name="aisle" value="{{ $location->location_details->aisle }}" class="col-lg-2">
							<input type="text" disabled="disabled" name="rack" value="{{ $location->location_details->rack }}" class="col-lg-2">
							<input type="text" disabled="disabled" name="floor" value="{{ $location->location_details->floor }}" class="col-lg-2">
							<input type="text" disabled="disabled" name="box" value="{{ $location->location_details->box }}" class="col-lg-2">
						</td>	
						<td>{{ $location->warehouse->name }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="100%" align="center">
							@lang('messages.common.no_records_found')
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	</div>--}}

