<!-- <div class="checklist-container">
    <a href="javascript:void(0)" class="btn-checklist-toggle">
        <span class="icon-moon icon-Spot-Check font-18"></span>
    </a>  -->
    <input type="hidden" name="booking_id" id="bookingQC" value="{{ $booking_details->id }}">       
    <div class="checklist-detail">
            <h3 class="title">Supplier Delivery Note</h3>
            <form id="delivery-form" method="post" action="{{route('api-booking.update',$booking_details->id)}}" enctype="multipart/form-data" name="delivery-form">
                 @method('PUT')  
                <input type="hidden" name="booking_id" id="booking_id" value="{{ $booking_details->id }}">
                <div class="d-flex align-items-center mb-4">
                    <span class="font-14-dark mr-2">Delivery Note No.</span>
                    <span class="font-14-dark bold"><input type="text" name="delivery_note_number" class="form-control" id="delivery_note_number" value="{{ $booking_details->delivery_note_number }}"></span>
                </div>

                <div class="upload-img-container d-flex-align-end mb-5">
                    <figure>
                        <img src="{{ $booking_details->delivery_notes_picture }}" width="100" id="delivery_note_preview" height="100" style="object-fit: contain;max-width: 100px;max-height: 100px"> 
                        <div class="dn-file">
                            <input type="file" id="dn_file" name="delivery_notes_picture" class="delivery_notes_picture" accept="image/*">
                            <label for="dn_file">Choose File</label>
                        </div> 
                    </figure> 
                    @if(!is_null($booking_details->getOriginal('delivery_notes_picture')))
                        <a class="btn-delete" href="javascript:void(0);" id="deleteImage" attr-val="2" onclick="removeImage('{{ $booking_details->id }}')"><span class="icon-moon icon-Delete" title="Delete Image"></span></a>   
                        
                    @else
                        <a class="btn-delete" href="javascript:void(0);" id="deleteImagenull" attr-val="2" onclick="removeImage('{{ $booking_details->id }}')" style="display: none"><span class="icon-moon icon-Delete" title="Delete Image"></span></a>   
                    @endif
                     <a class="btn-delete" href="javascript:void(0);" id="deleteImagenull" attr-val="2" onclick="removeImage('{{ $booking_details->id }}')" style="display: none"><span class="icon-moon icon-Delete" title="Delete Image"></span></a>    
                     <button class="btn btn-blue btn-header px-4 saveDeliveryNoteData mt-3"  title="@lang('messages.modules.button_save')" form="delivery-form" style="float: right;">@lang('messages.modules.button_save')</button>                           
                </div>

            </form>
             <hr>

            {{-- Start Pallet Receive and Return Design --}}
                @include('material_receipt.booking-pallet-form')
            {{-- End Pallet Receive Design --}}

            <hr>
            <h3 class="title mb-0">QC Checklist
            <a class="btn-comman" title="@lang('messages.common.print_qc')" attr-val="{{ $booking_details->id }}" id="printQC" onclick="printQCChecklist('{{ $booking_details->id }}')"><span class="icon-moon icon-Print"></span></a>
            </h3>
            <hr>
            
            <div class="form-group">               
                <label class="font-12-dark mb-2">Select products</label>                
                <div>
                    <select class="form-control custom-select-search bold" multiple="" id="product_qc">
                        @forelse($productList as $productKey=>$productVal)
                        <option value="{{ $productVal->id }}" {{ in_array($productVal->id,$selectedProductQc) ? "selected=selected" : "" }}>{{ $productVal->title }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                
            </div>
            <div class="form-group load_data">
            </div>
    </div>
<!-- </div>
<iframe name="print_frame" width=0 height=0 frameborder=0 src=about:blank>
  
</iframe> -->