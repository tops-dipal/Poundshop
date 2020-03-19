@extends('layouts.app')
@section('content')
@section('title',__('messages.modules.locations_list'))
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.modules.locations_list')</h3> 
        <div class="center-items">
            <input type="text" class="txt-listing-search" id="search_data" name="" placeholder="@lang('messages.common.search_location')" />
            <span class="refresh" title="@lang('messages.modules.clear_filter')"></span>
        </div>    
        <div class="right-items">
            <button id="btnFilter" class="btn btn-filter btn-header"><span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/></button>
            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex" id="location_advance_search">
                    <div class="form-fields">
                        <!-- <div class="sort-container">
                            <h2 class="title">@lang('messages.modules.sort_by')</h2>
                            <label class="fancy-checkbox">
                                <input name="" type="checkbox" class="master">
                                <span><i></i>Supplier who are over Credit limit</span>
                            </label>
                            <label class="fancy-checkbox">
                                <input name="" type="checkbox" class="master">
                                <span><i></i>Suppliers with Retro discount</span>
                            </label>
                        </div> -->
                        <div class="filter-container">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.table_label.aisle')</label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" id="fil_aisle" name="fil_aisle" maxlength="3" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.table_label.rack')</label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" id="fil_rack" name="fil_rack" maxlength="3" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.table_label.floor')</label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" id="fil_floor" name="fil_floor" maxlength="3" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.table_label.box')</label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" id="fil_box" name="fil_box" maxlength="3" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.table_label.location')</label>
                                            <div class="col-lg-7">
                                                <input type="text" class="form-control" id="fil_location" name="fil_location" maxlength="8" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.table_label.select_site')</label>
                                            <div class="col-lg-7">
                                                <select class="form-control" id="fil_site_id" name="fil_site_id">
                                                    <option value="">Select Site</option>
                                                    @if(!empty($warehouses))
                                                        @foreach($warehouses as $row)
                                                            <option value="{{$row->id}}">{{ucfirst($row->name)}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.table_label.location_type')</label>
                                            <div class="col-lg-7">
                                                <select class="form-control" id="fil_location_type" name="fil_location_type">
                                                    <option value="">Select Location Type</option>
                                                    @if(!empty($location_type))
                                                        <?php $i=1;?>
                                                        @foreach($location_type as $row)
                                                            <option value="{{ $i }}">{{ucfirst($row)}}</option>
                                                            <?php $i++;?>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.common.status')</label>
                                            <div class="col-lg-7">
                                                <select class="form-control" id="fil_status" name="fil_status">
                                                    <option value="">Select Status</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <input type="button" class="btn btn-gray cancle_fil" title="@lang('messages.modules.button_cancel')" value="@lang('messages.modules.button_cancel')">
                        <input type="button" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch();">
                    </div>
                </form>
            </div>
            @can('locations-edit')                                                
                <a class="btn btn-add btn-light-green btn-header location_settings" href="{{ route('locations-setting') }}" title="@lang('messages.modules.setting')"><span class="icon-moon icon-Setting"></span></a>
            @endcan
            
            @can('locations-create')
            <a class="btn btn-add btn-light-green btn-header" title="@lang('messages.modules.locations_add')" href="{{ route('locations.create') }}">
                <span class="icon-moon icon-Add"></span>                
            </a>

            @endcan
            
            @can('locations-delete')
                <!-- <button class="btn btn-add btn-red delete-many"><span class="icon-moon icon-Delete"></span>@lang('messages.modules.locations_delete')</button> -->
            @endcan
            
            @can('locations-edit')                
                <!-- <button class="btn btn-add btn-red active-many">@lang('messages.common.active')</button> -->
            @endcan

            @can('locations-edit')                                
                <!-- <button class="btn btn-add btn-red incative-many">@lang('messages.common.inactive')</button> -->
            @endcan

            
            
        </div>                  
    </div>  
    <div class="card-flex-container d-flex">                        
        <div class="d-flex-xs-block">
            <div class="table-responsive">
            <table id="locations_table" class="display">
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
                                            <button class="btn btn-add delete-many">
                                            <span class="icon-moon red icon-Delete"></span>
                                            @lang('messages.modules.locations_delete')
                                            </button>
                                            <button class="btn btn-add btn-red active-many">@lang('messages.common.active')</button>
                                            <button class="btn btn-add btn-red incative-many">@lang('messages.common.inactive')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>@lang('messages.table_label.aisle')</th>
                        <th>@lang('messages.table_label.rack')</th>
                        <th>@lang('messages.table_label.floor')</th>
                        <th>@lang('messages.table_label.box')</th>
                        <th>@lang('messages.table_label.location')</th>
                        <th>@lang('messages.table_label.location_type')</th>
                        <th>@lang('messages.table_label.case_pack')</th>
                        <th>@lang('messages.table_label.length')</th>
                        <th>@lang('messages.table_label.width')</th>
                        <th>@lang('messages.table_label.height')</th>
                        <th>@lang('messages.table_label.cbm')</th>
                        <th>@lang('messages.table_label.stor_weight_short')</th>
                        <th>@lang('messages.common.status')</th>
                        <th data-class-name="action action-three">
                            <div class="m-w-120">@lang('messages.table_label.action')</div>
                        </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table> 
            </div>               
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.modules.locations_edit')</h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    <button type="button" class="btn btn-blue font-12 px-3 ml-2" onclick="saveLocation(this)">@lang('messages.common.save')</button>
                </div>
            </div>
            <div class="modal-body p-4">
                <form id="locationsEditForm" method="post">
                    <input type="hidden" name="edit_record_id" value="" id="edit_record_id">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.location_type')</label>
                                <div class="col-lg-8">
                                    <select class="form-control" id="edi_location_type" name="edi_location_type">                                        
                                        @if(!empty($location_type))
                                            <?php $i=1;?>
                                            @foreach($location_type as $row)
                                                <option value="{{ $i }}">{{ucfirst($row)}}</option>
                                                <?php $i++;?>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.case_pack')</label>
                                <div class="col-lg-8">
                                    <select class="form-control" id="edi_case_pack" name="edi_case_pack">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>                         
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.length')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_length" name="edi_length" maxlength="6" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.width')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_width" name="edi_width" maxlength="6" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.height')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_height" name="edi_height" maxlength="6" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.cbm')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_cbm" name="edi_cbm" value="" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.stor_weight_short_kg')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_stor_weight" name="edi_stor_weight" value="" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);">
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
<script type="text/javascript" src="{{asset('js/locations/index.js?v='.CSS_JS_VERSION)}}"></script>
@endsection