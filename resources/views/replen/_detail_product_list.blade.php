@if(!empty($locationsData))    
    <table class="table custom-table bordered-tbl table-striped">
        <thead>
            <tr>
                <th></th>
                <th>@lang('messages.replen.barcode')</th>
                <th>@lang('messages.replen.case_loose')</th>
                <th>@lang('messages.replen.qty_box')</th>
                <th>@lang('messages.replen.no_of_box')</th>
                <th>@lang('messages.replen.total_qty')</th>
                <th>@lang('messages.replen.bbd')</th>
            </tr>
        </thead>
        @php

        @endphp
        <tbody>
        @foreach($locationsData as $locations)        
            <tr>
                <td>
                    <label class="fancy-radio">
                        <input type="radio" name="barcode" value="{{ isset($locations->id)?$locations->id:''}}" />
                        <span><i></i></span>
                    </label>
                </td>
                <td>{{ isset($locations->barcode)?$locations->barcode:'-'}}</td>
                <td>{{ isset($locations->case_type)?barcodeType($locations->case_type):'-'}}</td>
                <td class="qty_box_{{ isset($locations->id)?$locations->id:''}}">{{ isset($locations->qty_per_box)?$locations->qty_per_box:'-'}}</td>
                <td class="bold no_of_box_{{ isset($locations->id)?$locations->id:''}}">{{ isset($locations->total_boxes)?$locations->total_boxes:'-'}}</td>
                <td>{{ isset($locations->qty)?$locations->qty:'-'}}</td>
                <td>{{ isset($locations->best_before_date)?system_date($locations->best_before_date):'-'}}</td>
            </tr>                    
        @endforeach
        </tbody>
    </table>
@endif