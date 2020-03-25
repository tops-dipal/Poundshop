@extends('layouts.app')
@section('content')
@section('title',__('messages.move_poducts.title'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.move_poducts.title')</h3>
        
        <div class="right-items">
            <a href="{{route('cartons.index')}}" class="btn btn-gray btn-header px-4" title="@lang('messages.modules.button_cancel')">@lang('messages.modules.button_cancel')</a>
            <button class="btn btn-blue btn-header px-4" title="@lang('messages.modules.button_save')" form="create-carton-form">@lang('messages.modules.button_save')</button>
        </div>					
    </div>	
    <div class="card-flex-container">
        <form class="form-horizontal form-flex" method="post" id="create-carton-form" action="{{route('api-cartons.store')}}">     
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.move_poducts.scan_from_loc')<span class="asterisk">*</span></label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" placeholder="" name="scan_from_loc" id="scan_from_loc">
                                            <span id="from_location_type"></span>
                                        </div>
                                        <div class="col-lg-4">
                                               <span id="from_location_type"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.move_poducts.scan_product_barcode')<span class="asterisk">*</span></label>
                                        <div class="col-lg-4">
                                          <input type="text" class="form-control" id="scan_product_barcode" placeholder="" name="scan_product_barcode">
                                        </div>
                                    </div>
                                </div>
                                {{-- scanned barcode product info --}}
                                <div class="prouctInfo col-lg-6">
                                </div>
                                {{--end  scanned barcode product info --}}

                                {{-- scanned barcode product current locations --}}
                                <div class="currentProductLocations col-lg-6">
                                </div>
                                {{-- end scanned barcode product current locations --}}
                                <div class="qtyMove col-lg-12" style="display:none">
                                    <div class="col-lg-12">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.move_poducts.quantity_moved')<span class="asterisk">*</span></label>
                                            <div class="col-lg-4">
                                              <input type="text" class="form-control" id="quantity_moved" placeholder="" name="quantity_moved">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.material_receipt.best_before_date')<span class="asterisk">*</span></label>
                                            <div class="col-lg-4">
                                              <input type="text" class="form-control" id="best_before_date" placeholder="" name="best_before_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.move_poducts.scan_to_loc')<span class="asterisk">*</span></label>
                                            <div class="col-lg-4">
                                              <input type="text" class="form-control" id="scan_to_loc" placeholder="" name="scan_to_loc">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.location_assign.qty_that_will_fit_location')<span class="asterisk">*</span></label>
                                            <div class="col-lg-4">
                                              <input type="text" class="form-control" id="qty_that_will_fit_location" placeholder="" name="qty_that_will_fit_location">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>						
                </div>
            </div>	
            <div class="content-card-footer">
                <div class="button-container">
                    
                </div>
            </div>
        </form>   
    </div> 
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/product/move-product.js?v='.CSS_JS_VERSION)}}"></script>
@endsection