<form action="{{route('api-supplier.store')}}" method="POST" class="form-horizontal form-flex" role="form" tab_switch_save id="form-ratings">
	@csrf
	<input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
	<div class="table-responsive">
		<table class="table ratings_table display dataTable no-footer  table-striped custom-table" id="ratings_table">
			<thead>
				<tr>
					<th>Date</th>
					<th>Purchase Order Value</th>
					<th>Delivered Value</th>
					<th>Percentage</th>
					<th>Item Ordered</th>
					<th>Item Delivered</th>
					<th>Percentage</th>
				</tr>
			</thead>
			<tbody>
				@php
				$i=1;
				@endphp
				@forelse($ratings as $rk=>$rv)
				<tr>
					<td><div class="min-h-35">{{ $rv->year }}</div></td>
					<td>@lang('messages.common.pound_sign') {{ $rv->total_po_value }}</td>
					
					<td>@lang('messages.common.pound_sign') {{ $rv->deli_val }}</td>
					<td>{{ number_format($rv->percentage,2) }} %</td>
					<td>{{ $rv->items_ordered }}</td>
					<td>{{ $rv->item_delivered }}</td>
					<td> {{ number_format($rv->percentage1,2)}} %</td>
				</tr>
				@php
				$i++;
				@endphp
				@empty
				<tr><td colspan="7"><center>No Record Found</center></td></tr>
				@endforelse
				
			</tbody>
		</table>
	</div>
</form>