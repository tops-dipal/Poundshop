@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.pallets_list'))
<div class="content-card custom-scroll">
        <div class="content-card-header">
            <h3 class="page-title">@lang('messages.modules.pallets_list')</h3>     
            <div class="right-items">
                @can('pallet-create')
                <a class="btn btn-add btn-blue" href="{{ route('pallets.create') }}">
                    <span class="icon-moon icon-Supplier"></span>
                    @lang('messages.modules.pallets_add')
                </a>
                @endcan
                
                @can('pallet-delete')
                    <button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span>@lang('messages.modules.pallets_delete')</button>
                @endcan
            </div>                  
        </div>  
        <div class="card-flex-container d-flex">                        
        <div class="d-flex-xs-block">
                        <table id="pallets_table" class="display">
                            <thead>
                                <tr>
                                    <th>
                                    <div class="d-flex">
                                        <label class="fancy-checkbox">
                                            <input name="ids[]" type="checkbox" class="master">
                                            <span><i></i></span>
                                        </label>
                                    </div>
                                    </th>
                                    <th>@lang('messages.table_label.pallet_name')</th>
                                    <th>@lang('messages.table_label.length')</th>
                                    <th>@lang('messages.table_label.width')</th>
                                    <th>@lang('messages.table_label.stck_height')</th>
                                    <th>@lang('messages.table_label.max_vol')</th>                                    
                                    <th>@lang('messages.table_label.max_weight')</th>
                                    <th>@lang('messages.table_label.qty')</th>                                    
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
<script type="text/javascript" src="{{asset('js/pallets/index.js')}}"></script>
@endsection