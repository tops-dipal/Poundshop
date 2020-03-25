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
</head>

<body>
	@for($i=1; $i <= $count; $i++)
		 <img src="{{ url('set-barcode-img?text='.$barcode.'&size='.$barcode_size) }}" />
	@endfor
</body>
</html>
