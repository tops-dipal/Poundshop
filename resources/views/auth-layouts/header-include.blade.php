<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" type="image/png" href="{{ asset('img/fevicon.png')}}"/>
<!-- Bootstrap CSS -->
<link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}">		
<link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
<link rel="stylesheet" href="{{ asset('css/buy.css')}}">
<link rel="stylesheet" href="{{ asset('css/media.css')}}">
<!--<link rel="stylesheet" href="{{ asset('css/auth-poundshop.css')}}">-->
<script type="text/javascript">
    var POUNDSHOP_MESSAGES = <?php echo json_encode(Lang::get('messages')); ?>
</script>
<title>@yield('title')</title>