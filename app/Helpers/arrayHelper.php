<?php 
if (!function_exists('supplierCategory'))
{

    /**
     * used to display supplier category dropdown
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function supplierCategory($id = "")
    {
        $data[1] = 'Stock Supplier';
        $data[2] = 'No Stock Supplier';
        $data[3] = 'Carrier';
        $data[4] = 'Drop Shippling Supplier';
        return !empty($id) ? $data[$id] : $data;
    }
}

if (!function_exists('product_identifier_type'))
{

    /**
     * used to display supplier category dropdown
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function product_identifier_type($id = "")
    {
        $data[1] = 'UPC';
        $data[2] = 'EAN';
        $data[3] = 'Other';
        return !empty($id) ? $data[$id] : $data;
    }
}

if (!function_exists('WarehouseType'))
{

    /**
     * description
     *
     * @param
     * @return
     */
    function WarehouseType($id = "")
    {
        $data[1] = trans('messages.table_label.ware_type_wh');
        $data[2] = trans('messages.table_label.ware_type_off');
        $data[3] = trans('messages.table_label.ware_type_hq');
        $data[4] = trans('messages.table_label.ware_type_shop');
        return !empty($id) ? $data[$id] : $data;
    }
}

if (!function_exists('LocationType'))
{

    /**
     * description
     *
     * @param
     * @return
     */
    function LocationType($id = "")
    {
        $data[1] = 'Pick Location';
        $data[2] = 'Bulk Location';
        $data[3] = 'Pick Putaway Pallet';
        $data[4] = 'Bulk Putaway Pallet';
        $data[5] = 'Dispatch Location';
        $data[6] = 'Dropshipping Location';
        $data[7] = 'Aerosol Cage Location';        
        $data[8] = 'Quarantine Location';
        return !empty($id) ? $data[$id] : $data;
    }
}

if (!function_exists('barcodeType'))
{

    /**
     * description
     *
     * @param
     * @return
     */
    function barcodeType($id = "")
    {
        $data[1] = 'Single Barcode';
        $data[2] = 'Inner Case Barcode';
        $data[3] = 'Outer Case Barcode';
        return !empty($id) ? $data[$id] : $data;
    }
}

if (!function_exists('product_vat_types'))
{
    function product_vat_types($id = "")
    {
        $data[0] = 'Standard';
        $data[1] = 'Zero Rated';
        $data[2] = 'Mixed';
        return !empty($id) ? $data[$id] : $data;
    }
} 


if (!function_exists('product_logic_base_tags'))
{
    function product_logic_base_tags()
    {
        $data['flammable'] = 'Flammable';
        $data['reduced'] = 'Reduced';
        $data['do_not_buy_again'] = 'Do not buy again';
        $data['heavy'] = 'Heavy';
        $data['promotional'] = 'Promotional';
        return $data;
    }
}   


if(!function_exists('magento_product_type_id_from_name'))
{
    function magento_product_type_id_from_name($name="")
    {
        $data['normal'] = '1';
        $data['variation'] = '2';
        $data['parent'] = '3';        
        return !empty($name) ? $data[$name] : $data;
    }    
}


if(!function_exists('magento_product_type_name_from_id'))
{
    function magento_product_type_name_from_id($id="")
    {
        $data['1'] = 'normal';
        $data['2'] = 'variation';
        $data['3'] = 'parent';                
        return !empty($id) ? $data[$id] : $data;
    }    
} 