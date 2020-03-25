<div class="row">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">@lang('messages.bookings.form.receiving_site')<span class="asterisk">*</span></label>
                    <div class="col-lg-9">

                        <select class="form-control" id="warehouse" name="warehouse">
                            <option value="">Select Warehouse</option>
                            @if(!empty($booking->warehouse_id))
                            @foreach($wareHouses as $warehouse)
                            @if(isset($booking) && $warehouse->id == $booking->warehouse_id))
                            <option selected="selected" value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @else
                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endif
                            @endforeach
                            @else

                            @foreach($wareHouses as $warehouse)
                            @if($warehouse->is_default == 1)
                            <option selected="selected" value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @else
                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-6 col-form-label">@lang('messages.bookings.form.book_date')<span class="asterisk">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control"  id="book_date" name="book_date" placeholder="" autocomplete="false" value="{{isset($booking->book_date) ? $booking->book_date : '' }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-5 text-right col-form-label">@lang('messages.bookings.form.book_slot')<span class="asterisk">*</span></label>
                    <div class="col-lg-7">
                        <select class="form-control" id="slot" name="slot">
                            <option value="">Select Slot</option>
                            @foreach($slots as $slot)
                            @if(isset($booking) && $slot->id == $booking->slot_id))
                            <option selected="selected" value="{{$slot->id}}">{{date("g:i a", strtotime($slot->from)) .' to '.date("g:i a", strtotime($slot->to))}}</option>
                            @else
                            <option value="{{$slot->id}}">{{date("g:i a", strtotime($slot->from)) .' to '.date("g:i a", strtotime($slot->to))}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group row">
                    <label class="col-lg-6 col-form-label">@lang('messages.bookings.form.no_pallets')<span class="asterisk">*</span></label>
                    <div class="col-lg-6">
                        <input  type="text"  class="form-control"  id="pallet" name="pallet" placeholder="" maxlength="3" value="{{isset($booking->num_of_pallets) ? $booking->num_of_pallets : '' }}" />
                    </div>
                </div>
            </div>
            <div class="col-lg-6" id="estimated-container" @if(isset($radioOption) && ($radioOption == 1 || $radioOption==3)) style="display:none;" @else @if(isset($booking) && ($booking->status == 3 || $booking->status ==1)) style="display:none;" @endif @endif>
                 <div class="form-group row">
                    <label class="col-lg-5 text-right col-form-label">@lang('messages.bookings.form.estimated_val')<span class="asterisk">*</span></label>
                    <div class="col-lg-7">
                        <input type="text" class="form-control"  id="estimated_value" name="estimated_value" placeholder="" maxlength="9" value="{{isset($booking->estimated_value) ? $booking->estimated_value : '' }}" />
                    </div>
                </div>
            </div>


        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group row">
            <!-- <label class="col-lg-12 col-form-label">@lang('messages.bookings.form.comment')</label> -->
            <div class="col-lg-12">
                <textarea class="form-control" name="comment" id="comment" placeholder="Write Comment" rows="5" maxlength="500">{{isset($booking->comment) ? $booking->comment : '' }}</textarea>
            </div>
        </div>
    </div>

</div>