<form id="pallet-form" method="post"  enctype="multipart/form-data" action="{{route('api-booking-pallet.store')}}">
    <div class="pallet_return_receive_div">
        @php
        $sumOfReceivedPallet=0;
        $sumOfReturnPallets=0;
        @endphp
        <div class="d-flex align-items-center">
            <h3 class="font-12-dark bold flex-one">@lang('messages.mr_sidebar.pallet_recived')</h3>
            <h3 class="font-12-dark bold flex-one">@lang('messages.common.quantity')</h3>
            <!-- <a class="btn p-0" style="margin-left: auto; font-size: 16px;" href="javascript:void(0);"  id="add_more_receive_pallet">
                <span class="icon-moon icon-Add1"></span>
            </a> -->
        </div>
        <input type="hidden" name="total_receive_pallet" value="{{ (count($receivedPallets)==0) ? '1' : count($receivedPallets) }}">
        <input type="hidden" name="booking_id" value="{{ $booking_details->id }}">
        <input type="hidden" name="pallet_list" id="pallet_list" value="{{ json_encode($palletList) }}">
        @if(count($receivedPallets)==0)
        <div class="d-flex align-items-center mt-2 mb-2" id="pallet_receive_div_1">
            <span class="font-14-dark mr-2 flex-one">
                <select class="form-control" name="receive_pallets[]" id="receive_pallets_1">
                    <option value="">@lang('messages.mr_sidebar.select_pallet')</option>
                    @forelse($palletList as $palletKey=>$palletVal)
                    <option value="{{ $palletVal->id }}">{{ $palletVal->name }}</option>
                    @empty
                    @endforelse
                </select>
            </span>
            <span class="font-14-dark mr-2 flex-one">
                <input type="text" name="receive_num_of_pallets[]" id="receive_num_of_pallets_1" value="" class="form-control" onkeypress="return isNumber(event)" placeholder="@lang('messages.common.quantity')">
            </span>
            <span class="font-14-dark ml-2">
                <a title="@lang('messages.common.delete')" class="btn-delete btn-receive-delete-pallet" href="javascript:void(0);"  id="pallet_receive_div_1" attr-curr-div="pallet_receive_div_1"><span style="font-size: 16px" class="icon-moon icon-Cancel"></span></a>
            </span>
        </div>
        <div id="add_more_receive_pallet_div">
            
        </div>
        <div class="text-right">
            <a class="btn p-0" href="javascript:void(0);"  id="add_more_receive_pallet" title="@lang('messages.mr_sidebar.add_more_received')">
                <span class="icon-moon icon-Add1"></span>
            </a>
        </div>
        
        @else
        @php
        $countReceive=1;
        @endphp
        @forelse($receivedPallets as $receiveKey=>$receiveVal)
        
        <div class="d-flex align-items-center mb-2" id="pallet_receive_div_{{ $countReceive }}">
            <input type="hidden" name="update_id[]" value="{{ $receiveVal->id }}">
            <span class="font-14-dark mr-2 flex-one">
                <select class="form-control" name="receive_pallets[]" id="receive_pallets_{{ $countReceive }}">
                    <option value="">@lang('messages.mr_sidebar.select_pallet')</option>
                    @forelse($palletList as $palletKey=>$palletVal)
                    <option value="{{ $palletVal->id }}" {{($palletVal->id == $receiveVal->pallet_id) ? 'selected="selected"': '' }}>{{ $palletVal->name }}</option>
                    @empty
                    @endforelse
                </select>
            </span>
            <span class="font-14-dark mr-2 flex-one">
                <input type="text" name="receive_num_of_pallets[]" value="{{ $receiveVal->num_of_pallets }}" id="receive_num_of_pallets_{{ $countReceive }}" class="form-control" placeholder="@lang('messages.common.quantity')">
            </span>
            <span class="font-14-dark margin-left-2">
                <a title="@lang('messages.common.delete')" class="btn-delete btn-receive-delete-pallet" href="javascript:void(0);"  id="pallet_receive_del_{{ $countReceive }}" attr-curr-div="pallet_receive_div_{{ $countReceive }}" data-delete="{{ $receiveVal->id }}"><span style="font-size: 16px" class="icon-moon icon-Cancel"></span></a>
            </span>
        </div>
        @php
        $countReceive++;
        $sumOfReceivedPallet+=$receiveVal->num_of_pallets;
        @endphp
        @empty
        @endforelse
        <div id="add_more_receive_pallet_div">
            
        </div>
        <div class="text-right">
            <a class="btn p-0" href="javascript:void(0);"  id="add_more_receive_pallet" title="@lang('messages.mr_sidebar.add_more_received')">
                <span class="icon-moon icon-Add1"></span>
            </a>
        </div>
        @endif
        <div class="d-flex align-items-center">
            <h3 class="font-12-dark bold mb-2 flex-one">
                @lang('messages.mr_sidebar.pallet_return')
            </h3>
            <h3 class="font-12-dark bold flex-one">@lang('messages.common.quantity')</h3>
           <!--  <a class="btn p-0" style="margin-left: auto; font-size: 16px;" href="javascript:void(0);"  id="add_more_return_pallet">
                <span class="icon-moon icon-Add1"></span>
            </a> -->
        </div>
        <input type="hidden" name="total_return_pallet" id="total_return_pallet" value="{{ (count($returnPallets)==0) ? '1' : count($returnPallets) }}">
        @if(count($returnPallets)==0)
        <div class="d-flex align-items-center mb-2" id="pallet_return_div_1">
            <span class="font-14-dark mr-2 flex-one">
                <select class="form-control" name="return_pallets[]" id="return_pallets_1">
                    <option value="">Select Pallet</option>
                    @forelse($palletList as $palletKey=>$palletVal)
                    <option value="{{ $palletVal->id }}">{{ $palletVal->name }}</option>
                    @empty
                    @endforelse
                </select>
            </span>
            <span class="font-14-dark mr-2 flex-one">
                <input type="text" name="return_num_of_pallets[]" id="return_num_of_pallets_1" value="" class="form-control" placeholder="@lang('messages.common.quantity')">
                
            </span>
            <span class="font-14-dark ml-2">
                <a title="@lang('messages.common.delete')"  class="btn-delete btn-return-delete-pallet" href="javascript:void(0);"  id="pallet_return_del_1" attr-curr-div="pallet_return_div_1"><span style="font-size: 16px" class="icon-moon icon-Cancel"></span></a>
            </span>
        </div>
        <div id="add_more_return_pallet_div">
            
        </div>        
        @else
        @php
        $countReturn=1;
        @endphp
        @forelse($returnPallets as $returnKey=>$returnVal)
        <div class="d-flex align-items-center mb-2" id="pallet_return_div_{{ $countReturn }}">
            <input type="hidden" name="update_return_id[]" value="{{ $returnVal->id }}">
            <span class="font-14-dark mr-2 flex-one">
                <select class="form-control" name="return_pallets[]" id='return_pallets_{{$countReturn}}'>
                    <option value="">Select Pallet</option>
                    @forelse($palletList as $palletKey=>$palletVal)
                    <option value="{{ $palletVal->id }}" {{($palletVal->id == $returnVal->pallet_id) ? 'selected="selected"': '' }}>{{ $palletVal->name }}</option>
                    @empty
                    @endforelse
                </select>
            </span>
            <span class="font-14-dark mr-2 flex-one">
                <input type="text" name="return_num_of_pallets[]" value="{{ $returnVal->num_of_pallets }}" id='return_num_of_pallets_{{$countReturn}}' class="form-control" placeholder="@lang('messages.common.quantity')">
                
            </span>
            <span class="font-14-dark mr-2">
                <a title="@lang('messages.common.delete')" class="btn-delete  btn-return-delete-pallet" href="javascript:void(0);"  id="pallet_return_del_{{ $countReturn }}" attr-curr-div="pallet_return_div_{{ $countReturn }}" data-delete="{{ $returnVal->id }}"><span style="font-size: 16px" class="icon-moon icon-Cancel"></span></a>
            </span>
        </div>
        @php
        $countReturn++;
        $sumOfReturnPallets+=$returnVal->num_of_pallets;
        @endphp
        @empty
        @endforelse
        <div id="add_more_return_pallet_div">
            
        </div>
       <!--  <a class="btn btn-blue" href="javascript:void(0);"  id="add_more_return_pallet">Add Pallet Return </a> -->
        @endif
        <div class="text-right">
             <a class="btn p-0" style="margin-left: auto; font-size: 16px;" href="javascript:void(0);"  id="add_more_return_pallet" title="@lang('messages.mr_sidebar.add_more_return')">
                <span class="icon-moon icon-Add1"></span>
            </a>
        </div>

        <p class="font-14-dark mt-3">@lang('messages.mr_sidebar.pallet_owing'): <span id="owing_count" class="bold">{{ $sumOfReceivedPallet -$sumOfReturnPallets}}</span></p>
        <div class="text-right">
            <button class="btn btn-blue btn-header px-4 savePallet mt-3"  title="@lang('messages.modules.button_save')" onclick="submitPallets()">@lang('messages.modules.button_save')</button>
        </div>
        
    </div>
</form>