@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>
        <div class="center-items">
            <ul class="nav nav-tabs header-tab" id="myTab" role="tablist">                
                <li role="presentation" class="nav-item">
                    <a class="nav-link" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general">
                        @lang('messages.modules.supplier_general_info')
                        <i class="fa fa-exclamation-triangle" style="display: none;" aria-hidden="true"></i>
                    </a>
                </li>
                @if(!empty($id))
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact">
                            @lang('messages.modules.supplier_contact_info')
                            <i class="fa fa-exclamation-triangle" style="display: none;" aria-hidden="true"></i>
                        </a>
                    </li>
                    
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab" aria-controls="payment">
                            @lang('messages.modules.supplier_payment_info')
                            <i class="fa fa-exclamation-triangle" style="display: none;" aria-hidden="true"></i>
                        </a>
                    </li>
                    
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="ratings-tab" data-toggle="tab" href="#ratings" role="tab" aria-controls="ratings">
                            @lang('messages.modules.supplier_ratings')
                            <i class="fa fa-exclamation-triangle" style="display: none;" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li role="presentation"  class="nav-item">
                        <a class="nav-link" id="references-tab" data-toggle="tab" href="#references" role="tab" aria-controls="references">
                            @lang('messages.modules.references')
                            <i class="fa fa-exclamation-triangle" style="display: none;" aria-hidden="true"></i>
                        </a>
                    </li>
                    
                    <li role="presentation"  class="nav-item">
                        <a class="nav-link" id="terms-tab" data-toggle="tab" href="#terms" role="tab" aria-controls="terms">
                            @lang('messages.modules.supplier_terms_condition')
                            <i class="fa fa-exclamation-triangle" style="display: none;" aria-hidden="true"></i>
                        </a>
                    </li>
                @endif
            </ul>            
        </div>
        <div class="right-items">
            <a href="{{route('supplier.index')}}" class="btn btn-gray btn-header px-4">@lang('messages.common.cancel')</a>
            <button type="submit" form="" class="btn btn-blue btn-header px-4 tab_actions general_tab_actions payment_tab_actions terms_condition_tab_actions" id="form_submit">@lang('messages.common.save')</button>
            <!-- Contact Into -->
            <button type="button" class="btn btn-add btn-blue font-12 tab_actions contact_tab_actions" onclick="contactForm(this)" id="add-barcode-btn" style="display: flex;">
                    <span class="icon-moon icon-Add"></span>
                @lang('messages.supplier.add_supplier_contact')         
            </button>
        </div>
    </div>
    <div class="card-flex-container">
            <div class="form-fields">
                <div class="container-fluid">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                            @include('supplier.general-info')
                         
                        </div>
                        
                        @if(!empty($id))
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                               @include('supplier.contact-info')
                            </div>   
                            
                            <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                               @include('supplier.payment-info')     
                            </div>
                            
                            <div class="tab-pane fade" id="ratings" role="tabpanel" aria-labelledby="ratings-tab">
                               @include('supplier.ratings')
                            </div>
                            
                            <div class="tab-pane fade" id="references" role="tabpanel" aria-labelledby="references-tab">
                                    @include('supplier.references')
                            </div>
                            
                            <div class="tab-pane fade" id="terms" role="tabpanel" aria-labelledby="terms-tab">
                                @include('supplier.terms-condition')
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="custom-modal modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('messages.supplier.create_contact')</h5>
                    <div>
                        <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                        <button type="button" class="btn btn-green font-12 px-3 ml-2" onclick="saveContact(this)" id="contact_submit" >@lang('messages.common.submit')</button>
                    </div>
                </div>
                <div class="modal-body">
                    @php
                        $supplier_id = !empty($id) ? $id : '';
                    @endphp
                    <form id="contactForm" method="post" action="{{url('api/api-supplier-save-contacts')}}" refresh = "{{url('supplier/supplier-contacts'.$supplier_id)}}" html_id = "contact">
                        <div class="row">
                            <input type="hidden" name="supplier_id" value="{{ $supplier_id }}">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">@lang('messages.supplier.contact_person_name') <span class="asterisk">*</span></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="" name="name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">@lang('messages.supplier.contact_person_email') <span class="asterisk">*</span></label>
                                    <div class="col-lg-9">
                                        <input type="email" class="form-control" placeholder="" name="email">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">@lang('messages.supplier.contact_person_phone')</label>
                                    <div class="col-lg-9">
                                        <input type="text" only_digit class="form-control" placeholder="" name="phone">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">@lang('messages.supplier.contact_person_mobile')</label>
                                    <div class="col-lg-9">
                                        <input type="text" only_digit class="form-control" placeholder="" name="mobile">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">@lang('messages.supplier.contact_person_designation')</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="" name="designation">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">@lang('messages.supplier.contact_person_is_primary')</label>
                                    <div class="col-lg-9">
                                        <label class="fancy-checkbox mt-2">
                                            <input type="checkbox" placeholder="" name="is_primary" value="1">
                                            <span><i></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>                
            </div>
        </div>
    </div>
    @endsection
    @section('script')
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCqLRWPCHXYGR_xA8CvkeinflInRCwrwpQ"></script>
    <script type="text/javascript" src="{{asset('js/suppliers/form.js')}}"></script>
    @endsection
    @section('css')
    <style type="text/css">
    .disabledTab{
    /*pointer-events: none;*/
    }
    </style>
    @endsection