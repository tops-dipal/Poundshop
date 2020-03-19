	<div class="row">
		<div class="col-lg-6">
			<div class="form-group row">
				<label class="col-lg-4 col-form-label">Total Import Duty:</label>
				<label class="col-lg-4 col-form-label">{{ $total_import_duty }}</label>
				
			</div>
		</div>
		<div class="col-lg-6">
			<div class="form-group row">
				<label class="col-lg-4 col-form-label">Total Vat:</label>
				<label class="col-lg-2 col-form-label">{{ $total_vat }}</label>
				<label class="col-lg-3 col-form-label">Total Tax:</label>
				<label class="col-lg-3 col-form-label">{{ $total_tax }}</label>
				
			</div>
		</div>
		<div class="col-lg-6">
			<div class="form-group row">
				<label class="col-lg-6 col-form-label">Total Vat Paid on UK POs:</label>
				<label class="col-lg-2 col-form-label">{{ $total_vat_on_uk }}</label>
				
			</div>
		</div>
		<div class="col-lg-6">
			<div class="form-group row">
				<label class="col-lg-6 col-form-label">Total Vat Paid on Import POs:</label>
				<label class="col-lg-4 col-form-label">{{ $total_vat_on_import }}</label>
				
			</div>
		</div>
	</div>
	         		<div class="row">
	         			<table id="data_table" class="display">
	         				<thead>
	         					<tr>
	         						<th>PO Number</th>
	         						<th>Goods Recieved Date</th>
	         						<th>Supplier</th>
	         						<th>Uk PO/Import PO</th>
	         						<th>Amount Before VAT</th>
	         						<th>Import Duty(£)</th>
	         						<th>VAT Import(£)</th>
	         						<th>VAT-UK(£)</th>
	         						<th>Value of Zero Rated Items</th>
	         					</tr>
	         				</thead>
	         				<tbody>
	         					
								@forelse($reports as $reportKey=>$reportVal)
								<tr>
									<td>{{ $reportVal->po_number }}</td>
									<td>{{ $reportVal->po_date }}</td>
									<td>{{ $reportVal->supplier_name }}</td>
									<td>{{ $reportVal->po_import_type }}</td>
									<td>{{ $reportVal->itd_vat }}</td>
									<td>{{ $reportVal->import_duty }}</td>
									<td>{{ $reportVal->vat_import }}</td>
									<td>{{ $reportVal->vat_uk }}</td>
									<td>{{ $reportVal->zero_rated_items }}</td>
								</tr>
								@empty
								@endforelse
							
	         				</tbody>
	         			</table>
	         		</div>