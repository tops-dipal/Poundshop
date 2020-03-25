<!DOCTYPE html>
<html>
<head>
	<title></title>
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
<script src="{{ asset('js/jquery-2.2.4.min.js')}}" ></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            window.print();
        });
    </script>
</head>
<body>
	<h1>QC Checklist Name : {{ $title }}</h1>
	<table>
		<thead>
			<tr>
			<th>CheckList Points</th>
			</tr>
		</thead>
		<tbody>
			@forelse($points as $key=>$val)
			<tr>
				<td>{{ $val->title }}</td>
			</tr>
			@empty
			@endforelse
		</tbody>
	</table>
</body>
</html>
