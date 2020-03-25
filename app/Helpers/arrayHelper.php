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
        $data[2] = 'Consumables';
        $data[3] = 'Carrier';
        $data[4] = 'Drop Shippling Supplier';
        $data[5] = 'Sundries';
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
        $data[7] = 'Aerosol Cage Pick Location';        
        $data[12] = 'Aerosol Cage Bulk Location'; //added later
        $data[8] = 'Quarantine Location';
        $data[9] = 'On Hold';
        $data[10] = 'Return to Supplier';
        $data[11] = 'Photo Location';//added later
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

if(!function_exists('discrepancy_status_type'))
{
    function discrepancy_status_type($id="")
    {
        $data['1'] = 'Debit Note';
        $data['2'] = 'Keep it';
        $data['3'] = 'Dispose of';
        $data['4'] = 'Return to Supplier';
        return !empty($id) ? $data[$id] : $data;
    }    
} 

if(!function_exists('apply_float_value'))
{
    function apply_float_value($value) 
    {
        return floatval($value);
    }
}

if(!function_exists('priorityTypes'))
{
    function priorityTypes($id="") 
    {
        if($id!="" && ($id==0 || !in_array($id,array('2','4','6','8','10','12'))))
        {
            return 0;
        }
        else
        {
            $data['2'] = 'Emergency';
            $data['4'] = 'Priority-1';
            $data['6'] = 'Priority-2';
            $data['8'] = 'Priority-3';
            $data['10'] = 'Priority-4';
            $data['12'] = 'Priority-5';
            return !empty($id) ? $data[$id] : $data;
        }
    }
}

if(!function_exists('replenStatus'))
{
    function replenStatus($id="") 
    {
        $data['1'] = 'Dispatch - Next Day';
        $data['2'] = 'Dispatch - Standard';
        $data['3'] = 'Short Dated';
        $data['4'] = 'Expired';
        $data['5'] = 'Promotion';
        $data['6'] = 'Seasonal Products';
        $data['7'] = 'Day Stock Holding';
        return !empty($id) ? $data[$id] : $data;
    }
}

//Replen  Bulk Location Array
if(!function_exists('replenBulkLocationType'))
{
    function replenBulkLocationType() 
    {
        $data[2] = 'Bulk Location';
       
        $data[4] = 'Bulk Putaway Pallet';
      
        $data[12] = 'Aerosol Cage Bulk Location';     

        return array_keys($data);
    }
}

//Replen Pick Location Array
if(!function_exists('replenPickLocationType'))
{
    function replenPickLocationType() 
    {
        $data[1] = 'Pick Location';
      
        $data[3] = 'Pick Putaway Pallet';
       
        $data[6] = 'Dropshipping Location';

        $data[7] = 'Aerosol Cage Pick Location';        
       
        return array_keys($data);
    }
}



//Assign Aisle Priority
if(!function_exists('assignAislePriority'))
{
    function assignAislePriority() 
    {
        $data['2'] = 'Emergency';
        $data['4'] = 'Priority-1';
        $data['6'] = 'Priority-2';
      
       return array_keys($data);
    }
}

//Assign Aisle Priority
if(!function_exists('defaultPhotoLocation'))
{
    function defaultPhotoLocation() 
    {
        $data['aisle'] = 'Z';
        $data['rack'] = '00';
        $data['floor'] = '00';
        $data['box'] = '00';      
        return $data;
    }
}


//Location Assign Pick Location Array
if(!function_exists('locationAssignPickLocationType'))
{
    function locationAssignPickLocationType() 
    {
        $data[1] = 'Pick Location';

        $data[7] = 'Aerosol Cage Pick Location';        
       
        return array_keys($data);
    }
}


//Location Assign Bulk Location Array
if(!function_exists('locationAssignBulkLocationType'))
{
    function locationAssignBulkLocationType() 
    {
        $data[2] = 'Bulk Location';
       
        $data[12] = 'Aerosol Cage Bulk Location';     

        return array_keys($data);
    }
}

