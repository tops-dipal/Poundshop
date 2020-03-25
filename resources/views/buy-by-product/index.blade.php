@extends('layouts.app')
@section('content')
@section('title',__('messages.common.buy_by_product'))
<div class="content-card custom-scroll">
	<form  method="get" id="searchProductByBarcodeForm">
		<div class="content-card-header">
			<h3 class="page-title">@lang('messages.common.buy_by_product')</h3>
			
			<div class="center-items">
				<input type="text" class="form-control search-input" id="search_data" name="" placeholder="@lang('messages.common.search_buy_product')" />
				<a class="btn btn-add btn-green btn-header  ml-2" id="startButton"><span class="icon-moon icon-Add-Image"></span></a>
			</div>
			<div class="right-items">
			</div>
			
			
		</div>
	</form>
	<div class="card-flex-container ">
		<div class="container-fluid h-100 d-flex flex-column load_data">
			<div>
			<video id="video" width="" style="border: 1px solid gray; width: 100%; height: 500px;"></video>
		</div>
		<div id="sourceSelectPanel" style="display:none">
			<label for="sourceSelect">Change video source:</label>
			<select id="sourceSelect" style="max-width:400px">
			</select>
		</div>
	</div>
</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>
<script type="text/javascript" src="{{asset('js/buy-by-product/scan-barcode.js?v='.CSS_JS_VERSION)}}"></script>
<script type="text/javascript" src="{{asset('js/buy-by-product/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection