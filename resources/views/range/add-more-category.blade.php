<div class="form-group row addMoreCategory{{ $nextCount }}">
    <label for="inputPassword" class="col-lg-4 col-form-label">@lang("messages.range_management.cat_name")<span class="asterisk">*</span></label>
    <div class="col-lg-8">
        <div class="input-btn">
            <input type="text" class="form-control" id="" placeholder="" name="category_name[]">
            <div class="btn-container addMoreCategory{{ $nextCount }}">
                <button type="button" class="btn btn-remove remove">-</button>
            </div>
        </div>
        <div class="row mt-3 addMoreCategory{{ $nextCount }}">
            <div class="col-lg-6">
                <label class="fancy-radio">
                    <input type="radio" name="seasonal_status[{{$nextCount}}]" value="2" class="seasonal_status" checked="">
                    <span><i></i>@lang("messages.range_management.non_seasonal")</span>
                </label>        
            </div>
            <div class="col-lg-6">
                <label class="fancy-radio">
                    <input type="radio" name="seasonal_status[{{$nextCount}}]" value="1" class="seasonal_status">
                    <span><i></i>@lang("messages.range_management.seasonal")</span>
                </label>
            </div>    
        </div>
        <div class="mt-2  seasonal_show{{ $nextCount }} hidden addMoreCategory{{ $nextCount }}">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.retro_from')</label>
                <div class="col-lg-8">
                    <div class="d-flex input-select-group">
                        <input type="number" name="seasonal_range_fromdate[]" value="" class="form-control seasonal_show" min="1" max="31">
                        <select name="seasonal_range_frommonth[]" class="form-control seasonal_show">
                            @php
                            $monthArr=array("1"=>"Jan","2"=>"Feb","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");
                            @endphp
                            @foreach($monthArr as $monthKey=>$monthVal)
                            <option value="{{ $monthKey }}">{{ $monthVal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="seasonal_show{{ $nextCount }} hidden addMoreCategory{{ $nextCount }}">
            <div class="form-group row">
                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.retro_to')</label>
                <div class="col-lg-8">
                    <div class="d-flex input-select-group">
                        <input type="number" name="seasonal_range_todate[]" value="" class="form-control seasonal_show" min="1" max="31">
                        <select name="seasonal_range_tomonth[]" class="form-control seasonal_show">
                            @php
                            $monthArr=array("1"=>"Jan","2"=>"Feb","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");
                            @endphp
                            @foreach($monthArr as $monthKey=>$monthVal)
                            <option value="{{ $monthKey }}">{{ $monthVal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



