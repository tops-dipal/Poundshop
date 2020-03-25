<button id="btnFilter" class="btn btn-filter btn-header"><span class="icon-moon icon-Filter"></span>@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/></button>
<div class="search-filter-dropdown">
    <form class="form-horizontal form-flex" id="po-search-form">
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
                                <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.purchase_order.filters.po_status')</label>
                                <div class="col-lg-7">
                                    <select class="form-control" id="po_status" name="po_status">
                                        <option value="">@lang('messages.purchase_order.filters.po_status')</option>
                                        @foreach(config('params.po_status') as $key=>$value)
                                        <option value="{{$value}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.purchase_order.filters.category')</label>
                                <div class="col-lg-7">
                                    <select id="supplier_category" class="supplier_category form-control" name="supplier_category">
                                        <option value="">@lang('messages.purchase_order.filters.category') </option>
                                        @foreach(config('params.supplier_category') as $key=>$value)
                                        <option value="{{$value}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.purchase_order.filters.name')</label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" maxlength="50" value="">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="uk_po" id="uk_po" value="0" />
                                <span><i></i> @lang('messages.purchase_order.filters.uk')</span>
                            </label>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="import_po" id="import_po" value="0" />
                                <span><i></i> @lang('messages.purchase_order.filters.import')</span>
                            </label>
                        </div>

                        <div class="col-lg-12">
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="missing_photo" id="missing_photo" value="0" />
                                <span><i></i> @lang('messages.purchase_order.filters.photo')</span>
                            </label>
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="missing_information" id="missing_information" value="0" />
                                <span><i></i> @lang('messages.purchase_order.filters.info')</span>
                            </label>
                        </div>

                        <div class="col-lg-12">
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="outstanding_po" id="outstanding_po" value="0" />
                                <span><i></i> Outstanding Purchase Order</span>
                            </label>

                        </div>

                        <div class="col-lg-12">
                            <label class="fancy-checkbox">
                                <input type="checkbox" name="pending_descripancy" id="pending_descripancy" value="0" />
                                <span><i></i> PO with Pending Descripancy</span>
                            </label>

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