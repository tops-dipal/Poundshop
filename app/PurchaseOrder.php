<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SupplierMaster;
use Carbon\Carbon;
class PurchaseOrder extends Model
{
    //
    protected $table = 'purchase_order_master';

    protected $guarded=[];
    protected $dates = ['exp_deli_date','po_cancel_date','po_date ','po_updated_at','created_at', 'updated_at', 'deleted_at'];
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function setExpDeliDateAttribute($value){
        $this->attributes['exp_deli_date'] = !empty($value) ? date('Y-m-d',strtotime($value)) : null;
    }
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function setPoCancelDateAttribute($value){
        $this->attributes['po_cancel_date'] = !empty($value) ? date('Y-m-d',strtotime($value)) : null;
    }
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function setPoDateAttribute($value){
        $this->attributes['po_date'] = !empty($value) ?  date('Y-m-d',strtotime($value)) : null;
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getExpDeliDateAttribute(){
        return  !empty($this->attributes['exp_deli_date']) ? date('d-M-Y',strtotime($this->attributes['exp_deli_date'])) : '';
    }
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getPoCancelDateAttribute(){
        return  !empty($this->attributes['po_cancel_date']) ? date('d-M-Y',strtotime($this->attributes['po_cancel_date'])) : '';
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getPoUpdatedAtAttribute(){
        return !empty($this->attributes['po_updated_at']) ? date('d-M-Y h:i a',strtotime($this->attributes['po_updated_at'])) : '';
    }
    /**
     * @author  Hitesh Tank
     * @return type
     */
    public function getPoDateAttribute(){
        return  !empty($this->attributes['po_date']) ? date('d-M-Y',strtotime($this->attributes['po_date'])) : '';
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getBillingStreetAddress1(){
        return  !empty($this->attributes['street_address1']) ? $this->attributes['street_address1'] : '';
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getBillingStreetAddress2(){
        return  !empty($this->attributes['street_address2']) ? $this->attributes['street_address2'] : '';
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getBillingCountry(){
        return  !empty($this->attributes['street_country']) ? $this->attributes['street_country'] : '';
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getBillingState(){
        return  !empty($this->attributes['billing_state']) ? $this->attributes['billing_state'] : '';
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getBillingCity(){
        return  !empty($this->attributes['billing_city']) ? $this->attributes['billing_city'] : '';
    }
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getBillingZipcode(){
        return  !empty($this->attributes['billing_zipcode']) ? $this->attributes['billing_zipcode'] : '';
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getSubTotalAttribute(){
        return  floatval($this->attributes['sub_total']);
    }
    
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function product()
    {
        return $this->hasMany('App\PurchaseOrderProduct','po_id');
    }
    
    /**
     * @author Hitesh Tank
     * @return supplier object
     */
    public function supplier(){
        return $this->belongsTo(SupplierMaster::class,'supplier_id')->withTrashed()->withDefault();
    }
    
    /**
     * @author Hitesh Tank
     * @return supplier contact object
     */
    public function supplierContact(){
        return $this->belongsTo(SupplierContact::class,'supplier_contact')->withTrashed()->withDefault();
    }
    
    
    /**
     * @author Hitesh Tank
     * @return supplier contact object
     */
    public function wareHouse(){
        return $this->belongsTo(WareHouse::class,'recev_warehouse')->withDefault();
    }
    
    public function purchaseOrderCountry(){
        return $this->belongsTo(Country::class,'country_id')->withDefault();
    }
    
    /**
     * 
     * @return string
     */
    public static function autoGeneratePO(){
        $po_detail=self::select('po_number')->orderBy('id','desc')->first();
        if(!empty($po_detail)){
            $expNum=explode('-', $po_detail->po_number);
            return 'PS'.'-'.sprintf("%07d",$expNum[1]+1);
        }else{
            return 'PS-0000001';
        }
    }
    
    
    /**
     * @author  Hitesh Tank
     * @param type $perPage
     * @param type $params
     */
    public static function getAllOrders($perPage = '',
                                $params = []){
        $obj=self::with('product')
        ->leftJoin('supplier_master',function($q){
            $q->on('purchase_order_master.supplier_id','supplier_master.id');
        })
        ->leftJoin('po_products',function($q){
            $q->on('purchase_order_master.id','po_products.po_id');
        })
        
        ->leftJoin('products',function($q){
            $q->on('products.id','po_products.product_id');
        });
        $obj->select('purchase_order_master.id','po_number','supplier_order_number','po_status','purchase_order_master.created_at','name');
        if(!empty($params['search'])){
            $searchString=$params['search'];
            $obj->where(function($q) use($searchString){
                $q->where('po_number' ,
                    'like' ,
                    "%".$searchString."%")
                     ->orWhere('supplier_order_number','like' ,
                    "%".$searchString."%");
            });
        }
        if(!empty($params['advanceSearch']['po_status'])){
            $obj->where('po_status',$params['advanceSearch']['po_status']);
        }    
        if(!empty($params['advanceSearch']['supplier_category'])){
            $obj->where('supplier_category',$params['advanceSearch']['supplier_category']);
        }    
        if(!empty($params['advanceSearch']['supplier_name'])){
            $obj->where('name','like',"%".$params['advanceSearch']['supplier_name']."%");
        }    
        if(!empty($params['advanceSearch']['uk_po']) || !empty($params['advanceSearch']['import_po'])){
            $obj->where(function($q) use($params) {
                if(isset($params['advanceSearch']['uk_po'])){
                    $q->where('po_import_type',1);
                }
                if(isset($params['advanceSearch']['import_po'])){
                    $q->orWhere('po_import_type',2);
                }
            });
        }
        
        if(!empty($params['advanceSearch']['missing_photo']) || !empty($params['advanceSearch']['missing_information'])){
            $obj->where(function($q) use($params) {
                if(isset($params['advanceSearch']['missing_photo'])){
                    $q->where('products.mp_image_missing',1);
                }
                if(isset($params['advanceSearch']['missing_information'])){
                    $q->orWhere('products.info_missing',1);
                }
            });
        }
        
        
        
        $obj->orderBy($params['order_column'] ,
            $params['order_dir']);
        $obj->distinct();
        return $obj->paginate($perPage);
    }
    
    public function updateContent($request){
        $purchaseObj=$this::find($request->po_id);
        $purchaseObj->sub_total = $request->sub_total;
        $purchaseObj->total_margin = $request->total_margin;
        $purchaseObj->total_import_duty = $request->total_import_duty;
        $purchaseObj->total_delivery_charge = $request->total_delivery_charge;
        $purchaseObj->total_space = $request->total_space;
        $purchaseObj->cost_per_cube = $request->cost_per_cube;
        $purchaseObj->total_number_of_cubes = $request->total_number_of_cubes;
        $purchaseObj->remaining_space = $request->remaining_space;
        $purchaseObj->total_cost = $request->total_cost;
        $purchaseObj->save();
    }

    public static function getTaxReports($perPage = '',$params = array())
    {

        $reportOb=self::select();
        $reportOb->leftJoin('supplier_master','supplier_master.id','purchase_order_master.supplier_id');
        
         
       if(!empty($params['advance_search']))
        {
            $advance_search_data=$params['advance_search'];
             if(!empty($advance_search_data['supplier_id']))
            {
                $reportOb->where('purchase_order_master.supplier_id',$advance_search_data['supplier_id']);
            }
             if(!empty($advance_search_data['sku']))
            {
                $productIds=\App\Products::where('sku',$advance_search_data['sku'])->pluck('id')->toArray();

               $poIds=\App\PurchaseOrderProduct::whereIn('product_id',$productIds)->pluck('po_id')->toArray();
               $reportOb->whereIn('id',$poIds);
            }
             if(!empty($advance_search_data['vat_type']))
            {
                
                 $reportOb->join('po_products', 'purchase_order_master.id', '=', 'po_products.po_id')->whereIn('po_products.vat_type',$advance_search_data['vat_type']);
            }
             if(!empty($advance_search_data['country_id']))
            {
                $reportOb->where('purchase_order_master.country_id',$advance_search_data['country_id']);
            }
             if(!empty($advance_search_data['po_import_type']))
            {
                $reportOb->where('purchase_order_master.po_import_type',$advance_search_data['po_import_type']);
            }
             if(!empty($advance_search_data['from_date']) && !empty($advance_search_data['to_date']))
            {
                $fromDate=date('Y-m-d',strtotime($advance_search_data['from_date']));
                $toDate=date('Y-m-d',strtotime($advance_search_data['to_date']));
                $reportOb->whereBetween('purchase_order_master.po_date',[$fromDate,$toDate]);
            }
        }
        else{
            $reportOb->whereMonth('po_date', Carbon::now()->month);
        }
        $reportOb->orderBy($params['order_column'],$params['order_dir']);

        return $reportOb->paginate($perPage);
    }
}
