<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBarcode extends Model {

    protected
            $table    = 'product_barcodes';
    protected
            $fillable = [
        'product_id',
        'barcode_type',
        'barcode',
        'case_quantity',
        'parent_id',
        'created_by',
        'modified_by',
    ];
    protected
            $dates    = ['created_at', 'updated_at'];

    /**
     *
     * @param type $barcodes
     * @return boolean
     */
    public
            function uniqueBarCode($barcodes) {
        foreach ($barcodes as $barcode) {
            if ($this->where('barcode', $barcode)->first()) {
                return false;
            }
        }
        return true;
    }

    public
            function addBarcodes($datas) {
        $bulkInsert = [];
        foreach ($datas['barcodes'] as $key => $value) {
            $bulkInsert[] = [
                'product_id'  => $datas['products'][$key],
                'barcode'     => $value,
                'created_by'  => $datas['requestObj']->user->id,
                'modified_by' => $datas['requestObj']->user->id,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ];
        }
        $this::insert($bulkInsert);
    }

    public
            function innerDetails() {
        return $this->hasMany(self::class, 'parent_id')->where('barcode_type', '2');
    }

    public static
            function materialReceiptAddBarcodes($product_id, $barcode_details, $request) {
        if (!empty($product_id) && is_array($barcode_details)) {
            $db_array = array();

            foreach ($barcode_details as $barcode => $details) {
                $barcodes[] = $barcode;

                if ($details['barcode_type'] == '3' && !empty($details['inner_barcode'])) {
                    $barcodes[] = $details['inner_barcode'];
                }
            }

            $product_barcode_exist = self::select('id', 'barcode', 'barcode_type')->where(array('product_id' => $product_id))->whereIn('barcode', $barcodes)->get()->keyBy('barcode')->toArray();


            if (count($product_barcode_exist) == count($barcodes)) {
                return true;
            }

            // $product_barcode_not_unique = self::select('id', 'barcode', 'barcode_type')->where('product_id', '!=', $product_id)->whereIn('barcode', $barcodes)->get()->keyBy('barcode')->toArray();

            foreach ($barcode_details as $barcode => $details) {
                if ($details['barcode_type'] == '3') {
                    if (empty($product_barcode_exist[$barcode])) {
                        $db_outer_array = array(
                            'product_id'    => $product_id,
                            'barcode'       => $barcode,
                            'barcode_type'  => $details['barcode_type'],
                            'case_quantity' => !empty($details['outer_barcode_qty']) ? $details['outer_barcode_qty'] : 1,
                            'created_by'    => $request->user()->id,
                            'created_at'    => date('Y-m-d H:i:s'),
                        );

                        $parent_id = self::create($db_outer_array)->id;
                    }
                    else {
                        $parent_id = $product_barcode_exist[$barcode]['id'];
                    }
                    
                    if (!empty($details['inner_barcode']) && empty($product_barcode_exist[$details['inner_barcode']])
                    ) {
                        $db_array[] = array(
                            'product_id'    => $product_id,
                            'barcode'       => $details['inner_barcode'],
                            'barcode_type'  => 2,
                            'case_quantity' => !empty($details['inner_barcode_qty']) ? $details['inner_barcode_qty'] : 1,
                            'parent_id'     => $parent_id,
                            'created_by'    => $request->user()->id,
                            'created_at'    => date('Y-m-d H:i:s'),
                        );
                    }
                }
                elseif (empty($product_barcode_exist[$barcode])) {
                    $db_array[] = array(
                        'product_id'    => $product_id,
                        'barcode'       => $barcode,
                        'barcode_type'  => $details['barcode_type'],
                        'case_quantity' => 1,
                        'parent_id'     => NULL,
                        'created_by'    => $request->user()->id,
                        'created_at'    => date('Y-m-d H:i:s'),
                    );
                }
            }

            if (!empty($db_array)) {
                self::insert($db_array);

                return true;
            }

            return false;
        }
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     */
    public static
            function checkInnerCaseExist($params) {

        $outCase = self::where('barcode', $params['outer_barcode'])->where('product_id', $params['product_id'])->where('barcode_type', 3)->first();

        if (isset($outCase) && @count($outCase) > 0) {
            $result = self::where('barcode', $params['barcode'])->where('parent_id', $outCase->id)->where('product_id', $params['product_id'])->where('barcode_type', 2)->first();
            if (isset($result) && !empty($result)) {
                if ($result->case_quantity == $params['case_quantity']) {
                    return ['status' => true, 'exist' => true, 'outCase' => $outCase]; //exist and barcode qty okay
                }
                else {
                    return ['status' => false, 'exist' => true]; //exist and barcode qty not match
                }
            }
            else {
                return ['status' => false, 'exist' => false, 'outCase' => $outCase]; //record not exist
            }
        }
        else {
            return ['status' => false, 'exist' => true]; //exist and barcode qty not match
        }
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     */
    public static
            function checkLooseBarcodeExist($params) {
        $barcodeData = self::where('barcode', $params['barcode'])->where('barcode_type', 1)->where('product_id', $params['product_id'])->first();

        if (isset($barcodeData) && !empty($barcodeData) && @count($barcodeData) > 0) {
            return ['status' => true, 'exist' => true, 'barcodeData' => $barcodeData]; //barcode not exist
        }
        else {
            return ['status' => false, 'exist' => false]; //barcode not exist
        }
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     */
    public static
            function normalInnerExist($params) {
        $barcodeData = self::where('barcode', $params['barcode'])->where('barcode_type', 2)->where('product_id', $params['product_id'])->first();
        if (isset($barcodeData) && !empty($barcodeData) && @count($barcodeData) > 0) {
            return ['status' => true, 'exist' => true, 'barcodeData' => $barcodeData]; //barcode not exist
        }
        else {
            return ['status' => false, 'exist' => false]; //barcode not exist
        }
    }

    /**
     * @author Hitesh Tank
     * @param type $outerBarCodeId
     */
    public static
            function getInnerBarcodeData($outerBarCodeId) {
        return self::where('parent_id', $outerBarCodeId)->first();
    }

}
