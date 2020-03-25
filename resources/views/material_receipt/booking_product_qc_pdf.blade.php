<!DOCTYPE html5>
<html>
<head>

	<title>Print Barcode</title>
	<script src="{{ asset('js/jquery-2.2.4.min.js')}}" ></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            window.print();
        });
    </script>
    <style>
		table {
		  font-family: arial, sans-serif;
		  border-collapse: collapse;
		  width: 100%;
		}

		td, th {
		  border: 1px solid #dddddd;
		  text-align: left;
		  padding: 8px;
		}

		tr:nth-child(even) {
		  background-color: #dddddd;
		}
		</style>
</head>

<body>

<span>Booking Ref. Id : {{ $bookingDetail->booking_ref_id }}</span><br><br>
@forelse($data as $dataKey=>$dataVal)
<span>Product Title: {{ $dataVal->title }}</span>
<br>
<table>
	<thead>
		<tr>
			<th >QC Checklist</th>
			<th>QC Checklist Points</th>
			<th>Comment</th>
			<th>Image</th>
			<th>Checked Status</th>
		</tr>
	</thead>
	<tbody>
		
			@forelse($dataVal->bookingQCChecklistPoints as $pointKey=>$pointVal)
			<tr>
				@if($pointKey==0)
				<td rowspan="{{ count($dataVal->bookingQCChecklistPoints) }}">{{ $dataVal->name }}</td>
				@endif
				<td>{{ $pointVal->option_caption }}</td>
				<td>{{ empty($pointVal->comments) ? "-" : $pointVal->comments }}</td>
				<td><img src="{{ $pointVal->image }}" height="50" width="50"></td>
				<td>{{ ($pointVal->is_checked==1) ? 'Yes' :'No' }}</td>
			</tr>
			@empty
			@endforelse
		
	</tbody>
</table>
<br><br>
@empty
@endforelse
</body>
</html>
