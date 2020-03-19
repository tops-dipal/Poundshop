@extends('layouts.app')
@section('title',__('messages.purchase_order.revision.view'))
@section('content')

<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">@lang('messages.purchase_order.revision.view')</h3>
    </div>
    <div class="card-flex-container">
        <div class="container-fluid">

            @if($revisionData->purchase_order_content['po_import_type'] == 2)
            <div class="container-info">
                <h3 class="title">Container Info.</h3>
                <div class="form">

                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.purchase_order.items.tables.delivery_charge')</label>
                        <span>{{$revisionData->purchase_order_content['total_delivery_charge']}}</span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.purchase_order.items.tables.tot_space')</label>
                        <span>{{$revisionData->purchase_order_content['total_space']}}</span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.purchase_order.items.tables.cost_cube')</label>
                        <span>{{$revisionData->purchase_order_content['cost_per_cube']}}</span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.purchase_order.items.tables.tot_cube')</label>
                        <span>{{$revisionData->purchase_order_content['total_number_of_cubes']}}</span>
                    </div>
                    <div class="form-field">
                        <label class="custom-lbl">@lang('messages.purchase_order.items.tables.space_remaining')</label>
                        <span>{{$revisionData->purchase_order_content['remaining_space']}}</span>
                    </div>

                </div>
            </div>
            @endif
            <div class="table-responsive">
                <table class="table custom-table">
                    @if($revisionData->purchase_order_content['po_import_type'] == 1)
                    <thead>
                        @include('purchase-orders.uk-po')
                    <thead>
                        @else
                    <thead>
                        @include('purchase-orders.import-po')
                    <thead>
                        @endif

                    <tbody>
                        @if($revisionData->purchase_order_content['po_import_type'] == 1)
                        @include('purchase-orders.revise-uk-po-content')
                        @else
                        @include('purchase-orders.revise-import-po-content')
                        @endif

                    </tbody>
                    <tfoot>
                        @if($revisionData->purchase_order_content['po_import_type'] == 1)
                        <tr>
                            <td colspan="4" align="right">
                                <span class="title">@lang('messages.purchase_order.items.tables.sub_total')</span>
                            </td>
                            <td colspan="4" align="left">
                                <span class="desc">{{$revisionData->purchase_order_content['sub_total']}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">
                                <span class="title">@lang('messages.purchase_order.items.tables.margin')</span>
                            </td>
                            <td colspan="4" align="left">
                                <span class="desc">{{$revisionData->purchase_order_content['total_margin']}}%</span>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="4" align="right">
                                <span class="title">@lang('messages.purchase_order.items.tables.sub_total')</span>
                            </td>
                            <td colspan="4" align="left">
                                <span class="desc">{{$revisionData->purchase_order_content['sub_total']}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">
                                <span class="title">@lang('messages.purchase_order.items.tables.import_duty')</span>
                            </td>
                            <td colspan="4" align="left">
                                <span class="desc">{{$revisionData->purchase_order_content['total_import_duty']}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">
                                <span class="title">@lang('messages.purchase_order.items.tables.delivery_charge')</span>
                            </td>
                            <td colspan="4" align="left">
                                <span class="desc">{{$revisionData->purchase_order_content['total_delivery_charge']}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">
                                <span class="title">@lang('messages.purchase_order.items.tables.total_cost')</span>
                            </td>
                            <td colspan="4" align="left">
                                <span class="desc">{{$revisionData->purchase_order_content['total_cost']}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">
                                <span class="title">@lang('messages.purchase_order.items.tables.margin')</span>
                            </td>
                            <td colspan="4" align="left">
                                <span class="desc">{{$revisionData->purchase_order_content['total_margin']}}%</span>
                            </td>
                        </tr>
                        @endif

                    </tfoot>
                </table>
            </div>

            <h3 class="page-title-inner mt-5 mb-3">@lang('messages.purchase_order.po_detail')</h3>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.supplier')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['supplier']['name']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.supplier_contact')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['supplier_contact']['name']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.po_number')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['po_number']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.status')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{array_search($revisionData->purchase_order_content['po_status'], config('params.po_status')) }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.po_type')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{array_search($revisionData->purchase_order_content['po_import_type'],config('params.po_import_type'))}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.country')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['purchase_order_country']['name']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.incorterms')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['incoterms']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.mode_of_shipment')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{array_search($revisionData->purchase_order_content['mode_of_shipment'],config('params.shippment'))}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.supplier_order_number')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['supplier_order_number']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.ord_date')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['po_date']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.cancelled_date')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['po_cancel_date']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.expected_date')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['exp_deli_date']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.notes')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['notes']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.warehouse')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['warehouse']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.add1')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['street_address1']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.form.add2')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['street_address2']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.country')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['country']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.state')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['state']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.city')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['city']}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label py-1"><strong>@lang('messages.purchase_order.zipcode')</strong></label>
                        <div class="col-lg-8 col-form-label py-1">
                            {{$revisionData->purchase_order_content['zipcode']}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection