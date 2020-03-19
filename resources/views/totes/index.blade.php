@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.totes_master'))
	<div class="content-card custom-scroll">
		<div class="content-card-header">
			<h3 class="page-title">@lang('messages.modules.totes_master')</h3>	
			<div class="center-items">
	            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_totes')" />
	            <span class="refresh"></span>
	        </div>	
			<div class="right-items">	
			@can('totes-create')
            <a class="btn btn-add btn-light-green btn-header" href="{{ route('totes.create') }}" title="@lang('messages.totes.totes_add')">
                <span class="icon-moon icon-Add"></span>
               
            </a>
            @endcan			
				

			</div>					
		</div>	
		<div class="card-flex-container d-flex">					    
			<div class="d-flex-xs-block">
				<table id="totes_table" class="display">
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
                                            @lang('messages.totes.totes_delete')
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
				            <th>@lang('messages.totes.totes_name')</th>
				            <th>@lang('messages.table_label.category')</th>
				            <th>@lang('messages.table_label.length')</th>
				            <th>@lang('messages.table_label.width')</th>
				            <th>@lang('messages.totes.storable_height')</th>
				            <th>@lang('messages.totes.max_weight')</th>
				            <th>@lang('messages.table_label.qty')</th>
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
<script type="text/javascript" src="{{asset('js/totes/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection