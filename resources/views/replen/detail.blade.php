@extends('layouts.app')
@section('content')
@section('title',__('messages.storage.replen'))
<div class="content-card custom-scroll">
   <div class="content-card-header">
        @if(!empty($pallet_pick_location) && !empty($pallet_pick_location->toArray()))
            @php
            $pallet_pick_location_final_data='';
            $pallet_pick_location_final_data.= isset($pallet_pick_location[0]->location)?$pallet_pick_location[0]->location:'';
            $pallet_pick_location_final_data.= ' ';
            $pallet_pick_location_final_data.= isset($pallet_pick_location[0]->type_of_location)?LocationType($pallet_pick_location[0]->type_of_location):'';
            @endphp
        @endif
        <h3 class="page-title">@lang('messages.replen.scan_to_pallet_location') : {{ !empty($pallet_pick_location_final_data)?$pallet_pick_location_final_data:''}}</h3>		
        <div class="right-items">
            <a href="{{route('replen.index')}}" class="btn btn-blue btn-header px-4 redirect_page" title="@lang('messages.modules.button_cancel')">@lang('messages.replen.view_job_list')</a>
            <button class="btn btn-danger btn-header px-4 finish_job"  title="@lang('messages.modules.button_save')">@lang('messages.replen.finish')</button>
        </div>					
    </div>	

    <div class="card-flex-container">
        <div class="container-fluid">
            <div class="d-flex mb-3" style="border: 1px solid #dee2e6">
                <table class="table tbl-replane-product mb-0">
                    <thead>
                        <tr>
                            <th style="height: 56px;">@lang('messages.replen.product_info')</th>
                            <th>@lang('messages.replen.priority')</th>
                            <th>@lang('messages.replen.replen_pend_qty')</th>
                        </tr>                       
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">@lang('messages.replen.product_title'): <span class="bold">{{ isset($replen_data[0]->title)?$replen_data[0]->title:'' }}</span></td>
                        </tr>
                        <tr>                            
                            <td rowspan="3" class="cell-border-right cell-border-bottom">
                                <div class="d-flex align-items-center">
                                    
                                    <!-- <img src="https://methodhome.com/wp-content/uploads/laundry_p-v2-500x500.png" width="60" alt=""> -->

                                    @if (!isset($replen_data[0]->main_image_internal_thumb) && !empty($replen_data[0]->main_image_internal_thumb))
                                    <a href="{{url('/storage/uploads') . '/'.$replen_data[0]->main_image_internal_thumb}}" data-rel="lightcase">
                                    <img src="{{url('/storage/uploads') . '/'.$replen_data[0]->main_image_internal_thumb}}" width="60" height="" alt="">
                                    </a>
                                    @else
                                    <a href="{{url('/img/no-image.jpeg')}}" data-rel="lightcase">
                                    <img src="{{url('/img/no-image.jpeg')}}" width="60" height="" alt="">
                                    </a>
                                    @endif
                                    <div class="pl-2">
                                        <p class="mb-3">@lang('messages.replen.barcode'): <span>{{ isset($replen_data[0]->product_identifier)?$replen_data[0]->product_identifier:'' }}</span></p>
                                        <p>@lang('messages.replen.sku'): <span>{{ isset($replen_data[0]->sku)?$replen_data[0]->sku:'' }}</span></p>
                                    </div>    
                                </div>                                
                            </td>
                           
                            <td>{{ isset($replen_data[0]->priority)?priorityTypes($replen_data[0]->priority):'' }}</td>

                            <td class="highlighted-text">{{ isset($replen_data[0]->replan_qty)?$replen_data[0]->replan_qty:'' }}</td>
                        </tr>

                        <tr>                            
                            <!-- <td class="bold">@lang('messages.replen.pick')</td>
                            <td>{{ isset($replen_data[0]->replan_qty)?$replen_data[0]->replan_qty:'' }}</td> -->
                        </tr>
                        <tr>                            
                            <!-- <td class="bold cell-border-bottom">@lang('messages.replen.dropship')</td>
                            <td class="cell-border-bottom">100</td> -->
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" id="selected_pallet" value="{{ isset($pallet_pick_location[0]->location_id)?$pallet_pick_location[0]->location_id:'' }}">
                <input type="hidden" id="product_id" value="{{ isset($replen_data[0]->product_id)?$replen_data[0]->product_id:'' }}">
                <input type="hidden" id="replen-product-list-url" value="{{route('api-replen-product-list')}}" />
                <input type="hidden" id="replen-finish-pallet" value="{{route('api-replen-finish-pallet')}}" />
                <input type="hidden" id="mylist_link" value="{{route('replen.index')}}" />
                <input type="hidden" id="product-replen-url" value="{{route('api-replen-product')}}" />

                <table class="table tbl-replane-storage mb-0">
                    <thead>
                        <tr>
                            <th colspan="4" class="text-center">@lang('messages.replen.curr_st_deat')</th>
                        </tr>
                        <tr>
                            <th>@lang('messages.replen.locations')</th>
                            <th>@lang('messages.replen.quantity')</th>
                            <th>@lang('messages.replen.avai_spa_qty')</th>
                            <th>@lang('messages.replen.bbd')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        @php
                        $myselect_bulk=isset($replen_data[0]->location)?$replen_data[0]->location:'';
                        $bulk_default_selected=0;
                        $pick_default_selected=0;
                        $counter=0;
                        $total_val=0;
                        $checked='';
                        @endphp

                        @if(!empty($bulk_location_data))
                            @php
                            $total_val=count($bulk_location_data);
                            @endphp
                            @foreach($bulk_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($bulk_putaway_location_data) || !empty($aerosol_bulk_cage_location_data) || !empty($pick_location_data) || !empty($pick_putaway_location_data) || !empty($aerosol_cage_location_data) || !empty($dropshipping_location_data) || !empty($dispatch_location_data) || !empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp
                                @if($myselect_bulk==$newr['location'] && empty($bulk_default_selected))
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td class="highlighted-text">{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                    @php
                                    $bulk_default_selected=1;
                                    @endphp
                                @else
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                @endif
                                @php
                                $counter++;
                                @endphp

                            @endforeach
                        @endif

                        @if(!empty($bulk_putaway_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($bulk_putaway_location_data);
                            @endphp

                            @foreach($bulk_putaway_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($aerosol_bulk_cage_location_data) || !empty($pick_location_data) || !empty($pick_putaway_location_data) || !empty($aerosol_cage_location_data) || !empty($dropshipping_location_data) || !empty($dispatch_location_data) || !empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp
                                @if($myselect_bulk==$newr['location'] && empty($bulk_default_selected))
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td class="highlighted-text">{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                    @php
                                    $bulk_default_selected=1;
                                    @endphp
                                @else
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                @endif
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif


                        @if(!empty($aerosol_bulk_cage_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($aerosol_bulk_cage_location_data);
                            @endphp

                            @foreach($aerosol_bulk_cage_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($pick_location_data) || !empty($pick_putaway_location_data) || !empty($aerosol_cage_location_data) || !empty($dropshipping_location_data) || !empty($dispatch_location_data) || !empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp
                                @if($myselect_bulk==$newr['location'] && empty($bulk_default_selected))
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td class="highlighted-text">{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                    @php
                                    $bulk_default_selected=1;
                                    @endphp
                                @else
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                @endif
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($pick_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($pick_location_data);
                            @endphp
                            @foreach($pick_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($pick_putaway_location_data) || !empty($aerosol_cage_location_data) || !empty($dropshipping_location_data) || !empty($dispatch_location_data) || !empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp
                                @if(empty($pick_default_selected))
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td class="highlighted-text">{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                    @php
                                    $pick_default_selected=1;
                                    @endphp
                                @else
                                    <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                        <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                        <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                        <td></td>
                                        <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                    </tr>
                                @endif
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($pick_putaway_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($pick_putaway_location_data);
                            @endphp
                            @foreach($pick_putaway_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($aerosol_cage_location_data) || !empty($dropshipping_location_data) || !empty($dispatch_location_data) || !empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($aerosol_cage_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($aerosol_cage_location_data);
                            @endphp
                            @foreach($aerosol_cage_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($dropshipping_location_data) || !empty($dispatch_location_data) || !empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($dropshipping_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($dropshipping_location_data);
                            @endphp
                            @foreach($dropshipping_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($dispatch_location_data) || !empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($dispatch_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($dispatch_location_data);
                            @endphp
                            @foreach($dispatch_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($quarantine_location_data) || !empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($quarantine_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($quarantine_location_data);
                            @endphp
                            @foreach($quarantine_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($hold_location_data) || !empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($hold_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($hold_location_data);
                            @endphp
                            @foreach($hold_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($return_supplier_location_data) || !empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($return_supplier_location_data))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($return_supplier_location_data);
                            @endphp
                            @foreach($return_supplier_location_data as $newr)
                                @php                                
                                if($counter==($total_val-1) && (!empty($other_location)))
                                {
                                    $checked=1;
                                }
                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif

                        @if(!empty($other_location))
                            @php
                            $counter=0;
                            $total_val=0;
                            $checked='';
                            $total_val=count($other_location);
                            @endphp
                            @foreach($other_location as $newr)
                                @php                                
                                if($counter==($total_val-1))
                                {
                                    $checked=0; //never use border
                                }

                                @endphp                            
                                <tr class="{{ !empty($checked)?'tr-border-bottom-2':''}}">
                                    <td>{{ isset($newr['location'])?$newr['location']:'' }} - {{ isset($newr['type_of_location'])?LocationType($newr['type_of_location']):'' }}</td>
                                    <td>{{ isset($newr['qty'])?$newr['qty']:'' }}</span></td>
                                    <td></td>
                                    <td>{{ isset($newr['best_before_date'])?system_date($newr['best_before_date']):'' }}</td>
                                </tr>
                                @php
                                $counter++;
                                @endphp
                            @endforeach
                        @endif                          
                        
                    </tbody>
                </table>            
            </div>
            @php
            $selected_default_location='';
            $selected_default_location_type='';
            if(!empty($replen_data[0]->selected_bulk_location))
            {
                $selected_default_location=isset($replen_data[0]->selected_bulk_location)?$replen_data[0]->selected_bulk_location:'';
                $selected_default_location_type='';
            }
            else
            {
                $selected_default_location=isset($replen_data[0]->location)?$replen_data[0]->location:'';
                $selected_default_location_type=isset($replen_data[0]->type_of_location)?$replen_data[0]->type_of_location:'';
            }

            $scan_pro_barcode='';
            if(!empty($replen_data[0]->selected_bulk_location))
            {
                $scan_pro_barcode=isset($replen_data[0]->selected_pro_barcode)?$replen_data[0]->selected_pro_barcode:'';
            }
            @endphp
            <input type="hidden" id="warehouse_id" value="{{ isset($replen_data[0]->warehouse_id)?$replen_data[0]->warehouse_id:'' }}">
            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">                    
                        <label class="font-12-dark mb-2 bold">@lang('messages.replen.scan_bulk_location')</label>
                        <input type="text" class="form-control" name="scan_bulk_location" id="scan_bulk_location" value="{{ $selected_default_location }}">
                        <span class="location_type font-10-dark bold d-block mt-1">{{ !empty($selected_default_location_type)?LocationType($selected_default_location_type):''}}</span>
                    </div>                            
                </div>
                <div class="col-lg-2 pl-0">
                     <div class="form-group">                    
                        <label class="font-12-dark mb-2 bold">@lang('messages.replen.scan_pro_case_barcode')</label>
                        <input type="text" class="form-control" id="scan_pro_barcode" name="scan_pro_barcode" value="{{$scan_pro_barcode}}">
                    </div>    
                 </div>
            </div>
            <div class="product_list_div" id="product_list_div">        
                
            </div>
            <div class="row mt-5">
                <div class="col-lg-1 pr-0">
                    <div class="form-group">                    
                        <label class="font-12-dark bold mb-2">@lang('messages.replen.box_pick')</label>
                        <input type="text" class="form-control" name="box_picked" id="box_picked" maxlength="9">
                    </div>                            
                </div>
                <div class="col-lg-2">
                     <div class="form-group">                    
                        <label class="font-12-dark bold mb-2">@lang('messages.replen.total_qty')</label>
                        <p class="font-14-dark bold mt-3 total_qty_sel"></p>
                    </div>    
                 </div>
                 <div class="col-lg-12">
                     <button class="btn btn-blue mr-2 px-4 moved_qty">@lang('messages.replen.moved')</button>
                     <button class="btn btn-green" onclick="reportstockModal({{ isset($replen_data[0]->product_id)?$replen_data[0]->product_id:'' }});">@lang('messages.replen.stock_chc_req')</button>
                 </div>
            </div>
        </div>
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="http://topsdemo.co.in/test_m/barcode_scanner.js"></script>
<script src="{{ asset('js/bootstrap-typeahead.js') }}"></script>
<script type="text/javascript" src="{{asset('js/replen/replen_detail.js?v='.CSS_JS_VERSION)}}"></script>
@endsection