<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" type="image/png" href="{{ asset('img/fevicon.png')}}"/>
<!-- Bootstrap CSS -->
<link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap" rel="stylesheet">



<link rel="stylesheet" href="{{ asset('css/animate.css')}}">
<link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css')}}">
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css"> -->
<link rel="stylesheet" href="{{ asset('css/rowReorder.dataTables.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/responsive.dataTables.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/fixedColumns.dataTables.min.css')}}">


<link rel="stylesheet" href="{{ asset('css/lightcase.css')}}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />

<!-- commented -->
<link rel="stylesheet" href="{{asset('css/perfect-scrollbar.css')}}">
<link rel="stylesheet" href="{{ asset('css/icon.css')}}">
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap-datepicker.css')}}">
<link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/modal.css')}}">
<link rel="stylesheet" href="{{ asset('css/slick.css')}}">
<!-- commented -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/css/bootstrap-select.min.css">
<!-- commented -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/bootstrap-tagsinput.css')}}">
<link rel="stylesheet" href="{{ asset('css/slick-theme.css')}}">
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
<link rel="stylesheet" href="{{ asset('css/media.css')}}">
<link rel="stylesheet" href="{{ asset('css/image-uploader.min.css')}}">

<!-- commented -->

<link rel="stylesheet" href="{{ asset('css/developer.css')}}">
<!-- <link rel="stylesheet" href="{{asset('css/poundshop.css')}}"> -->
@yield('css')
<script>
    var BASE_URL = '<?php echo url('api'); ?>/';
    var API_TOKEN = '{{Session::get('apiToken')}}';
    var WEB_BASE_URL = "{{url('/')}}";
    var NO_PRODUCT_IMG_URL = "{{ url('storage/uploads/product-images/no-image.jpeg') }}";
</script>
<script type="text/javascript">
    var POUNDSHOP_MESSAGES = <?php echo json_encode(Lang::get('messages')); ?>
</script>
