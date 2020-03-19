<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Products;

class PurchaseOrderProduct extends Model {

    //
    protected
            $table   = 'po_products';
    protected
            $guarded = [];
    protected
            $dates   = ['best_before_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function setBestBeforeDateAttribute($value) {
        $this->attributes['best_before_date'] = !empty($value) ? date('Y-m-d', strtotime($value)) : null;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getExpectedMrosAttribute() {
        return floatval($this->attributes['expected_mros']);
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getSelPriceAttribute() {
        return !empty($this->attributes['sel_price']) ? floatval($this->attributes['sel_price']) : 1;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getSelQtyAttribute() {
        return !empty($this->attributes['sel_qty']) ? floatval($this->attributes['sel_qty']) : 1;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getUnitPriceAttribute() {
        return !empty($this->attributes['unit_price']) ? floatval($this->attributes['unit_price']) : 0;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getVatAttribute() {
        return floatval($this->attributes['vat']);
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getImportDutyAttribute() {
        return floatval($this->attributes['import_duty']);
    }

    /**
     * @author  Hitesh Tank
     * @return type
     */
    public
            function getBestBeforeDateAttribute() {
        return !empty($this->attributes['best_before_date']) ? date('d-M-Y', strtotime($this->attributes['best_before_date'])) : '';
    }

    public
            function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function products() {
        return $this->belongsTo(Products::class, 'product_id');
    }

    /**
     *
     * @param type $data
     * @return \self
     */
    public static
            function saveItemContent($data) {
        $obj                                  = new self;
        $obj->po_id                           = $data['po_id'];
        $obj->product_id                      = $data['product_id'];
        $obj->supplier_sku                    = $data['supplier_sku'];
        $obj->barcode                         = $data['new_barcode'];
        $obj->qty_per_box                     = $data['qty_per_box'];
        $obj->total_box                       = $data['total_box'];
        $obj->total_quantity                  = $data['total_quantity'];
        $obj->unit_price                      = $data['unit_price'];
        $obj->total_product_cost              = $data['total_product_cost'];
        $obj->vat                             = !empty($data['vat']) ? $data['vat'] : 0;
        $obj->vat_type                        = !empty($data['vat_type']) ? $data['vat_type'] : 0;
        $obj->standard_rate                   = !empty($data['standard_rate']) ? $data['standard_rate'] : 0;
        $obj->standard_rate_value             = !empty($data['standard_rate_value']) ? $data['standard_rate_value'] : 0;
        $obj->zero_rate                       = !empty($data['zero_rate']) ? $data['zero_rate'] : 0;
        $obj->zero_rate_value                 = !empty($data['zero_rate_value']) ? $data['zero_rate_value'] : 0;
        $obj->best_before_date                = !empty($data['best_before_date']) ? $data['best_before_date'] : null;
        $obj->expected_mros                   = $data['expected_mros'];
        $obj->sel_qty                         = $data['sel_qty'];
        $obj->sel_price                       = $data['sel_price'];
        $obj->landed_product_cost             = $data['landed_product_cost'];
        $obj->net_selling_price_excluding_vat = $data['net_selling_price_excluding_vat'];
        $obj->total_net_selling_price         = $data['total_net_selling_price'];
        $obj->total_net_profit                = $data['total_net_profit'];
        $obj->total_net_margin                = $data['total_net_margin'];
        $obj->mros                            = $data['mros'];
        if ($data['po_import_type'] == 2) {
            $obj->cube_per_box           = $data['cube_per_box'];
            $obj->total_num_cubes        = $data['total_num_cubes'];
            $obj->vat_in_amount          = $data['vat_in_amount'];
            $obj->import_duty_in_amount  = $data['import_duty_in_cost'];
            $obj->total_delivery_charge  = $data['delivery_charge'];
            $obj->landed_price_in_pound  = $data['landed_price_in_pound'];
            $obj->itd_vat                = $data['itd_vat'];
            $obj->total_vat              = $data['total_vat'];
            $obj->currency_exchange_rate = $data['currency_exchange_rate'];
            $obj->import_duty            = !empty($data['import_duty']) ? $data['import_duty'] : 0;
        }
        $obj->save();
        return $obj;
    }

    /**
     * @author Hitesh Tank
     * @param type $data
     * @return type
     */
    public static
            function preparedUpdateItems($data) {

        $dataArray = ['po_id' => $data['po_id'],
            'product_id' => $data['product_id'],
            'supplier_sku' => $data['supplier_sku'],
            'barcode' => $data['new_barcode'],
            'qty_per_box' => $data['qty_per_box'],
            'total_box' => $data['total_box'],
            'total_quantity' => $data['total_quantity'],
            'unit_price' => $data['unit_price'],
            'total_product_cost' => $data['total_product_cost'],
            'vat' => !empty($data['vat']) ? $data['vat'] : 0,
            'vat_type' => !empty($data['vat_type']) ? $data['vat_type'] : 0,
            'standard_rate' => !empty($data['standard_rate']) ? $data['standard_rate'] : 0,
            'standard_rate_value' => !empty($data['standard_rate_value']) ? $data['standard_rate_value'] : 0,
            'zero_rate' => !empty($data['zero_rate']) ? $data['zero_rate'] : 0,
            'zero_rate_value' => !empty($data['zero_rate_value']) ? $data['zero_rate_value'] : 0,
            'best_before_date' => !empty($data['best_before_date']) ? date('Y-m-d', strtotime($data['best_before_date'])) : null,
            // 'expected_mros' => $data['expected_mros'],
            'sel_qty' => $data['sel_qty'],
            'sel_price' => $data['sel_price'],
            'landed_product_cost' => $data['landed_product_cost']
            , 'net_selling_price_excluding_vat' => $data['net_selling_price_excluding_vat']
            , 'total_net_selling_price' => $data['total_net_selling_price']
            , 'total_net_profit' => $data['total_net_profit']
            , 'total_net_margin' => $data['total_net_margin']
            , 'mros' => $data['mros']
        ];
        if ($data['po_import_type'] == 2) {
            $dataArray['cube_per_box']                    = $data['cube_per_box'];
            $dataArray['total_num_cubes']                 = $data['total_num_cubes'];
            $dataArray['vat_in_amount']                   = $data['vat_in_amount'];
            $dataArray['total_delivery_charge']           = $data['delivery_charge'];
            $dataArray['import_duty_in_amount']           = $data['import_duty_in_cost'];
            $dataArray['landed_price_in_pound']           = $data['landed_price_in_pound'];
            $dataArray['itd_vat']                         = $data['itd_vat'];
            $dataArray['total_vat']                       = $data['total_vat'];
            $dataArray['currency_exchange_rate']          = $data['currency_exchange_rate'];
            $dataArray['net_selling_price_excluding_vat'] = $data['net_selling_price_excluding_vat'];
            $dataArray['import_duty']                     = !empty($data['import_duty']) ? $data['import_duty'] : 0;
        }

        return $dataArray;
    }

    /**
     *
     * @param type $updateItems
     * @param type $updatedIds
     */
    public static
            function updateItems($updateItems, $updatedIds) {
        foreach ($updatedIds as $key => $value) {
            self::where('id', $value)->update($updateItems[$key]);
        }
    }

    /**
     *
     * @param type $items
     * @return type
     */
    public static
            function calculateSubTotal($items) {
        $subTotal = 0.00;
        if ($items != "" && !empty($items) && isset($items)) {
            foreach ($items as $item) {
                if (isset($item->total_quantity) && isset($item->unit_price)) {
                    $subTotal += floatval($item->total_quantity * $item->unit_price);
                }
            }
        }
        else {
            return 0.00;
        }
        return floatval($subTotal);
    }

    public static
            function reCalculateItemsData($purchaseOrder) {
        $subTotal             = 0;
        $totalNetProfit       = 0;
        $totalNetSellingPrice = 0;
        $totalImportDuty      = 0;
        $totalCost            = 0;
        $totalNoOfCubes       = 0;
        foreach ($purchaseOrder->product as $product) {
            if ($purchaseOrder->po_import_type == 1) { // UK PO
                $subTotal             += $product->total_product_cost;
                $totalNetProfit       += $product->total_net_profit;
                $totalNetSellingPrice += $product->total_net_profit;
            }
            else { // Import PO
                $subTotal             += $product->total_product_cost;
                $totalNetProfit       += $product->total_net_profit;
                $totalNetSellingPrice += $product->total_net_profit;
                $totalImportDuty      += $product->import_duty_in_amount;
                $totalNoOfCubes       += $product->total_num_cubes;
            }
        }
        $purchaseOrder->sub_total             = $subTotal;
        $purchaseOrder->total_import_duty     = $totalImportDuty;
        $purchaseOrder->total_cost            = $subTotal + $totalImportDuty + $purchaseOrder->total_delivery_charge;
        $purchaseOrder->total_margin          = $totalNetSellingPrice != 0 ? $totalNetProfit / $totalNetSellingPrice : 0;
        $purchaseOrder->total_number_of_cubes = $totalNoOfCubes;
        $purchaseOrder->remaining_space       = $purchaseOrder->total_space - $totalNoOfCubes;
        $purchaseOrder->save();
//        sub_total
//        total_import_duty
//        total_delivery_charge
//        total_cost
//        total_margin
//        total_space
//        cost_per_cube
//        total_number_of_cubes
//        remaining_space
    }

}
