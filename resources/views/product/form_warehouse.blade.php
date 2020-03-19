@csrf
<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
<div class="row">
	<div class="col-lg-12">
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
	</div>
</div>		
