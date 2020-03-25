<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\PurchaseOrder;
use App\BookingPO;
use App\BookingPOProducts;
use App\BookingPODiscrepancy;
use App\BookingPOProductCaseDetails;
use App\BookingPOProductLocation;
use App\BookingQcChecklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Imagine;
use Intervention\Image\ImageManagerStatic as Image;
use App\Events\SendMail;
use Gumlet\ImageResize;

class BookingsController extends Controller {

    public
            function index(Request $request) {
        try {
            $columns          = [
                0  => 'book_date',
                1  => 'total_bookings',
                2  => 'total_pallets',
                3  => 'total_dropshipping',
                4  => 'total_skus',
                5  => 'total_variants_val',
                6  => 'total_new_products',
                7  => 'total_essential',
                8  => 'total_seasonal',
                9  => 'total_promotion',
                10 => 'short_dated',
                11 => 'total_product_qty',
                12 => 'total_po_value',
                13 => 'total_short_qty',
                14 => 'total_over_qty',
                15 => 'total_value_received',
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }
            $logic_applied         = 0;
            $adv_search_array_data = array();
            if (!empty($adv_search_array['booking_status'])) {
//for completed only
                if (in_array('1', $adv_search_array['booking_status'])) {
                    $adv_search_array_data['booking_status'] = array('6');
                    $logic_applied++;
                }

//for not completed only
                if (in_array('2', $adv_search_array['booking_status'])) {
                    $adv_search_array_data['booking_status'] = array('1', '2', '3', '4', '5');
                    $logic_applied++;
                }

//if both applied then show all
                if ($logic_applied == 2) {
                    $adv_search_array_data['booking_status'] = array();
                }
            }
            $params                                = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                'search'         => $request->search['value'],
                'book_date'      => $request->book_date_ur,
                'advance_search' => $adv_search_array_data
            );
            $totalColArr                           = array();
            $totalColArr['total_col_booking']      = 0;
            $totalColArr['total_col_pallets']      = 0;
            $totalColArr['total_col_dropship']     = 0;
            $totalColArr['total_col_skus']         = 0;
            $totalColArr['total_col_varients']     = 0;
            $totalColArr['total_col_new_products'] = 0;
            $totalColArr['total_col_essential']    = 0;
            $totalColArr['total_col_seasonal']     = 0;
            $totalColArr['total_col_promotion']    = 0;
            $totalColArr['total_col_short_dated']  = 0;
            $totalColArr['total_col_qty']          = 0;
            $totalColArr['total_col_po_val']       = 0;
            $totalColArr['total_col_qty_shortage'] = 0;
            $totalColArr['total_col_qty_overage']  = 0;
            $totalColArr['total_col_received_val'] = 0;
            $week_no                               = 0;
//dd($totalArr);
            $request->length                       = 1000; //as of now static to avoid pagination
            $booking                               = Booking::getBookings($request->length, $params);
            $data                                  = [];
// dd($booking);
            if (!empty($booking)) {

                $data = $booking->transform(function ($result) use ($data) {
                    $tempArray = array();

                    $tempArray[]  = View::make('components.list-title', ['title' => "<b class='day-date'>" . booking_date_time($result->book_date) . '</b>', 'edit_url' => route('booking-in.bookingDayList', date('Y-m-d', strtotime($result->book_date))), 'btn_title' => trans('messages.bookings.booking_table.bookin_day_list')])->render();
                    $tempArray[]  = !empty($result->total_bookings) ? $result->total_bookings : 0;
                    $totalPallets = Booking::where('book_date', date('Y-m-d', strtotime($result->book_date)))->sum('num_of_pallets');
                    $tempArray[]  = !empty($totalPallets) ? $totalPallets : 0;
                    $tempArray[]  = !empty($result->total_dropshipping) ? $result->total_dropshipping : 0;
                    $tempArray[]  = !empty($result->total_skus) ? $result->total_skus : 0;
                    $tempArray[]  = !empty($result->total_variants_val) ? $result->total_variants_val : 0;
                    $tempArray[]  = !empty($result->total_new_products) ? $result->total_new_products : 0;
                    $tempArray[]  = !empty($result->total_essential) ? $result->total_essential : 0;
                    $tempArray[]  = !empty($result->total_seasonal) ? $result->total_seasonal : 0;

                    $tempArray[] = !empty($result->total_promotion) ? $result->total_promotion : 0;
                    $tempArray[] = !empty($result->short_dated) ? $result->short_dated : 0;
                    $tempArray[] = !empty($result->total_product_qty) ? $result->total_product_qty : 0;
                    $tempArray[] = trans('messages.common.pound_sign') . priceFormate($result->total_po_value);
                    $tempArray[] = !empty($result->total_short_qty) ? $result->total_short_qty : 0;
                    $tempArray[] = !empty($result->total_over_qty) ? $result->total_over_qty : 0;
                    $receivedVal=!empty($result->total_value_received) ? $result->total_value_received : 0;
                    $tempArray[] = trans('messages.common.pound_sign') . priceFormate($receivedVal);

                    return $tempArray;
                });
            }


            if ($booking->total() != 0) {
                $dataBook = $booking->toArray();
                foreach ($dataBook['data'] as $key => $value) {
                    $totalColArr['total_col_booking']      += $value[1];
                    $totalColArr['total_col_pallets']      += $value[2];
                    $totalColArr['total_col_dropship']     += $value[3];
                    $totalColArr['total_col_skus']         += $value[4];
                    $totalColArr['total_col_varients']     += $value[5];
                    $totalColArr['total_col_new_products'] += $value[6];
                    $totalColArr['total_col_essential']    += $value[7];
                    $totalColArr['total_col_seasonal']     += $value[8];
                    $totalColArr['total_col_promotion']    += $value[9];
                    $totalColArr['total_col_short_dated']  += $value[10];
                    $totalColArr['total_col_qty']          += $value[11];
                    $po                                    = str_replace(trans('messages.common.pound_sign'), "", $value[12]);
                    $totalColArr['total_col_po_val']       += priceFormate($po);
                    $totalColArr['total_col_qty_shortage'] += $value[13];
                    $totalColArr['total_col_qty_overage']  += $value[14];
                    $recVal=str_replace(trans('messages.common.pound_sign'), "", $value[15]);
                    $totalColArr['total_col_received_val'] += priceFormate($recVal);
                }
                $totalArr   = array();
                $totalArr[] = "<span class='bold'>Week No.: " . get_week_num($request->book_date_ur) . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_booking'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_pallets'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_dropship'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_skus'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_varients'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_new_products'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_essential'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_seasonal'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_promotion'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_short_dated'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_qty'] . "</span>";
                $totalArr[] = "<span class='bold'>" . trans('messages.common.pound_sign') . priceFormate($totalColArr['total_col_po_val']) . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_qty_shortage'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_qty_overage'] . "</span>";
                $totalArr[] = "<span class='bold'>" . trans('messages.common.pound_sign') . priceFormate($totalColArr['total_col_received_val']) . "</span>";

                $data->add($totalArr);
            }
            else {

                $totalArr   = array();
                $totalArr[] = "<span class='bold'>Week No:" . get_week_num($request->book_date_ur) . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_booking'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_pallets'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_dropship'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_skus'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_varients'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_new_products'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_essential'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_seasonal'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_promotion'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_short_dated'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_qty'] . "</span>";
                $totalArr[] = "<span class='bold'>" . trans('messages.common.pound_sign') . priceFormate($totalColArr['total_col_po_val']) . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_qty_shortage'] . "</span>";
                $totalArr[] = "<span class='bold'>" . $totalColArr['total_col_qty_overage'] . "</span>";
                $totalArr[] = "<span class='bold'>" . trans('messages.common.pound_sign') . priceFormate($totalColArr['total_col_received_val']) . "</span>";

                $data->add($totalArr);
            }


            $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => $booking->total(), // Total number of records
                "recordsFiltered" => $booking->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {

        }
    }

    public
            function bookingDayList(Request $request) {
        try {
            $columns = [
                0  => 'id',
                1  => 'book_date',
                2  => 'slot_from',
                3  => 'booking_ref_id',
                4  => 'supplier_name',
                5  => 'po_list',
                6  => 'num_of_pallets',
                7  => 'total_skus',
                8  => 'total_variants_val',
                9  => 'total_essential',
                10 => 'total_seasonal',
                11 => 'total_product_qty',
                12 => 'total_po_value',
                13 => 'completed',
                14 => 'status',
            ];

            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }

            $logic_applied         = 0;
            $adv_search_array_data = array();
            if (isset($adv_search_array['booking_status']) && !empty($adv_search_array['booking_status'])) {
                //for completed only
                if (in_array('1', $adv_search_array['booking_status'])) {
                    $adv_search_array_data['booking_status'] = array('6');
                    $logic_applied++;
                }

                //for not completed only
                if (in_array('2', $adv_search_array['booking_status'])) {
                    $adv_search_array_data['booking_status'] = array('1', '2', '3', '4', '5');
                    $logic_applied++;
                }

                //if both applied then show all
                if ($logic_applied == 2) {
                    $adv_search_array_data['booking_status'] = array();
                }
            }

            $params = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : 'slot_from',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : 'ASC',
                'search'         => $request->search['value'],
                'book_date'      => $request->book_date_ur,
                'advance_search' => $adv_search_array_data
            );

            $booking_day = date('l', strtotime($request->book_date_ur));

            $listing_type = 1;
            if ($params['order_column'] != 'slot_from') {
                $listing_type = 2;
            }

            if (isset($request->is_api_call) && !empty($request->is_api_call)) {
                $listing_type = 2;
            }


            $request->length = 1000; //as of now static to avoid pagination
            $booking         = '';
            $bookingam       = '';
            $bookingpm       = '';
            if ($listing_type == 2) {
                $booking = Booking::getBookingsDaywise($request->length, $params);
                if (isset($request->is_api_call) && !empty($request->is_api_call) && !empty($booking) && !empty($booking->toArray())) {
                    $result       = $booking->toArray();
                    $recordsTotal = $booking->total();
                    $data         = compact(
                            'result', 'recordsTotal'
                    );
                    return $this->sendResponse('Booking list', 200, $data);
                }
                else if (isset($request->is_api_call) && !empty($request->is_api_call)) {
                    return $this->sendValidation(array('No Booking found.'), 422);
                }
            }
            else {
                $bookingam = Booking::getBookingsDaywise($request->length, $params, 1);
                $bookingpm = Booking::getBookingsDaywise($request->length, $params, 2);
            }

            $data         = [];
            $data1        = [];
            $data2        = [];
            $am_pallet    = 0;
            $am_sku       = 0;
            $am_vari      = 0;
            $am_ess_pro   = 0;
            $am_sea_pro   = 0;
            $am_total_qty = 0;
            $am_po_val    = 0;

            $pm_pallet    = 0;
            $pm_sku       = 0;
            $pm_vari      = 0;
            $pm_ess_pro   = 0;
            $pm_sea_pro   = 0;
            $pm_total_qty = 0;
            $pm_po_val    = 0;

            $total_pallet    = 0;
            $total_sku       = 0;
            $total_vari      = 0;
            $total_ess_pro   = 0;
            $total_sea_pro   = 0;
            $total_total_qty = 0;
            $total_po_val    = 0;
            $day_tab         = '';
            if ($listing_type == 2) {
                if (!empty($booking) && !empty($booking->toArray())) {
                    $data = $booking->transform(function ($result) use ($data) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox', ['object' => $result])->render();
                        $tempArray[] = "<b class='day-date live_entry'>" . booking_date_time($result->book_date) . '</b>';
                        $tempArray[] = date("g:iA", strtotime($result->slot_from)) . ' <br/>' . date("g:iA", strtotime($result->slot_to));
                        if(!empty($result->status) && $result->status!='2' && !empty($result->po_list) && !empty($result->warehouse_id))
                        {
                            $tempArray[] = "<a href='".route('material_receipt.index',$result->id)."'>".$result->booking_ref_id."</a>";
                        }
                        else {
                            $tempArray[] = $result->booking_ref_id;
                        }

                        $supplierName = '<a href="' . url('supplier/form' . $result->supplier_id) . '#general">' . ucwords($result->supplier_name) . '</a>';
                        $tempArray[]  = $supplierName;

                        //po list new logic
                        if (!empty($result->po_list)) {
                            $po_list_array = explode('<br/>', $result->po_list);
                            $counter       = count($po_list_array);
                            if ($counter > 2) {
                                $sliced_array    = array_slice($po_list_array, 0, 2);
                                $new_po_list     = implode('<br/>', $sliced_array);
                                $new_counter     = $counter - 2;
                                $complete_string = $new_po_list . '<br/>';
                                $complete_string .= '<a tabindex="0" data-html="true"  data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="Po list" data-content="' . $result->po_list . '">(+' . $new_counter . ' more)</a>';
                                $tempArray[]     = $complete_string;
                            }
                            else {
                                $tempArray[] = $result->po_list;
                            }
                        }
                        else {
                            $tempArray[] = $result->po_list;
                        }

                        $tempArray[]      = !empty($result->num_of_pallets) ? $result->num_of_pallets : 0;
                        $tempArray[]      = !empty($result->total_skus) ? $result->total_skus : 0;
                        $tempArray[]      = !empty($result->total_variants_val) ? $result->total_variants_val : 0;
                        $tempArray[]      = !empty($result->total_essential) ? $result->total_essential : 0;
                        $tempArray[]      = !empty($result->total_seasonal) ? $result->total_seasonal : 0;
                        $tempArray[]      = !empty($result->total_product_qty) ? $result->total_product_qty : 0;
                        $tempArray[]      = !empty($result->total_po_value) ? sprintf('%0.2f', $result->total_po_value) : 0.00;
                        $tempArray[]      = $result->completed.'%';
                        $tempArray[]      = config('params.booking_status.' . $result->status);
                        $viewActionButton = View::make('bookings.day-list-action-buttons', ['object' => $result]);
                        $tempArray[]      = $viewActionButton->render();
                        return $tempArray;
                    });

                    //final total
                    $booking_array_data = $booking->toArray();
                    if (!empty($booking_array_data['data'])) {
                        foreach ($booking_array_data['data'] as $row) {
                            $total_pallet    = $total_pallet + $row[6];
                            $total_sku       = $total_sku + $row[7];
                            $total_vari      = $total_vari + $row[8];
                            $total_ess_pro   = $total_ess_pro + $row[9];
                            $total_sea_pro   = $total_sea_pro + $row[10];
                            $total_total_qty = $total_total_qty + $row[11];
                            $total_po_val    = $total_po_val + $row[12];
                        }
                    }

                    $item[0]  = NULL;
                    $item[1]  = NULL;
                    $item[2]  = NULL;
                    $item[3]  = NULL;
                    $item[4]  = NULL;
                    $item[5]  = '<span class="bold default_entry">' . $booking_day . ' Total </span>';
                    $item[6]  = '<span class="bold">' . $total_pallet . '</span>';
                    $item[7]  = '<span class="bold">' . $total_sku . '</span>';
                    $item[8]  = '<span class="bold">' . $total_vari . '</span>';
                    $item[9]  = '<span class="bold">' . $total_ess_pro . '</span>';
                    $item[10] = '<span class="bold">' . $total_sea_pro . '</span>';
                    $item[11] = '<span class="bold">' . $total_total_qty . '</span>';
                    $item[12] = '<span class="bold">' . sprintf('%0.2f', $total_po_val) . '</span>';
                    $item[13] = NULL;
                    $item[14] = NULL;
                    $item[15] = NULL;
                    $data->add($item);
                }

                $jsonData = [
                    "draw"            => intval($request->draw),
                    "recordsTotal"    => $booking->total(), // Total number of records
                    "recordsFiltered" => $booking->total(),
                    "data"            => $data // Total data array
                ];
                return response()->json($jsonData);
            }
            else {
                if (!empty($bookingam) && !empty($bookingam->toArray())) {
                    $data1 = $bookingam->transform(function ($result) use ($data1) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox', ['object' => $result])->render();
                        $tempArray[] = "<b class='day-date live_entry'>" . booking_date_time($result->book_date) . '</b>';
                        $tempArray[] = date("g:iA", strtotime($result->slot_from)) . ' <br/>' . date("g:iA", strtotime($result->slot_to));
                        if(!empty($result->status) && $result->status!='2' && !empty($result->po_list) && !empty($result->warehouse_id))
                        {
                            $tempArray[] = "<a href='".route('material_receipt.index',$result->id)."'>".$result->booking_ref_id."</a>";

                        }
                        else {
                            $tempArray[] = $result->booking_ref_id;
                        }

                        $supplierName = '<a href="' . url('supplier/form' . $result->supplier_id) . '#general">' . ucwords($result->supplier_name) . '</a>';
                        $tempArray[]  = $supplierName;
                        //po list new logic
                        if (!empty($result->po_list)) {
                            $po_list_array = explode('<br/>', $result->po_list);
                            $counter       = count($po_list_array);
                            if ($counter > 2) {
                                $sliced_array    = array_slice($po_list_array, 0, 2);
                                $new_po_list     = implode('<br/>', $sliced_array);
                                $new_counter     = $counter - 2;
                                $complete_string = $new_po_list . '<br/>';
                                $complete_string .= '<a tabindex="0" data-html="true"  data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="Po list" data-content="' . $result->po_list . '">(+' . $new_counter . ' more)</a>';
                                $tempArray[]     = $complete_string;
                            }
                            else {
                                $tempArray[] = $result->po_list;
                            }
                        }
                        else {
                            $tempArray[] = $result->po_list;
                        }

                        $tempArray[] = !empty($result->num_of_pallets) ? $result->num_of_pallets : 0;
                        $tempArray[] = !empty($result->total_skus) ? $result->total_skus : 0;
                        $tempArray[] = !empty($result->total_variants_val) ? $result->total_variants_val : 0;
                        $tempArray[] = !empty($result->total_essential) ? $result->total_essential : 0;
                        $tempArray[] = !empty($result->total_seasonal) ? $result->total_seasonal : 0;
                        $tempArray[] = !empty($result->total_product_qty) ? $result->total_product_qty : 0;
                        //$tempArray[]      = !empty($result->total_po_value) ? sprintf('%0.2f', $result->total_po_value) : 0.00;
                        if (!empty($result->total_po_value)) {
                            $tempArray[] = trans('messages.common.pound_sign') . priceFormate($result->total_po_value);
                        }
                        else {
                            $tempArray[] = trans('messages.common.pound_sign') . '0.00';
                        }

                        $tempArray[]      = $result->completed.'%';
                        $tempArray[]      = config('params.booking_status.' . $result->status);
                        $viewActionButton = View::make('bookings.day-list-action-buttons', ['object' => $result]);
                        $tempArray[]      = $viewActionButton->render();
                        return $tempArray;
                    });

                    $booking_array_data = $bookingam->toArray();

                    if (!empty($booking_array_data['data'])) {
                        foreach ($booking_array_data['data'] as $row) {
                            $am_pallet    = $am_pallet + $row[6];
                            $am_sku       = $am_sku + $row[7];
                            $am_vari      = $am_vari + $row[8];
                            $am_ess_pro   = $am_ess_pro + $row[9];
                            $am_sea_pro   = $am_sea_pro + $row[10];
                            $am_total_qty = $am_total_qty + $row[11];
                            $am_po_val    = $am_po_val + str_replace(trans('messages.common.pound_sign'), '', $row[12]);
                        }

                        $item[0]  = NULL;
                        $item[1]  = NULL;
                        $item[2]  = NULL;
                        $item[3]  = NULL;
                        $item[4]  = NULL;
                        $item[5]  = '<span class="bold default_entry">AM Total</span>';
                        $item[6]  = '<span class="bold">' . (!empty($am_pallet) ? $am_pallet : 0) . '</span>';
                        $item[7]  = '<span class="bold">' . (!empty($am_sku) ? $am_sku : 0) . '</span>';
                        $item[8]  = '<span class="bold">' . (!empty($am_vari) ? $am_vari : 0) . '</span>';
                        $item[9]  = '<span class="bold">' . (!empty($am_ess_pro) ? $am_ess_pro : 0) . '</span>';
                        $item[10] = '<span class="bold">' . (!empty($am_sea_pro) ? $am_sea_pro : 0) . '</span>';
                        $item[11] = '<span class="bold">' . (!empty($am_total_qty) ? $am_total_qty : 0) . '</span>';

                        if (!empty($am_po_val)) {
                            $am_po_val = trans('messages.common.pound_sign') . priceFormate($am_po_val);
                        }
                        else {
                            $am_po_val = trans('messages.common.pound_sign') . '0.00';
                        }

                        $item[12] = '<span class="bold">' . $am_po_val . '</span>';
                        $item[13] = NULL;
                        $item[14] = NULL;
                        $item[15] = NULL;
                        $data1->add($item);
                    }
                }

                if (!empty($bookingpm) && !empty($bookingpm->toArray())) {
                    $data2 = $bookingpm->transform(function ($result) use ($data2) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox', ['object' => $result])->render();
                        $tempArray[] = "<b class='day-date live_entry'>" . booking_date_time($result->book_date) . '</b>';
                        $tempArray[] = date("g:iA", strtotime($result->slot_from)) . ' <br/>' . date("g:iA", strtotime($result->slot_to));
                        if(!empty($result->status) && $result->status!='2' && !empty($result->po_list) && !empty($result->warehouse_id))
                        {
                            $tempArray[] = "<a href='".route('material_receipt.index',$result->id)."'>".$result->booking_ref_id."</a>";
                        }
                        else {
                            $tempArray[] = $result->booking_ref_id;
                        }

                        $supplierName = '<a href="' . url('supplier/form' . $result->supplier_id) . '#general">' . ucwords($result->supplier_name) . '</a>';
                        $tempArray[]  = $supplierName;
                        //po list new logic
                        if (!empty($result->po_list)) {
                            $po_list_array = explode('<br/>', $result->po_list);
                            $counter       = count($po_list_array);
                            if ($counter > 2) {
                                $sliced_array    = array_slice($po_list_array, 0, 2);
                                $new_po_list     = implode('<br/>', $sliced_array);
                                $new_counter     = $counter - 2;
                                $complete_string = $new_po_list . '<br/>';
                                $complete_string .= '<a tabindex="0" data-html="true"  data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="Po list" data-content="' . $result->po_list . '">(+' . $new_counter . ' more)</a>';
                                $tempArray[]     = $complete_string;
                            }
                            else {
                                $tempArray[] = $result->po_list;
                            }
                        }
                        else {
                            $tempArray[] = $result->po_list;
                        }
                        $tempArray[] = !empty($result->num_of_pallets) ? $result->num_of_pallets : 0;
                        $tempArray[] = !empty($result->total_skus) ? $result->total_skus : 0;
                        $tempArray[] = !empty($result->total_variants_val) ? $result->total_variants_val : 0;
                        $tempArray[] = !empty($result->total_essential) ? $result->total_essential : 0;
                        $tempArray[] = !empty($result->total_seasonal) ? $result->total_seasonal : 0;
                        $tempArray[] = !empty($result->total_product_qty) ? $result->total_product_qty : 0;
                        //$tempArray[]      = !empty($result->total_po_value) ? sprintf('%0.2f', $result->total_po_value) : 0.00;
                        if (!empty($result->total_po_value)) {
                            $tempArray[] = trans('messages.common.pound_sign') . priceFormate($result->total_po_value);
                        }
                        else {
                            $tempArray[] = trans('messages.common.pound_sign') . '0.00';
                        }

                        $tempArray[]      = $result->completed.'%';
                        $tempArray[]      = config('params.booking_status.' . $result->status);
                        $viewActionButton = View::make('bookings.day-list-action-buttons', ['object' => $result]);
                        $tempArray[]      = $viewActionButton->render();
                        return $tempArray;
                    });

                    $booking_array_data = $bookingpm->toArray();

                    if (!empty($booking_array_data['data'])) {
                        foreach ($booking_array_data['data'] as $row) {
                            $pm_pallet    = $pm_pallet + $row[6];
                            $pm_sku       = $pm_sku + $row[7];
                            $pm_vari      = $pm_vari + $row[8];
                            $pm_ess_pro   = $pm_ess_pro + $row[9];
                            $pm_sea_pro   = $pm_sea_pro + $row[10];
                            $pm_total_qty = $pm_total_qty + $row[11];
                            //$pm_po_val    = $pm_po_val + $row[12];
                            $pm_po_val    = $pm_po_val + str_replace(trans('messages.common.pound_sign'), '', $row[12]);
                        }

                        $item[0]  = NULL;
                        $item[1]  = NULL;
                        $item[2]  = NULL;
                        $item[3]  = NULL;
                        $item[4]  = NULL;
                        $item[5]  = '<span class="bold default_entry">PM Total</span>';
                        $item[6]  = '<span class="bold">' . (!empty($pm_pallet) ? $pm_pallet : 0) . '</span>';
                        $item[7]  = '<span class="bold">' . (!empty($pm_sku) ? $pm_sku : 0) . '</span>';
                        $item[8]  = '<span class="bold">' . (!empty($pm_vari) ? $pm_vari : 0) . '</span>';
                        $item[9]  = '<span class="bold">' . (!empty($pm_ess_pro) ? $pm_ess_pro : 0) . '</span>';
                        $item[10] = '<span class="bold">' . (!empty($pm_sea_pro) ? $pm_sea_pro : 0) . '</span>';
                        $item[11] = '<span class="bold">' . (!empty($pm_total_qty) ? $pm_total_qty : 0) . '</span>';
                        //$item[12] = '<span class="bold">'.(!empty($pm_po_val) ? sprintf('%0.2f', $pm_po_val) : 0.00).'</span>';
                        if (!empty($pm_po_val)) {
                            $pm_po_val = trans('messages.common.pound_sign') . priceFormate($pm_po_val);
                        }
                        else {
                            $pm_po_val = trans('messages.common.pound_sign') . '0.00';
                        }

                        $item[12] = '<span class="bold">' . $pm_po_val . '</span>';
                        $item[13] = NULL;
                        $item[14] = NULL;
                        $item[15] = NULL;
                        $data2->add($item);
                    }
                }

                if ($request->order[0]['dir'] == 'desc') {
                    if (!empty($data1)) {
                        foreach ($data1 as $row) {
                            $data2->push($row);
                        }
                    }

                    $data = $data2;
                }
                else {
                    if (!empty($data2)) {
                        foreach ($data2 as $row) {
                            $data1->push($row);
                        }
                    }
                    $data = $data1;
                }

                if (!empty($bookingam->total()) || !empty($bookingpm->total())) {
                    //final total
                    $total_pallet    = $am_pallet + $pm_pallet;
                    $total_sku       = $am_sku + $pm_sku;
                    $total_vari      = $am_vari + $pm_vari;
                    $total_ess_pro   = $am_ess_pro + $pm_ess_pro;
                    $total_sea_pro   = $am_sea_pro + $pm_sea_pro;
                    $total_total_qty = $am_total_qty + $pm_total_qty;
                    $am_po_val       = str_replace(trans('messages.common.pound_sign'), '', $am_po_val);
                    $pm_po_val       = str_replace(trans('messages.common.pound_sign'), '', $pm_po_val);
                    $total_po_val    = $am_po_val + $pm_po_val;

                    $item[0]  = NULL;
                    $item[1]  = NULL;
                    $item[2]  = NULL;
                    $item[3]  = NULL;
                    $item[4]  = NULL;
                    $item[5]  = '<span class="bold default_entry">' . $booking_day . ' Total </span>';
                    $item[6]  = '<span class="bold">' . $total_pallet . '</span>';
                    $item[7]  = '<span class="bold">' . $total_sku . '</span>';
                    $item[8]  = '<span class="bold">' . $total_vari . '</span>';
                    $item[9]  = '<span class="bold">' . $total_ess_pro . '</span>';
                    $item[10] = '<span class="bold">' . $total_sea_pro . '</span>';
                    $item[11] = '<span class="bold">' . $total_total_qty . '</span>';
                    //$item[12] = '<span class="bold">' . $total_po_val . '</span>';
                    if (!empty($total_po_val)) {
                        $total_po_val = trans('messages.common.pound_sign') . priceFormate($total_po_val);
                    }
                    else {
                        $total_po_val = trans('messages.common.pound_sign') . '0.00';
                    }

                    $item[12] = '<span class="bold">' . $total_po_val . '</span>';
                    $item[13] = NULL;
                    $item[14] = NULL;
                    $item[15] = NULL;
                    $data->add($item);
                }

                $jsonData = [
                    "draw"            => intval($request->draw),
                    "recordsTotal"    => $bookingam->total() + $bookingpm->total(), // Total number of records
                    "recordsFiltered" => $bookingam->total() + $bookingpm->total(),
                    "data"            => $data // Total data array
                ];
                return response()->json($jsonData);
            }
        }
        catch (Exception $ex) {

        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\Bookings\POListingRequest $request
     * @return type
     */
    public
            function getPurchaseOrders(\App\Http\Requests\Api\Bookings\POListingRequest $request) {
        try {
            $columns        = [
                0 => 'id',
                1 => 'po_number',
                2 => 'supplier_order_number',
            ];
            $params         = array(
                'order_column' => $columns[$request->order[0]['column']],
                'order_dir'    => $request->order[0]['dir'],
                'supplier_id'  => $request->supplier_id,
                'search'       => $request->search,
                'cancelled_po' => $request->cancelled_po,
            );
            $purchaseOrders = PurchaseOrder::getPOs($request->length, $params);
            $data           = [];

            if (!empty($purchaseOrders)) {
                $purchaseOrderIds = [];
                if (isset($request->booking_id)) {
                    $purchaseOrderIds = Booking::find($request->booking_id)->bookingPOs->pluck('po_id', 'id');
                    $purchaseOrderIds = $purchaseOrderIds->toArray();
                }
                foreach ($purchaseOrders as $result) {
                    $tempArray = [];
                    $isVisible = false;
                    if (in_array($result->id, $purchaseOrderIds)) {
                        $isVisible = true;
                    }
                    else {
                        if (in_array($result->po_status, [1, 2, 3, 4, 5, 6])) {
                            $isVisible = true;
                        }
                        else {
                            $isVisible = false;
                        }
                    }
                    if ($isVisible === true) {
                        $tempArray[] = View::make('bookings._po-listing-checkbox', ['object' => $result, 'poIds' => $purchaseOrderIds])->render();
                        $tempArray[] = View::make('bookings._color-code-listing', ['object' => $result, 'column' => 'title'])->render();
                        $tempArray[] = !empty($result->supplier_order_number) ? $result->supplier_order_number : '--';
                        $tempArray[] = !empty($result->exp_deli_date) ? $result->exp_deli_date : '--';
                        $tempArray[] = $result->total_skus;
                        $tempArray[] = $result->total_variant;
                        $tempArray[] = $result->essential_product;
                        $tempArray[] = $result->seasonal_product;
                        $tempArray[] = $result->short_dated;
                        $tempArray[] = isset($result->total_quantity) ? $result->total_quantity : 0;
                        $tempArray[] = '<span style="float:right;">' . trans('messages.common.pound_sign') . priceFormate($result->sub_total) . '</span>';
                        $tempArray[] = View::make('purchase-orders._color-code-listing', ['object' => $result, 'column' => ''])->render();
                        $data[]      = $tempArray;
                    }
                }
            }

            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => @count($purchaseOrders), // Total number of records
                "recordsFiltered" => @count($purchaseOrders),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @author  Hitesh Tank
     * @param \App\Http\Requests\Api\Bookings\CreateBookingRequest $request
     * @return type
     */
    public
            function store(\App\Http\Requests\Api\Bookings\CreateBookingRequest $request) {

        DB::beginTransaction();
        try {
            if (isset($request->booking_id)) {
                $bookingObj = Booking::find($request->booking_id);
            }
            else {
                $bookingObj = new Booking();
            }
            if (empty($request->booking_id))
                $bookingObj->supplier_id = $request->supplier;

            if (empty($request->booking_id))
                $bookingObj->booking_ref_id = Booking::getAutoGenerateBookingRefID();

            $bookingObj->status = $request->status;
            if ((isset($request->edit_booking) && $request->edit_booking == true) || ($request->status == array_search('Reserve Slot With PO', config("params.booking_status")) ||
                    $request->status == array_search('Reserve Slot Without PO', config("params.booking_status")))) { // without po or with slot
                $bookingObj->warehouse_id    = $request->warehouse;
                $bookingObj->num_of_pallets  = $request->pallet;
                $bookingObj->estimated_value = !empty($request->estimated_value) ? $request->estimated_value : 0;
                $bookingObj->comment         = $request->comment;
                $bookingObj->slot_id         = $request->slot;
                $bookingObj->book_date       = $request->book_date;
            }
            else {

                if (empty($request->booking_id)) {
                    $slotID = \App\Slot::whereNull('deleted_at')->first();
                    if ($request->status == array_search('Confirm', config("params.booking_status"))) {
                        if ($slotID instanceof \App\Slot) {
                            $bookingObj->slot_id   = $slotID->id;
                            $bookingObj->book_date = Carbon::now()->addDay()->format('Y-m-d');
                        }
                        else {
                            DB::rollback();
                            return $this->sendError('Slot not found, please add and try again.', 200);
                        }
                    }
                    else {
                        $bookingObj->slot_id   = $request->slot;
                        $bookingObj->book_date = $request->book_date;
                    }
                }
                else {
                    $bookingObj->warehouse_id    = $request->warehouse;
                    $bookingObj->num_of_pallets  = $request->pallet;
                    $bookingObj->estimated_value = !empty($request->estimated_value) ? $request->estimated_value : 0;
                    $bookingObj->comment         = $request->comment;
                    $bookingObj->slot_id         = $request->slot;
                    $bookingObj->book_date       = $request->book_date;
                }
            }
            if (!isset($request->booking_id) && empty($request->booking_id))
                $bookingObj->created_by = $request->user->id;

            $bookingObj->modified_by = $request->user->id;
            if ($bookingObj->save()) {
                if (isset($request->booking_id) && !empty($request->booking_id)) {
//If booking status has been change to without remove all booking pos
                    if ($request->status == array_search('Reserve Slot Without PO', config("params.booking_status"))) {
                        $bookingObj->bookingPOs()->delete();
                    }
                    else { //remove unselected po from booking
                        if (isset($booking->edit_booking) && $booking->edit_booking == true) {
                            if (isset($request->added_ids) && !empty($request->added_ids)) {
                                $bookingObj->bookingPOs()->whereNotIn('id', $request->added_ids)->delete();
                            }
                            else {
                                $bookingObj->bookingPOs()->delete();
                            }
                        }
                    }
                }

                if ($request->status == array_search('Confirm', config("params.booking_status")) ||
                        $request->status == array_search('Reserve Slot With PO', config("params.booking_status"))) { // confirm or with slot po
                    $bookingPurchaseOrdersObj = [];
                    if (isset($request->ids)) {
                        foreach ($request->ids as $purchaseOrderID) {
                            $bookingPurchaseOrdersObj [] = new \App\BookingPO(['po_id' => $purchaseOrderID, 'created_by' => $request->user->id, 'modified_by' => $request->user->id]); //attach booking to booking po
                        }
                        $bookingObj->bookingPOs()->saveMany($bookingPurchaseOrdersObj);
//update the Po Status to Booking in
                        PurchaseOrder::whereIn('id', $request->ids)->update(['po_status' => config("params.po_status.Book In")]);
                    }
                }

                DB::commit();
                if ($request->status == array_search('Confirm', config("params.booking_status"))) { // confirm
                    return $this->sendResponse(trans('messages.booking_message.confirm_booking'), 200, $bookingObj);
                }
                else if ($request->status == array_search('Reserve Slot With PO', config("params.booking_status"))) { //reserve with po
                    return $this->sendResponse(trans('messages.booking_message.reserve_booking'), 200, $bookingObj);
                }
                else if ($request->status == array_search('Reserve Slot Without PO', config("params.booking_status"))) { //reserve withour po
                    return $this->sendResponse(trans('messages.booking_message.slot_without_po'), 200, $bookingObj);
                }
            }
            else {
                DB::rollback();
                return $this->sendError(trans('messages.booking_message.add_bookig_error'), 200);
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

//    public
//            function store(\App\Http\Requests\Api\Bookings\CreateBookingRequest $request) {
//
//        DB::beginTransaction();
//        try {
//            if (isset($request->booking_id)) {
//                $bookingObj = Booking::find($request->booking_id);
//            }
//            else {
//                $bookingObj = new Booking();
//            }
//            if (empty($request->booking_id))
//                $bookingObj->supplier_id = $request->supplier;
//
//            if (empty($request->booking_id))
//                $bookingObj->booking_ref_id = Booking::getAutoGenerateBookingRefID();
//
//            $bookingObj->status          = $request->status;
//            $bookingObj->warehouse_id    = $request->warehouse;
//            $bookingObj->slot_id         = $request->slot;
//            $bookingObj->num_of_pallets  = $request->pallet;
//            $bookingObj->estimated_value = $request->estimated_value;
//            $bookingObj->comment         = $request->comment;
//            $bookingObj->book_date       = $request->book_date;
//
//            if (!isset($request->booking_id) && empty($request->booking_id))
//                $bookingObj->created_by = $request->user->id;
//
//            $bookingObj->modified_by = $request->user->id;
//            if ($bookingObj->save()) {
//                if (isset($request->booking_id) && !empty($request->booking_id)) {
//                    //If booking status has been change to without remove all booking pos
//                    if ($request->status == array_search('Reserve Slot Without PO', config("params.booking_status"))) {
//                        $bookingObj->bookingPOs()->delete();
//                    }
//                    else { //remove unselected po from booking
//                        if (isset($request->po_add_edit) && $request->po_add_edit == true) {
//                            if (isset($request->added_ids) && !empty($request->added_ids)) {
//                                $bookingObj->bookingPOs()->whereNotIn('id', $request->added_ids)->delete();
//                            }
//                            else {
//                                $bookingObj->bookingPOs()->delete();
//                            }
//                        }
//                    }
//                }
//
//                if ($request->status == array_search('Confirm', config("params.booking_status")) ||
//                        $request->status == array_search('Reserve Slot With PO', config("params.booking_status"))) { // confirm or with slot po
//                    $bookingPurchaseOrdersObj = [];
//                    if (isset($request->ids)) {
//                        foreach ($request->ids as $purchaseOrderID) {
//                            $bookingPurchaseOrdersObj [] = new \App\BookingPO(['po_id' => $purchaseOrderID, 'created_by' => $request->user->id, 'modified_by' => $request->user->id]); //attach booking to booking po
//                        }
//                        $bookingObj->bookingPOs()->saveMany($bookingPurchaseOrdersObj);
//                        //update the Po Status to Booking in
//                        PurchaseOrder::whereIn('id', $request->ids)->update(['po_status' => config("params.po_status.Book In")]);
//                    }
//                }
//
//                DB::commit();
//                if ($request->status == array_search('Confirm', config("params.booking_status"))) { // confirm
//                    return $this->sendResponse(trans('messages.booking_message.confirm_booking'), 200, $bookingObj);
//                }
//                else if ($request->status == array_search('Reserve Slot With PO', config("params.booking_status"))) { //reserve with po
//                    return $this->sendResponse(trans('messages.booking_message.reserve_booking'), 200, $bookingObj);
//                }
//                else if ($request->status == array_search('Reserve Slot Without PO', config("params.booking_status"))) { //reserve withour po
//                    return $this->sendResponse(trans('messages.booking_message.slot_without_po'), 200, $bookingObj);
//                }
//            }
//            else {
//                return $this->sendError(trans('messages.booking_message.add_bookig_error'), 200);
//            }
//        }
//        catch (Exception $ex) {
//            DB::rollback();
//            return $this->sendError(trans('messages.bad_request '), 400);
//        }
//    }

    /**
     * @autho Hitesh Tank
     * @param \App\Http\Requests\Api\Bookings\BookingPORequest $request
     * @return type
     */
    public
            function getBookingPOs(\App\Http\Requests\Api\Bookings\BookingPORequest $request) {
        try {
            $columns = [
                0 => 'po_number',
                1 => 'supplier_order_number',
            ];

            $params         = array(
                'order_column' => $columns[$request->order[0]['column']],
                'order_dir'    => $request->order[0]['dir'],
                'booking_id'   => $request->booking_id,
                'booking_po'   => true
            );
            $purchaseOrders = Booking::getBookingPOs($request->length, $params);
            $data           = [];

            if (!empty($purchaseOrders)) {
                foreach ($purchaseOrders as $result) {
                    $tempArray   = [];
                    $tempArray[] = View::make('bookings._color-code-listing', ['object' => $result, 'column' => 'title'])->render();
                    $tempArray[] = !empty($result->supplier_order_number) ? $result->supplier_order_number : '--';
                    $tempArray[] = !empty($result->exp_deli_date) ? date('d-M-Y', strtotime($result->exp_deli_date)) : '--';
                    $tempArray[] = $result->total_skus;
                    $tempArray[] = $result->total_variant;
                    $tempArray[] = $result->essential_product;
                    $tempArray[] = $result->seasonal_product;
                    $tempArray[] = $result->short_dated;
                    $tempArray[] = isset($result->total_quantity) ? $result->total_quantity : 0;
                    $tempArray[] = trans('messages.common.pound_sign') . priceFormate($result->sub_total);
                    $tempArray[] = View::make('purchase-orders._color-code-listing', ['object' => $result, 'column' => ''])->render();
                    $tempArray[] = View::make('bookings._delete-booking-po-action', ['object' => $result])->render();
                    $data[]      = $tempArray;
                }
            }

            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => @count($purchaseOrders), // Total number of records
                "recordsFiltered" => @count($purchaseOrders),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {

        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\Bookings\PoDeleteRequest $request
     * @return type
     */
    public
            function deletePO(\App\Http\Requests\Api\Bookings\PoDeleteRequest $request) {
        DB::beginTransaction();
        try {
            $po = BookingPO::find($request->id);
            if (isset($po)) {
                $po->purchaseOrder->update(['po_status' => config('params.po_status.Supplier Confirmed')]);
                if ($po->delete()) {
                    DB::commit();
                    return $this->sendResponse(trans('messages.purchase_order_messages.delete'), 200);
                }
                else {
                    return $this->sendError(trans('messages.purchase_order_messages.delete_error'), 422);
                }
            }
            else {
                return $this->sendError(trans('messages.purchase_order_messages.delete_error'), 422);
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\Bookings\WeeklyListRequest $request
     * @return type
     */
    public
            function weekBooking(\App\Http\Requests\Api\Bookings\WeeklyListRequest $request) {
        try {
            $columns  = [
                0 => 'book_date',
                1 => 'noOfBooking',
                2 => 'noOfPallets',
                3 => 'noOfSkus',
                4 => 'noOfVariants',
                5 => 'noOfEssentialProducts',
                6 => 'noOfSeasonalProducts',
                7 => 'noOfShortDatedProducts',
                8 => 'noOfTotalQuantity',
                9 => 'totalPurchaseOrderValue',
            ];
            $params   = array(
                'order_column' => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                'order_dir'    => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                'book_date'    => date('Y-m-d', strtotime($request->book_date_ur)),
            );
            $bookObj  = new Booking;
            $bookings = $bookObj->getWeekDetail($params);
            $data     = [];
            if (isset($bookings) && !empty($bookings) && @count($bookings) > 0) {
                foreach ($bookings as $booking) {
                    $tempArray = array();

                    $tempArray[]  = View::make('components.list-title', ['title' => booking_date_time($booking->book_date), 'edit_url' => route('booking-in.bookingDayList', date('Y-m-d', strtotime($booking->book_date))), 'btn_title' => trans('messages.bookings.booking_table.bookin_day_list')])->render();
                    $tempArray[]  = $booking->noOfBooking;
                    $totalPallets = Booking::where('book_date', date('Y-m-d', strtotime($booking->book_date)))->sum('num_of_pallets');
                    $tempArray[]  = !empty($totalPallets) ? $totalPallets : 0;
                    $tempArray[]  = $booking->noOfSkus;
                    $tempArray[]  = $booking->noOfVariants;
                    $tempArray[]  = $booking->noOfEssentialProducts;
                    $tempArray[]  = $booking->noOfSeasonalProducts;
                    $tempArray[]  = $booking->noOfShortDatedProducts;
                    $tempArray[]  = $booking->noOfTotalQuantity;
                    $tempArray[]  = trans('messages.common.pound_sign') . priceFormate($booking->totalPurchaseOrderValue);
                    $data[]       = $tempArray;
                }
            }
            $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => @count($bookings), // Total number of records
                "recordsFiltered" => @count($bookings),
                "weekNo"          => getWeekNum($params['book_date']),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function destroy($id) {
        $id = $this->delete_applicable_ids($id);
        if (!empty($id)) {
//get po product_list
            $po_product_id_array       = $this->po_product_id_array($id);
            $booking_po_prod_loc_array = '';
            if (!empty($po_product_id_array)) {
                $booking_po_prod_loc_array = $this->booking_po_product_locations($po_product_id_array);
            }

//delete where we get the booking id
            if (!empty($booking_po_prod_loc_array)) {
                BookingPOProductLocation::whereIn('case_detail_id', explode(",", $booking_po_prod_loc_array))->delete();
            }

            if (!empty($po_product_id_array)) {
                BookingPOProductCaseDetails::whereIn('booking_po_product_id', explode(",", $po_product_id_array))->delete();

                BookingPODiscrepancy::whereIn('booking_po_products_id', explode(",", $po_product_id_array))->delete();
            }

            BookingPOProducts::where('booking_id', $id)->delete();

            BookingPO::where('booking_id', $id)->delete();

            BookingQcChecklist::where('booking_id', $id)->delete();

//remove from booking main table
            $booking = Booking::find($id);
            if ($booking->delete()) {
                return $this->sendResponse(trans('messages.api_responses.booking_delete_success'), 200);
            }
            else {
                return $this->sendError(trans('messages.api_responses.booking_delete_error'), 422);
            }
        }
        else {
            return $this->sendError(trans('messages.api_responses.booking_delete_error'), 422);
        }
    }

    public
            function destroyMany(Request $request) {
        $ids = $request->ids;
        $ids = $this->delete_applicable_ids($ids);
        if (!empty($ids)) {
//get po product_list
            $po_product_id_array = $this->po_product_id_array($ids);

            $booking_po_prod_loc_array = '';
            if (!empty($po_product_id_array)) {
                $booking_po_prod_loc_array = $this->booking_po_product_locations($po_product_id_array);
            }

//delete where we get the booking id
            if (!empty($booking_po_prod_loc_array)) {
                BookingPOProductLocation::whereIn('case_detail_id', explode(",", $booking_po_prod_loc_array))->delete();
            }

            if (!empty($po_product_id_array)) {
                BookingPOProductCaseDetails::whereIn('booking_po_product_id', explode(",", $po_product_id_array))->delete();

                BookingPODiscrepancy::whereIn('booking_po_products_id', explode(",", $po_product_id_array))->delete();
            }

            BookingPOProducts::whereIn('booking_id', explode(",", $ids))->delete();

            BookingPO::whereIn('booking_id', explode(",", $ids))->delete();

            BookingQcChecklist::whereIn('booking_id', explode(",", $ids))->delete();

            if (Booking::whereIn('id', explode(",", $ids))->delete()) {
                return $this->sendResponse(trans('messages.api_responses.booking_delete_success'), 200);
            }
            else {
                return $this->sendError(trans('messages.api_responses.booking_delete_error'), 422);
            }
        }
        else {
            return $this->sendError(trans('messages.api_responses.booking_delete_error'), 422);
        }
    }

    public
            function po_product_id_array($booking_id) {
        $booking_po_prod_array = BookingPOProducts::select('id')->whereIn('booking_id', explode(",", $booking_id))->get();
        $booking_po_prod       = '';
        if (!empty($booking_po_prod_array) && !empty($booking_po_prod_array->toArray())) {
            foreach ($booking_po_prod_array->toArray() as $row) {
                if (!empty($booking_po_prod)) {
                    $booking_po_prod = $booking_po_prod . ',' . $row['id'];
                }
                else {
                    $booking_po_prod = $row['id'];
                }
            }
        }
        return $booking_po_prod;
    }

    public
            function booking_po_product_locations($booking_po_prod) {
        $booking_po_prod_loc_array = BookingPOProductCaseDetails::select('id')->whereIn('booking_po_product_id', explode(",", $booking_po_prod))->get();
        $booking_po_prod_loc       = '';
        if (!empty($booking_po_prod_loc_array) && !empty($booking_po_prod_loc_array->toArray())) {
            foreach ($booking_po_prod_loc_array->toArray() as $row) {
                if (!empty($booking_po_prod_loc)) {
                    $booking_po_prod_loc = $booking_po_prod_loc . ',' . $row['id'];
                }
                else {
                    $booking_po_prod_loc = $row['id'];
                }
            }
        }
        return $booking_po_prod_loc;
    }

    public
            function delete_applicable_ids($ids) {
        $delete_allowed_status = array('1', '2', '3');
        $ids_array             = Booking::select('id')->whereIn('status', $delete_allowed_status)->whereIn('id', explode(",", $ids))->get();
        $ids_data              = '';
        if (!empty($ids_array) && !empty($ids_array->toArray())) {
            foreach ($ids_array->toArray() as $row) {
                if (!empty($ids_data)) {
                    $ids_data = $ids_data . ',' . $row['id'];
                }
                else {
                    $ids_data = $row['id'];
                }
            }
        }
        return $ids_data;
    }

    public
            function update(\App\Http\Requests\Api\Bookings\BookingPORequest $request) {
        try {
            if (isset($request->booking_id)) {
                $bookingObj = Booking::find($request->booking_id);
            }

            $bookingObj->delivery_note_number = $request->delivery_note_number;
            if ($request->file('delivery_notes_picture')) {
                $bookingObj->delivery_notes_picture = $this->saveDeliveryPic($request);
            }
//echo $bookingObj->delivery_notes_picture;exit;
            $bookingObj->modified_by = $request->user->id;
            if ($bookingObj->save()) {

                return $this->sendResponse(trans('messages.api_responses.delivery_note_success'), 200, $bookingObj);
            }
            else {
                return $this->sendError(trans('messages.booking_message.add_bookig_error'), 200);
            }
        }
        catch (Exception $ex) {
            DB::rollback();
            return $this->sendError(trans('messages.bad_request '), 400);
        }
    }

    public
            function saveDeliveryPic(\App\Http\Requests\Api\Bookings\BookingPORequest $request) {
        if ($request->file('delivery_notes_picture')) {
            $bookingObj = Booking::find($request->booking_id);
            $folder     = 'bookings';
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0777, true);
            }
            $uploadedFile = $request->file('delivery_notes_picture');
            $extension    = strtolower($uploadedFile->getClientOriginalExtension());
            $name         = time() . '' . $uploadedFile->getClientOriginalName();
            $path         = Storage::putFileAs(('bookings'), $uploadedFile, $name);
            if (!empty($path)) {
                $folder = 'bookings';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }
                $folder = 'bookings/thumbnail/';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }

                $thumbName1   = explode('/', $path);
                $thumbName    = $thumbName1[1];
                $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'bookings/' . $thumbName;

                $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'bookings/thumbnail/' . $thumbName;

                //   echo $thumbPath;exit;
                Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                })->save($thumbPath, 100);

                Storage::delete($bookingObj->delivery_notes_picture);
                if (isset($bookingObj->delivery_notes_picture) && !empty($bookingObj->delivery_notes_picture)) {
                    $thumbName = explode('/', $bookingObj->delivery_notes_picture)[1];
                    Storage::delete('bookings/thumbnail/' . $thumbName);
                }
                return $path;
            }
        }
        else {
            return NULL;
        }
    }

    public
            function getBookingDetails(\App\Http\Requests\Api\Bookings\BookingDetailRequest $request) {
        try {
            if ($booking_details = Booking::find($request->id)) {
                return $this->sendResponse("Booking details found", 200, $booking_details);
            }
            else {
                return $this->sendError(trans('messages.bad_request'), 400);
            }
        }
        catch (Exception $ex) {
            return $this->sendError(trans('messages.bad_request'), 400);
        }
    }

}
