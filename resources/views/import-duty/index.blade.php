@extends('layouts.app')
@section('content')
@section('title',__('messages.commodity_code_master.import_duty'))
	<div class="content-card custom-scroll">
		<div class="content-card-header">
			<h3 class="page-title">@lang('messages.commodity_code_master.import_duty')</h3>	
			<div class="center-items">
	            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_duty')" />
	              <span class="refresh"></span>
	        </div>  
	        <div class="right-items"> 
	            @can('importduty-create')
	            <a class="btn btn-add btn-light-green btn-header" href="{{ route('import-duty.create') }}" title="@lang('messages.import_duty_master.add_duty')">
	                <span class="icon-moon icon-Add"></span>
	                <!-- @lang('messages.modules.pallets_add') -->
	            </a>
	            @endcan	
			<!-- <div class="right-items">				
				<a class="btn btn-add btn-blue" href="{{ route('commodity-codes.create') }}">
                    <span class="icon-moon icon-Supplier"></span>
                    @lang('messages.commodity_code_master.add_commodity_code')
                </a>

				<button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span> @lang('messages.commodity_code_master.delete_commodity_code')</button> -->
			</div>					
		</div>	
		<div class="card-flex-container d-flex">					    
			<div class="d-flex-xs-block">
				<table id="importduty_table" class="display">
				    <thead>
				        <tr>
				            <th class="remove_sort">
				            	<div class="d-flex">
					            	<label class="fancy-checkbox">
					                    <input name="ids[]" type="checkbox" class="master">
					                    <span><i></i></span>
					                </label>
					                <div class="dropdown bulk-action-dropdown">
		                                <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                                    <span class="icon-moon icon-Drop-Down-1"/>
		                                </button>
		                                
		                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
		                                    <h4 class="title">Bulk action</h4>
		                                    <button class="btn btn-add delete-many">
		                                    <span class="icon-moon red icon-Delete"></span>
		                                  @lang('messages.commodity_code_master.delete_commodity_code')
		                                    </button>
		                                    <!-- <button class="btn btn-add delete-many">
		                                    <span class="icon-moon yellow icon-Delete"></span>
		                                    Select All
		                                    </button>
		                                    <button class="btn btn-add delete-many">
		                                    <span class="icon-moon gray icon-Delete"></span>
		                                    Deselect All
		                                    </button> -->
		                                </div>
		                            </div>
					            </div>
				            </th>
				            <th>@lang('messages.commodity_code_master.commodity_code')</th>
				            <th>@lang('messages.commodity_code_master.commodity_code_desc')</th>
				            <th>@lang('messages.import_duty_master.rate_per')</th>
				            <th>@lang('messages.user_management.country')</th>
				            <th data-class-name="action">@lang('messages.table_label.action')</th>				
				        </tr>
				    </thead>
				    <tbody>
				    	
				    		<!--  -->
				    	
				    </tbody>
				    <tbody>
				        
				    </tbody>
				</table>				
			</div>
		</div>
	</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/import-duty/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection