@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.tax_paymnet_report'))

<div class="content-card custom-scroll">
	<div class="content-card-header">
		<h3 class="page-title">@lang('messages.modules.tax_paymnet_report')</h3>
		<div class="right-items">	
			<button id="btnFilter" class="btn btn-filter btn-header"><span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count">(1)</span><span class="icon-moon icon-Drop-Down-1"/></button>
			 <div class="search-filter-dropdown">
               <form id="tax-report-form"  method="post" class="form-horizontal form-flex">
                    <div class="form-fields">
                       
                        <div class="filter-container">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.tax_report.goods_receive_date_range'):</label>
                                            <div class="col-lg-3">
                                               <input type="text" class="form-control datepicker" name="from_date" id="from_date" value="{{ date('d-M-Y',strtotime(date('Y-m-01'))) }}">
                                               
                                            </div>
                                            <div class="col-lg-3">
                                            	<input type="text" class="form-control datepicker" name="to_date" id="to_date" value="{{ date('d-M-Y',strtotime(date('Y-m-t'))) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                           <label class="col-lg-5 col-form-label">@lang('messages.inventory.vat_type')</label>
                                            <div class="col-lg-7" id="vat_types">
                                                <label class="fancy-checkbox">
                                                    <input type="checkbox" class="vat_type" name="vat_type[]" value="0"/>
                                                    <span><i></i>@lang('messages.table_label.cat_standard')</span>
                                                </label>
                                                <label class="fancy-checkbox">
                                                    <input type="checkbox" class="vat_type" name="vat_type[]" value="1"/>
                                                    <span><i></i>@lang('messages.tax_report.zero_rated')</span>
                                                </label>
                                                <label class="fancy-checkbox">
                                                    <input type="checkbox" class="vat_type" name="vat_type[]" value="2"/>
                                                    <span><i></i>@lang('messages.tax_report.mix_rated')</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.tax_report.suppliers')</label>
                                            <div class="col-lg-7">
                                               <select class="form-control custom-select-search" name="supplier_id" id="supplier_id">
			                    				<option value="">--Select Supplier--</option>
			                    				@forelse($suppliers as $supKey=>$supVal)
			                    				<option value="{{ $supVal->id }}">{{ $supVal->name }}</option>
			                    				@empty
			                    				@endforelse
			                    			</select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                           <label class="col-lg-5 col-form-label">@lang('messages.purchase_order.items.sku')</label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" id="sku" name="sku">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.purchase_order.country')</label>
                                            <div class="col-lg-7">
                                                <select  class="form-control" name="country_id" id="country_id">
			                    				<option value="">---Select @lang('messages.supplier.country')---</option>
			                    				@php
	                                                    $sel_country_id = !empty(old('country_id')) ? old('country_id') : @$result->country_id;
	                                                @endphp

	                                               
	                                                @foreach($countries as $country)
	                                                    <option value="{{$country->id}}" {{($sel_country_id == $country->id) ? 'selected="selected"': '' }}>{{$country->name}}</option>
	                                                @endforeach
			                    			</select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.tax_report.po_type')</label>
                                            <div class="col-lg-7" id="po_types">
                                                 <label class="fancy-checkbox">
                                                    <input type="checkbox" class="po_type" name="po_import_type[]" value="1"/>
                                                    <span><i></i>UK P.O.s</span>
                                                </label>
                                                 <label class="fancy-checkbox">
                                                    <input type="checkbox"  class="po_type" name="po_import_type[]" value="2"/>
                                                    <span><i></i>Import P.O.s</span>
                                                </label>
                                                
                                            </div>
                                        </div>
                                    </div>
                                   
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                      <input type="button" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch();">
			             <button class="btn btn-gray btn-header px-4" id="reset" type="button">@lang('messages.common.reset')</button>
                    </div>
                </form>
            </div>
		</div>					
	</div>
	<div class="card-flex-container ">					    
		<div class="container-fluid h-100 d-flex flex-column">
			<div class="container-info">
               
                <div class="form">
                    
                    <div class="form-field">
                        <label class="custom-lbl">@lang("messages.tax_report.total_import_duty"):</label>
                        <span class="pound-sign">@lang("messages.common.pound_sign")</span>  <span class="total_import_duty"></span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang("messages.tax_report.total_vat"):</label>
                        <span class="pound-sign">@lang("messages.common.pound_sign")</span> <span class="total_vat"></span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang("messages.tax_report.total_tax"):</label>
                        <span class="pound-sign">@lang("messages.common.pound_sign")</span> <span class="total_tax"></span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang("messages.tax_report.total_uk_po"):</label>
                        <span class="pound-sign">@lang("messages.common.pound_sign")</span>  <span class="total_vat_on_uk"></span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang("messages.tax_report.total_import_po"):</label>
                        <span class="pound-sign">@lang("messages.common.pound_sign")</span>  <span class="total_vat_on_import"></span>
                    </div>                    
                    
                </div>
            </div>    
               
         	
                
 			<table id="data_table" class="display">
 				<thead>
 					<tr>
 						<th>@lang("messages.tax_report.po_num")</th>
 						<th>@lang("messages.tax_report.goods_receive_date")</th>
 						<th>Supplier</th>
 						<th>@lang("messages.tax_report.uk_import")</th>
 						<th class="dt-head-align-right">
                            <span class="dt-head-text">
                                @lang("messages.tax_report.amount_before_vat")
                            </span>
                        </th>
 						<th class="dt-head-align-right">
                            <span class="dt-head-text">
                                @lang("messages.tax_report.import_duty")
                            </span>
                        </th>
 						<th class="dt-head-align-right">
                            <span class="dt-head-text">
                                @lang("messages.tax_report.vat_import")
                            </span>
                        </th>
 						<th class="dt-head-align-right">
                            <span class="dt-head-text">
                                @lang("messages.tax_report.vat_uk")
                            </span>
                        </th>
 						<th class="dt-head-align-right">
                            <span class="dt-head-text">
                                @lang("messages.tax_report.val_zero_rated")
                            </span>
                        </th>
 					</tr>
 				</thead>
 				<tbody>
 					
					
				
 				</tbody>
 			</table>
         	 </div>	
	         	
			
		</div>
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/po/tax-payment-report.js?v='.CSS_JS_VERSION)}}"></script>
@endsection