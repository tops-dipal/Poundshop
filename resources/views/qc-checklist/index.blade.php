@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.qc_checklist'))
	<div class="content-card custom-scroll">
		<div class="content-card-header">
			<h3 class="page-title">@lang('messages.modules.qc_checklist')</h3>	
			<div class="center-items">
	            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_checklist')" />
	              <span class="refresh"></span>
	        </div>  
	        <div class="right-items"> 
	            @can('importduty-create')
	            <a class="btn btn-add btn-light-green btn-header" href="{{ route('qc-checklist.create') }}" title="@lang('messages.common.add_qc_checklist')">
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
				<div class="table-responsive">
				<table id="importduty_table" class="display">
				    <thead>
				        <tr>
				            <th>
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
		                                  @lang('messages.common.delete_qc_checklist')
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
				            <th class="m-w-120">@lang('messages.qc.qc_checklist_name')</th>
				            <th>@lang('messages.qc.created_on')</th>
				            <th data-class-name="action action-three">@lang('messages.table_label.action')</th>				
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
	</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/qc-checklist/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection