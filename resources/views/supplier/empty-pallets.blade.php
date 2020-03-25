<form action="{{route('api-supplier.store')}}" method="POST" class="form-horizontal form-flex" role="form" tab_switch_save id="form-empty-pallets">
    <div>
        @if(count($pallets)>0)
        <div class="container-info">
            
            <div class="form">
                @forelse($pallets as $palletKey=>$palletVal)
                
                <div class="form-field">
                    <label class="custom-lbl">{{ $palletVal->name }}:</label>
                    <span class="total_import_duty">{{ $palletVal->total_return_pallets - $palletVal->total_received_pallets }}</span>
                </div>
                @empty
                @endforelse
                
            </div>
        </div>
        @endif
        <div class="table-responsive mt-2">
            <table id="supplier_contact_person" class="table display dataTable no-footer table-striped custom-table">
                <thead>
                    <tr>
                        
                        <th>@lang('messages.bookings.booking_table.bookin_ref_num')</th>
                        <th>@lang('messages.supplier.date_received')</th>
                        
                        @forelse($pallets as $palletKey=>$palletVal)
                        <th data-pallet="{{ $palletVal->id }}">{{ $palletVal->name }}</th>
                        @empty
                        @endforelse
                    </tr>
                </thead>
                <tbody>
                    @php
                    
                    $i=1;
                    @endphp
                    @forelse($palletBookings as $bookingKey=>$bookingVal)
                    @if(count($bookingVal->bookingPallets)>0)
                    <tr>
                        
                        <td><div class="min-h-35"><a href="{{route('material_receipt.index',$bookingVal->id)}}">{{ $bookingVal->booking_ref_id }}</a></div></td>
                        <td>{{ date('d-M-Y',strtotime($bookingVal->arrived_date)) }}</td>
                        
                        @forelse($pallets as $palletKey=>$palletVal)
                        @php
                        $receivedPallet=\App\BookingPallet::join('bookings', function($join) use ($id)
                        {
                        $join->on('bookings.id', '=', 'booking_pallets.booking_id');
                        $join->where('bookings.supplier_id','=', $id);
                        })->where('booking_id',$bookingVal->id)->where('pallet_type',"1")->where('pallet_id',$palletVal->id)->sum('booking_pallets.num_of_pallets');
                        $returnPallet=\App\BookingPallet::join('bookings', function($join) use ($id)
                        {
                        $join->on('bookings.id', '=', 'booking_pallets.booking_id');
                        $join->where('bookings.supplier_id','=', $id);
                        })->where('booking_id',$bookingVal->id)->where('pallet_type',"2")->where('pallet_id',$palletVal->id)->sum('booking_pallets.num_of_pallets');
                        $result=$returnPallet-$receivedPallet;
                        @endphp
                        <td>{{ $result }}</td>
                        @php
                        $i++;
                        @endphp
                        @empty
                        
                        @endforelse
                        
                        
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="{{ (2+count($pallets)) }}" ><center>@lang('messages.common.no_records_found')</center></td></tr>
                        @endforelse
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>