    <div class="search-filter-dropdown">
       <form id="week-booking-form"  method="post" class="form-horizontal form-flex">
            <div class="form-fields">
               
                <div class="filter-container">
                        <h2 class="title">@lang('messages.modules.filter_by')</h2>
                        <div class="container-fluid p-0">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row align-items-center">
                                        <label for="inputPassword" class="col-lg-5 col-form-label">@lang('messages.bookings.booking_table.bookin_date')</label>
                                        <div class="col-lg-7">
                                            <input type="text" class="form-control datepicker" id="booking_date" name="booking_date" value="" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group row align-items-center">
                                        <label class="fancy-checkbox col-lg-5 col-form-label">
                                            <input name="booking_status[]" type="checkbox" class="form-control" value="1" id="booking_status_comp">
                                            <span><i></i>@lang('messages.goods_in_master.status_completed')</span>
                                        </label>
                                        <label class="fancy-checkbox col-lg-5 col-form-label">
                                            <input name="booking_status[]" type="checkbox" class="form-control" value="2" id="booking_status_not_comp">
                                            <span><i></i>@lang('messages.goods_in_master.status_not_completed')</span>
                                        </label>
                                    </div>
                                </div>                       
                            </div>
                        </div>
                    </div>
            </div>
            <div class="form-buttons">
              <input type="button" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch();">
	             <button class="btn btn-gray btn-header px-4 cancle_fil" id="reset" type="button">Reset</button>
            </div>
        </form>
    </div>