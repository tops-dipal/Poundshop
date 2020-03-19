@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.pallets_list'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.modules.pallets_list')</h3>
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_pallet')" />
            <span class="refresh" title="@lang('messages.modules.clear_filter')"></span>
        </div>
        <div class="right-items">            
            <!-- <button id="btnFilter" class="btn btn-filter  btn-header"><span class="icon-moon icon-Filter"></span>Filter<span class="icon-moon icon-Drop-Down-1"/></button>
            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex">
                    <div class="form-fields">
                        <div class="sort-container">
                            <h2 class="title">Sort By</h2>
                            <label class="fancy-checkbox">
                                <input name="" type="checkbox" class="master">
                                <span><i></i>Supplier who are over Credit limit</span>
                            </label>
                            <label class="fancy-checkbox">
                                <input name="" type="checkbox" class="master">
                                <span><i></i>Suppliers with Retro discount</span>
                            </label>
                        </div>
                        <div class="filter-container">
                            <h2 class="title">filter By</h2>
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">Location<span class="asterisk">*</span></label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" id="" placeholder="" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">Supplier Category<span class="asterisk">*</span></label>
                                            <div class="col-lg-7">
                                                <select class="form-control">
                                                    <option>Category 1</option>
                                                    <option>Category 2</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button class="btn btn-gray">Cancel</button>
                        <button class="btn btn-blue">Apply</button>
                    </div>
                </form>
            </div> -->
            
            @can('pallet-create')
            <a class="btn btn-add btn-light-green btn-header" href="{{ route('pallets.create') }}" title="@lang('messages.modules.pallets_add')">
                <span class="icon-moon icon-Add"></span>
                <!-- @lang('messages.modules.pallets_add') -->
            </a>
            @endcan
            <!-- @can('pallet-delete')
            <button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span>@lang('messages.modules.pallets_delete')</button>
            @endcan -->
        </div>
    </div>
    <div class="card-flex-container d-flex">
        <div class="d-flex-xs-block">
            <table id="pallets_table" class="display">
                <thead>
                    <tr>
                        <th class="remove_sort">
                            <div class="d-flex">
                                <label class="fancy-checkbox">
                                    <input name="ids[]" type="checkbox" class="master">
                                    <span><i></i></span>
                                </label>
                                <div class="dropdown bulk-action-dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                                    <span class="icon-moon icon-Drop-Down-1"/>
                                        </button>                               
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                            <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                            <button class="btn btn-add delete-many" title="@lang('messages.modules.pallets_delete')">
                                            <span class="icon-moon red icon-Delete"></span>
                                            @lang('messages.modules.pallets_delete')
                                            </button>                                 
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>@lang('messages.table_label.pallet_name')</th>
                            <th>@lang('messages.table_label.length')</th>
                            <th>@lang('messages.table_label.width')</th>
                            <th>@lang('messages.table_label.stck_height')</th>
                            <th>@lang('messages.table_label.max_weight')</th>
                            <th>@lang('messages.pallet_master.rentable')</th>
                            <th>@lang('messages.pallet_master.sellable')</th>
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
<script type="text/javascript" src="{{asset('js/pallets/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection