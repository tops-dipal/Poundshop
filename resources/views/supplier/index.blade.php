@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">{{$page_title}}</h3>	
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="Search by Sup. Name, Sup. Account, Cont. Person" />
            <span class="refresh" title="@lang('messages.modules.clear_filter')"></span>
        </div>	
        <div class="right-items">
            <button id="btnFilter" class="btn btn-filter btn-header">
                <span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span>
                <span class="icon-moon icon-Drop-Down-1"/>

            </button>
           
            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex" id="custom_advance_search" method="post">
                    <div class="form-fields">
                        <div class="filter-container" id="custom_advance_search_fields">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>
                             <div class="container-fluid p-0">
                                <div class="row">
                                    
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.supplier.filter_city_country')</label>
                                            <div class="col-lg-7">
                                                <input type="text"  class="form-control" name="filter_city_country" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label class="col-lg-5 col-form-label">@lang('messages.supplier.supplier_category')</label>
                                            <div class="col-lg-7">
                                                <select class="form-control" name="filter_by_category">
                                                    <option value="">@lang('messages.common.select') @lang('messages.supplier.supplier_category')</option>

                                                @foreach(supplierCategory() as $supplier_cat_id => $supplier_cat)
                                                    <option value="{{ $supplier_cat_id }}">{{ $supplier_cat }}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 display-none">
                                        <label class="fancy-checkbox">
                                            <input type="checkbox" name="filter_suppliers_over_credit_limit" value="1">
                                            <span><i></i>
                                                @lang('messages.supplier.filter_suppliers_over_credit_limit')
                                            </span>
                                        </label>
                                    </div>

                                    <div class="col-lg-12">
                                        <label class="fancy-checkbox">
                                            <input type="checkbox" name="filter_suppliers_with_retro_discount" value="1">
                                            <span><i></i>
                                                @lang('messages.supplier.filter_suppliers_with_retro_discount')
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                    <div class="form-buttons">
                        <input type="button" class="btn btn-gray cancle_fil" title="@lang('messages.modules.button_cancel')" value="@lang('messages.modules.button_cancel')">
                        <input type="submit" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch(event);">
                    </div>
                </form>
            </div>
                            
            @can('supplier-create')
            <a class="btn btn-add btn-light-green btn-header" href="{{ route('supplier.form') }}#general" title="@lang('messages.common.add') @lang('messages.modules.supplier')">
                <span class="icon-moon icon-Add"></span>
                <!-- @lang('messages.common.add') @lang('messages.modules.supplier')  -->
            </a>
            @endcan
            
            @can('supplier-delete')
                <!-- <button class="btn btn-add btn-red" onclick="delete_record(this)"><span class="icon-moon icon-Delete"></span>@lang('messages.common.delete') @lang('messages.modules.suppliers') </button> -->
            @endcan
        </div>					
    </div>	
    <div class="card-flex-container d-flex">					    
        <div class="d-flex-xs-block">
            <div class="table-responsive">
            <table id="listing_table" class="display">
                <thead>
                    <tr>
                        <th>
                            <div class="d-flex">
                                <label class="fancy-checkbox">
                                    <input name="agree" type="checkbox" class="master-checkbox">
                                    <span><i></i></span>
                                </label>
                                <div class="dropdown bulk-action-dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                                    <span class="icon-moon icon-Drop-Down-1"/>
                                    </button>                                        
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                        <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                        <button class="btn btn-add delete-many" onclick="delete_record(this)" title="@lang('messages.common.delete') @lang('messages.modules.suppliers')">
                                        <span class="icon-moon red icon-Delete"></span>
                                        @lang('messages.common.delete') @lang('messages.modules.suppliers')
                                        </button>                                           
                                    </div>                                    
                                </div>
                            </div>
                        </th>
                        <th>@lang('messages.table_label.supplier')</th>
                        <th>@lang('messages.table_label.account')</th>
                        <th>@lang('messages.table_label.credit_limit')</th>
                        <th>@lang('messages.table_label.contact')</th>
                        <th>@lang('messages.table_label.email')</th>
                        <th>@lang('messages.table_label.phone')</th>
                        <th>@lang('messages.table_label.city')</th>
                        <th>@lang('messages.table_label.supplier_type')</th>
                        <th data-class-name="action">@lang('messages.table_label.action')</th>				
                    </tr>
                </thead>
                <tbody></tbody>
            </table>	
            </div>			
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/suppliers/index.js')}}"></script>
@endsection