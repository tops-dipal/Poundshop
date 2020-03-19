@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))
@section('content')
<div class="content-card custom-scroll">
        <div class="content-card-header">
            <h3 class="page-title">{{$page_title}}</h3>		
            <div class="right-items">
                @can('supplier-create')
                <a class="btn btn-add btn-blue" href="{{ route('supplier.create') }}">
                    <span class="icon-moon icon-Supplier"></span>
                    Add Supplier
                </a>
                @endcan
                
                @can('supplier-delete')
                    <button class="btn btn-add btn-red"><span class="icon-moon icon-Delete"></span>Delete Suppliers</button>
                @endcan
            </div>					
        </div>	
        <div class="card-flex-container d-flex">					    
        <div class="table-responsive">
                        <table id="listing_table" class="display">
                            <thead>
                                <tr>
                                    <th>
                                    <div class="d-flex">
                                        <label class="fancy-checkbox">
                                            <input name="agree" type="checkbox">
                                            <span><i></i></span>
                                        </label>
                                    </div>
                                    </th>
                                    <th>@lang('messages.table_label.supplier')</th>
                                    <th>@lang('messages.table_label.account')</th>
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
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/suppliers/index.js')}}"></script>
@endsection