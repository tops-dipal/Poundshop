<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LocationAssign;

class LocationAssignTrans extends Model {

    protected
            $table   = 'location_assign_trans';
    protected
            $guarded = [];
    public
            $timestamps = false;

    public
            function locationAssign() {
        return $this->belongsTo(LocationAssign::class, 'loc_ass_id');
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     */
    public static
            function putAwayProductCombinationExist($params = []) {

        //  $query = self::where('product_id', $params['product_id']);
//                ::join('product_barcodes', function($q) {
//                    $q->on('location_assign_trans.barcode_id', 'product_barcodes.id');
//                });
//        if ($params['putaway_case'] == 3) { //outer case
//            $query->where('product_barcodes.barcode', $params['selected_barcode']);
//            $query->where('location_assign_trans.qty_per_box', $params['outer_qty_per_box']);
//        }
//        else {
//            if ($params['scanned_case_type'] == 3) {
//                if ($params['putaway_case'] == 2) { //inner
//                    $query->where('location_assign_trans.qty_per_box', $params['qty_per_box']);
//                }
//            }
//
//            if ($params['scanned_case_type'] == 2) {
//                if ($params['putaway_case'] == 2) { //inner
//                    $query->where('location_assign_trans.qty_per_box', $params['inner_qty_per_box']);
//                }
//            }
//
//
//            if ($params['scanned_case_type'] == 1) {
//                $query->where('product_barcodes.barcode', $params['selected_barcode']);
//            }
//            else {
//                $query->where('product_barcodes.barcode', $params['put_away_barcode_textbox']);
//            }
//        }
        $query = self::where('location_assign_trans.loc_ass_id', $params['loc_ass_id']);

        if (empty($params['best_before_date']))
            $query->whereNull('location_assign_trans.best_before_date');
        else
            $query->where('location_assign_trans.best_before_date', date('Y-m-d', strtotime($params['best_before_date'])));

        //$query->where('location_assign_trans.case_type', $params['putaway_case']);
        return $query->first();
    }

    /**
     * @author Hitesh Tank
     * @param type $outBox
     */
    public static
            function foldOuterBoxCombination($outBox) {
        $boxArray  = [];
        $qtyPerBox = $outBox->qty_per_box;
        $startQty  = 1;
        for ($key = 0; $key < $outBox->total_boxes; $key++) {
            array_push($boxArray, ['start' => $startQty, 'end' => ($startQty * $qtyPerBox)]);
            $startQty++;
        }

        return $boxArray;
    }

    /**
     * @author Hitesh Tank
     * @param type $outer
     * @param int $inner
     * @return type
     */
    public static
            function foldOuterInnerSingleCombination($outer, $inner) {

        $outerBoxArray = [];
        $innerBoxArray = [];
        $outerPerBox   = $outer->qty_per_box;
        $outerBox      = $outer->total_boxes;
        $caseQuantity  = $inner->case_quantity;
        for ($key = 0; $key < $outerBox; $key++) {
            array_push($outerBoxArray, ['qty_per_box' => $outerPerBox, 'no_of_box' => 1, 'total' => $outerPerBox]);

            $innerTotalBox                                = $outer->qty_per_box / $caseQuantity;
            $outerBoxArray[$key]['inners']['qty_per_box'] = $caseQuantity;
            $outerBoxArray[$key]['inners']['no_of_box']   = $innerTotalBox;
            $startQty                                     = 1;
            for ($inner = 0; $inner < $innerTotalBox; $inner++) {
                $outerBoxArray[$key]['inners']['sets'][] = ['start' => $startQty, 'end' => ($startQty + $caseQuantity) - 1];
                $startQty                                = $startQty + $caseQuantity;
            }
        }
        return $outerBoxArray;
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     */
    public static
            function remainingQuantiesPallet($params) {

        $deductQty = $params['qty'];
        foreach ($params['existingData'] as $outerKey => $outer) {

            $found = false;

            if ((($outerKey + 1) * $outer['qty_per_box'] * $outer['no_of_box']) < $deductQty) {

                $deductQty = $params['qty'] - (($outerKey + 1) * $outer['qty_per_box'] * $outer['no_of_box']);
                unset($params['existingData'][$outerKey]);
                continue;
            }


            foreach ($outer['inners']['sets'] as $key => $sets) {

                if ($sets['end'] >= $deductQty) {

                    if (($sets['end'] - $deductQty) == 0) { //yes then remove all above qty
                        unset($params['existingData'][$outerKey]['inners']['sets'][$key]);
                        $looseQty                                                 = $sets['end'] - $deductQty;
                        $found                                                    = true;
                        $params['existingData'][$outerKey]['inners']['no_of_box'] = $params['existingData'][$outerKey]['inners']['no_of_box'] - ($key + 1);
                        break;
                    }
                    else { // convert inner into loose and then remove above all combination of inner
                        $looseQty                                                 = $sets['end'] - $deductQty;
                        unset($params['existingData'][$outerKey]['inners']['sets'][$key]);
                        $params['existingData'][$outerKey]['inners']['no_of_box'] = $params['existingData'][$outerKey]['inners']['no_of_box'] - ($key + 1);
                        $found                                                    = true;
                        break;
                    }
                }
                else {
                    unset($params['existingData'][$outerKey]['inners']['sets'][$key]);
                }
            }
            if ($found == true) {
                break;
            }
        }

        foreach ($params['existingData'] as $outer => $remainingData) {
            if (empty($params['existingData'][$outerKey]['inners']['sets'])) { //skip sets and remove outer from array
                unset($params['existingData'][$outerKey]);
            }
        }
        $params['existingData'] = array_values($params['existingData']);
        $obj                    = new self;
        return $obj->preparedInsertNewPalletRecords($params['existingData'], $params['innerCaseDetail'], $params['scannedPalletData'], $params['singleBarcode'], $looseQty);
    }

    /**
     * @author Hitesh Tank
     * @param type $datas
     * @param type $innerCase
     * @param type $scannedPalletData
     * @param type $singleBarcode
     * @param type $looseQty
     * @return type
     */
    public
            function preparedInsertNewPalletRecords($datas, $innerCase, $scannedPalletData, $singleBarcode, $looseQty) {
        $inertRecords = [];
        $outerExist   = false;
        $innerExist   = false;
        foreach ($datas as $key => $records) {
            if ($records['total'] != ($records['inners']['qty_per_box'] * $records['inners']['no_of_box'])) { //convert into inner
                if ($key == 0) {
                    array_push($inertRecords, [
                        'qty_per_box'                    => $records['inners']['qty_per_box'],
                        'total_boxes'                    => $records['inners']['no_of_box'],
                        'qty'                            => ($records['inners']['no_of_box'] * $records['inners']['qty_per_box']),
                        'case_type'                      => 2,
                        'barcode_id'                     => $innerCase->id,
                        'loc_ass_id'                     => $scannedPalletData->loc_ass_id,
                        'booking_po_product_id'          => !empty($scannedPalletData->booking_po_product_id) ? $scannedPalletData->booking_po_product_id : NULL,
                        'booking_po_case_detail_id'      => !empty($scannedPalletData->booking_po_case_detail_id) ? $scannedPalletData->booking_po_case_detail_id : NULL,
                        'booking_po_product_location_id' => !empty($scannedPalletData->booking_po_product_location_id) ? $scannedPalletData->booking_po_product_location_id : NULL,
                        'best_before_date'               => !empty($scannedPalletData->best_before_date) ? $scannedPalletData->best_before_date : NULL,
                        'created_at'                     => \Carbon\Carbon::now(),
                        'updated_at'                     => \Carbon\Carbon::now(),
                    ]);
                    $innerExist = true;
                }
                else {
                    $inertRecords[0]['total_boxes'] = $inertRecords[0]['total_boxes'] + 1;
                    $inertRecords[0]['qty']         = $inertRecords[0]['qty'] + ($records['inners']['no_of_box'] * $records['inners']['qty_per_box']);
                }
            }
            else { //make it outer as a seperate
                if ($key == 0 && !isset($inertRecords[0])) {
                    $outerExist = true;
                    array_push($inertRecords, [
                        'qty_per_box'                    => $records['qty_per_box'],
                        'total_boxes'                    => $records['no_of_box'],
                        'qty'                            => $records['total'],
                        'case_type'                      => 3,
                        'barcode_id'                     => $scannedPalletData->barcode_id,
                        'loc_ass_id'                     => $scannedPalletData->loc_ass_id,
                        'booking_po_product_id'          => !empty($scannedPalletData->booking_po_product_id) ? $scannedPalletData->booking_po_product_id : NULL,
                        'booking_po_case_detail_id'      => !empty($scannedPalletData->booking_po_case_detail_id) ? $scannedPalletData->booking_po_case_detail_id : NULL,
                        'booking_po_product_location_id' => !empty($scannedPalletData->booking_po_product_location_id) ? $scannedPalletData->booking_po_product_location_id : NULL,
                        'best_before_date'               => !empty($scannedPalletData->best_before_date) ? $scannedPalletData->best_before_date : NULL,
                        'created_at'                     => \Carbon\Carbon::now(),
                        'updated_at'                     => \Carbon\Carbon::now(),
                    ]);
                }
                else {
                    if ($outerExist == true && $innerExist == false) {
                        $inertRecords[0]['total_boxes'] = $inertRecords[0]['total_boxes'] + 1;
                        $inertRecords[0]['qty']         = $inertRecords[0]['qty'] + $records['total'];
                    }
                    else {
                        if ($innerExist == true && !isset($inertRecords[1])) {
                            array_push($inertRecords, [
                                'qty_per_box'                    => $records['qty_per_box'],
                                'total_boxes'                    => $records['no_of_box'],
                                'qty'                            => $records['total'],
                                'case_type'                      => 3,
                                'barcode_id'                     => $scannedPalletData->barcode_id,
                                'loc_ass_id'                     => $scannedPalletData->loc_ass_id,
                                'booking_po_product_id'          => !empty($scannedPalletData->booking_po_product_id) ? $scannedPalletData->booking_po_product_id : NULL,
                                'booking_po_case_detail_id'      => !empty($scannedPalletData->booking_po_case_detail_id) ? $scannedPalletData->booking_po_case_detail_id : NULL,
                                'booking_po_product_location_id' => !empty($scannedPalletData->booking_po_product_location_id) ? $scannedPalletData->booking_po_product_location_id : NULL,
                                'best_before_date'               => !empty($scannedPalletData->best_before_date) ? $scannedPalletData->best_before_date : NULL,
                                'created_at'                     => \Carbon\Carbon::now(),
                                'updated_at'                     => \Carbon\Carbon::now(),
                            ]);
                        }
                        else {
                            $inertRecords[1]['total_boxes'] = $inertRecords[1]['total_boxes'] + 1;
                            $inertRecords[1]['qty']         = $inertRecords[1]['qty'] + $records['total'];
                        }
                    }
                }
            }
        }

        if ($looseQty != 0) {
            array_push($inertRecords, [
                'qty_per_box'                    => $looseQty,
                'total_boxes'                    => 1,
                'qty'                            => $looseQty,
                'case_type'                      => 1,
                'barcode_id'                     => $singleBarcode,
                'loc_ass_id'                     => $scannedPalletData->loc_ass_id,
                'booking_po_product_id'          => !empty($scannedPalletData->booking_po_product_id) ? $scannedPalletData->booking_po_product_id : NULL,
                'booking_po_case_detail_id'      => !empty($scannedPalletData->booking_po_case_detail_id) ? $scannedPalletData->booking_po_case_detail_id : NULL,
                'booking_po_product_location_id' => !empty($scannedPalletData->booking_po_product_location_id) ? $scannedPalletData->booking_po_product_location_id : NULL,
                'best_before_date'               => !empty($scannedPalletData->best_before_date) ? $scannedPalletData->best_before_date : NULL,
                'created_at'                     => \Carbon\Carbon::now(),
                'updated_at'                     => \Carbon\Carbon::now(),
            ]);
        }

        return $inertRecords;
    }

    /**
     * @author Hitesh Tank
     * @param type $outer
     * @param int $inner
     * @return type
     */
    public static
            function foldOuterBoxInnerCombination($outer, $qtyPerBox) {

        $outerBoxArray = [];
        $innerBoxArray = [];
        $outerPerBox   = $outer->qty_per_box;
        $outerBox      = $outer->total_boxes;
        $caseQuantity  = $qtyPerBox;
        for ($key = 0; $key < $outerBox; $key++) {
            array_push($outerBoxArray, ['qty_per_box' => $outerPerBox, 'no_of_box' => 1, 'total' => $outerPerBox]);

            $innerTotalBox                                = $outer->qty_per_box / $caseQuantity;
            $outerBoxArray[$key]['inners']['qty_per_box'] = $caseQuantity;
            $outerBoxArray[$key]['inners']['no_of_box']   = $innerTotalBox;
            $startQty                                     = 1;
            for ($inner = 0; $inner < $innerTotalBox; $inner++) {
                $outerBoxArray[$key]['inners']['sets'][] = ['start' => $startQty, 'end' => ($startQty + $caseQuantity) - 1];
                $startQty                                = $startQty + $caseQuantity;
            }
        }
        return $outerBoxArray;
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     */
    public static
            function remainingInnerCasePallets($params) {
        $deductQty = (int) $params['qty'];
        foreach ($params['existingData'] as $outerKey => $outer) {
            $found = false;

            if ((($outerKey + 1) * $outer['qty_per_box'] * $outer['no_of_box']) < $params['qty']) {
                $deductQty = $params['qty'] - (($outerKey + 1) * $outer['qty_per_box'] * $outer['no_of_box']);
                unset($params['existingData'][$outerKey]);
                continue;
            }


            foreach ($outer['inners']['sets'] as $key => $sets) {
                if ($sets['end'] >= $deductQty) {
                    if (($sets['end'] - $deductQty) == 0) { //yes then remove all above qty
                        unset($params['existingData'][$outerKey]['inners']['sets'][$key]);
                        $params['existingData'][$outerKey]['inners']['no_of_box'] = $params['existingData'][$outerKey]['inners']['no_of_box'] - ($key + 1);
                        $found                                                    = true;
                        break;
                    }
                    else { // convert inner into loose and then remove above all combination of inner
                        $looseQty                                                 = $sets['end'] - $deductQty;
                        unset($params['existingData'][$outerKey]['inners']['sets'][$key]);
                        $params['existingData'][$outerKey]['inners']['no_of_box'] = $params['existingData'][$outerKey]['inners']['no_of_box'] - ($key + 1);
                        $found                                                    = true;
                        break;
                    }
                }
                else {
                    unset($params['existingData'][$outerKey]['inners']['sets'][$key]);
                }
            }
            if ($found == true) {
                break;
            }
        }

        foreach ($params['existingData'] as $outer => $remainingData) {
            if (empty($params['existingData'][$outerKey]['inners']['sets'])) { //skip sets and remove outer from array
                unset($params['existingData'][$outerKey]);
            }
        }

        $params['existingData'] = array_values($params['existingData']);
        $obj                    = new self;
        return $obj->preparedInsertNewPalletRecords($params['existingData'], $params['innerCaseDetail'], $params['scannedPalletData'], 0, 0);
    }

    /**
     * @author Hitesh Tank
     * @param type $scannedProductTransactionData
     * @param type $params
     * @return type
     */
    public
    static
            function outerBoxDeduction($scannedProductTransactionData, $params) {
        $scannedProductTransactionData->qty         = $scannedProductTransactionData->qty - $params['qty'];
        $scannedProductTransactionData->total_boxes = $scannedProductTransactionData->total_boxes - $params['total_box'];
        $scannedProductTransactionData->save();
        return $scannedProductTransactionData->fresh();
    }

    /**
     * @author Hitesh Tank
     * @param type $outer
     * @param int $inner
     * @return type
     */
    public static
            function foldInnerBoxes($inners, $qtyPerBox) {

        $innerBoxArray = [];
        $totalBoxes    = $inners->total_boxes;
        $caseQuantity  = $qtyPerBox;
        for ($key = 0; $key < $totalBoxes; $key++) {

            array_push($innerBoxArray, ['qty_per_box' => $qtyPerBox, 'no_of_box' => 1, 'total' => ($qtyPerBox * 1)]);
            $startQty = 1;
            for ($inner = 0; $inner < 1; $inner++) {
                $innerBoxArray[$key]['sets'][] = ['start' => $startQty, 'end' => ($startQty + $caseQuantity) - 1];
                $startQty                      = $startQty + $caseQuantity;
            }
        }
        return $innerBoxArray;
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     */
    public static
            function remainingInnersQuantityPallets($params) {
        $deductQty = (int) $params['qty'];
        $looseQty  = 0;
        foreach ($params['existingData'] as $innerKey => $inners) {
            $found = false;

            if ((($innerKey + 1) * $inners['qty_per_box'] * $inners['no_of_box']) < $params['qty']) {
                $deductQty = $params['qty'] - (($innerKey + 1) * $inners['qty_per_box'] * $inners['no_of_box']);
                unset($params['existingData'][$innerKey]);
                continue;
            }


            foreach ($inners['sets'] as $key => $sets) {
                if ($sets['end'] >= $deductQty) {

                    if (($sets['end'] - $deductQty) == 0) { //yes then remove all above qty
                        unset($params['existingData'][$innerKey]['sets'][$key]);
                        $params['existingData'][$innerKey]['no_of_box'] = $params['existingData'][$innerKey]['no_of_box'] - ($key + 1);
                        $found                                          = true;
                        break;
                    }
                    else { // convert inner into loose and then remove above all combination of inner
                        $looseQty = $sets['end'] - $deductQty;
                        unset($params['existingData'][$innerKey]['sets'][$key]);
//                        $params['existingData'][$inners]['no_of_box'] = $params['existingData'][$inners]['no_of_box'] - ($key + 1);
                        $found    = true;
                        break;
                    }
                }
                else {

                    unset($params['existingData'][$inners]['sets'][$key]);
                }
            }
            if ($found == true) {
                break;
            }
        }

        foreach ($params['existingData'] as $inners => $remainingData) {
            if (empty($params['existingData'][$inners]['sets'])) { //skip sets and remove outer from array
                unset($params['existingData'][$inners]);
            }
        }

        $params['existingData'] = array_values($params['existingData']);
        $obj                    = new self;
        return $obj->preparedInnersInsertPalletRecords($params['existingData'], $params['innerCaseDetail'], $params['scannedPalletData'], 0, $looseQty);
    }

    /**
     * @author Hitesh Tank
     * @param type $datas
     * @param type $innerCase
     * @param type $scannedPalletData
     * @param type $singleBarcode
     * @param type $looseQty
     * @return type
     */
    public
            function preparedInnersInsertPalletRecords($datas, $innerCase, $scannedPalletData, $singleBarcode, $looseQty) {
        $inertRecords = [];

        foreach ($datas as $key => $records) {
            if ($key == 0) { //convert into inner
                array_push($inertRecords, [
                    'qty_per_box'                    => $records['qty_per_box'],
                    'total_boxes'                    => $records['no_of_box'],
                    'qty'                            => ($records['no_of_box'] * $records['qty_per_box']),
                    'case_type'                      => 2,
                    'barcode_id'                     => $scannedPalletData->barcode_id,
                    'loc_ass_id'                     => $scannedPalletData->loc_ass_id,
                    'booking_po_product_id'          => !empty($scannedPalletData->booking_po_product_id) ? $scannedPalletData->booking_po_product_id : NULL,
                    'booking_po_case_detail_id'      => !empty($scannedPalletData->booking_po_case_detail_id) ? $scannedPalletData->booking_po_case_detail_id : NULL,
                    'booking_po_product_location_id' => !empty($scannedPalletData->booking_po_product_location_id) ? $scannedPalletData->booking_po_product_location_id : NULL,
                    'best_before_date'               => !empty($scannedPalletData->best_before_date) ? $scannedPalletData->best_before_date : NULL,
                    'created_at'                     => \Carbon\Carbon::now(),
                    'updated_at'                     => \Carbon\Carbon::now(),
                ]);
            }
            else { //make it outer as a seperate
                $inertRecords[0]['total_boxes'] = $inertRecords[0]['total_boxes'] + 1;
                $inertRecords[0]['qty']         = $inertRecords[0]['qty'] + $records['total'];
            }
        }



        if ($looseQty != 0) {
            array_push($inertRecords, [
                'qty_per_box'                    => $looseQty,
                'total_boxes'                    => 1,
                'qty'                            => $looseQty,
                'case_type'                      => 1,
                'barcode_id'                     => $innerCase->id,
                'loc_ass_id'                     => $scannedPalletData->loc_ass_id,
                'booking_po_product_id'          => !empty($scannedPalletData->booking_po_product_id) ? $scannedPalletData->booking_po_product_id : NULL,
                'booking_po_case_detail_id'      => !empty($scannedPalletData->booking_po_case_detail_id) ? $scannedPalletData->booking_po_case_detail_id : NULL,
                'booking_po_product_location_id' => !empty($scannedPalletData->booking_po_product_location_id) ? $scannedPalletData->booking_po_product_location_id : NULL,
                'best_before_date'               => !empty($scannedPalletData->best_before_date) ? $scannedPalletData->best_before_date : NULL,
                'created_at'                     => \Carbon\Carbon::now(),
                'updated_at'                     => \Carbon\Carbon::now(),
            ]);
        }
        return $inertRecords;
    }

    /**
     * @author Hitesh Tank
     * @param type $arrayData
     * @return type
     */
    public static
            function updateExistingPutAwayData($arrayData) {
        $obj = new self;
        if (isset($arrayData['loc_ass_id']) && !empty($arrayData['loc_ass_id'])) {

            if ($obj->updateRecord($arrayData)) {
                unset($arrayData);
            }
        }
        else {
            foreach ($arrayData as $newRemaingData) {
//            $remainingInnerQuantitiesCaseData = [
//                'qty_per_box'                    => $scannedProductTransactionData->qty - $request->qty,
//                'total_boxes'                    => 1,
//                'qty'                            => $scannedProductTransactionData->qty - $request->qty,
//                'case_type'                      => 1,
//                'barcode_id'                     => $newBarcodeObj->id,
//                'loc_ass_id'                     => $scannedProductTransactionData->loc_ass_id,
//                'booking_po_product_id'          => !empty($scannedProductTransactionData->booking_po_product_id) ? $scannedProductTransactionData->booking_po_product_id : NULL,
//                'booking_po_case_detail_id'      => !empty($scannedProductTransactionData->booking_po_case_detail_id) ? $scannedProductTransactionData->booking_po_case_detail_id : NULL,
//                'booking_po_product_location_id' => !empty($scannedProductTransactionData->booking_po_product_location_id) ? $scannedProductTransactionData->booking_po_product_location_id : NULL,
//                'best_before_date'               => !empty($scannedProductTransactionData->best_before_date) ? $scannedProductTransactionData->best_before_date : NULL,
//                'created_at'                     => \Carbon\Carbon::now(),
//                'updated_at'                     => \Carbon\Carbon::now(),
//            ];
//        }
            }
        }
        return $arrayData;
    }

    /**
     * @author Hitesh tank
     * @param type $newRemaingData
     * @return boolean
     */
    public
            function updateRecord($newRemaingData) {

        $query = self::where('loc_ass_id', $newRemaingData['loc_ass_id']);
        $query->where('barcode_id', $newRemaingData['barcode_id']);
        $query->where('case_type', $newRemaingData['case_type']);
        //    $query->where('qty_per_box', $newRemaingData['qty_per_box']);
        if (isset($newRemaingData['booking_po_product_id']) && !empty($newRemaingData['booking_po_product_id']))
            $query->where('booking_po_product_id', $newRemaingData['booking_po_product_id']);

        if (isset($newRemaingData['booking_po_case_detail_id']) && !empty($newRemaingData['booking_po_case_detail_id']))
            $query->where('booking_po_case_detail_id', $newRemaingData['booking_po_case_detail_id']);

        if (isset($newRemaingData['booking_po_product_location_id']) && !empty($newRemaingData['booking_po_product_location_id']))
            $query->where('booking_po_product_location_id', $newRemaingData['booking_po_product_location_id']);

        if (isset($newRemaingData['best_before_date'])) {
            $query->where('best_before_date', date('Y-m-d', strtotime($newRemaingData['best_before_date'])));
        }
        $row = $query->first();
        if (isset($row) && !empty($row)) {
            $row->qty         = $row->qty + $newRemaingData['qty'];
            $row->total_boxes = $row->total_boxes + $newRemaingData['total_boxes'];
            if ($row->save()) {
                return true;
            }
            else {
                return false;
            }
        }
    }

}
