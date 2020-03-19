<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderRevises extends Model
{
     //
    protected $table = 'purchase_order_revises';

    protected $guarded=[];
    protected $dates = ['created_at', 'updated_at'];

    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function setPurchaseOrderContentAttribute($value){
        $this->attributes['purchase_order_content'] = serialize($value);
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function setPurchaseOrderItemContentAttribute($value){
        $this->attributes['purchase_order_item_content'] = serialize($value);
    }
    
     /**
     * @author Hitesh Tank
     * @return type
     */
    public function getPurchaseOrderContentAttribute(){
        return unserialize($this->attributes['purchase_order_content']); 
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getPurchaseOrderItemContentAttribute($value){
        return unserialize($this->attributes['purchase_order_item_content']);
    }
    
    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getCreatedAtAttribute(){
        return  !empty($this->attributes['created_at']) ? date('d-M-Y',strtotime($this->attributes['created_at'])) : '';
    }
    /**
     * @author  Hitesh Tank
     * @return type
     */
    public function getUpdatedAtAttribute(){
        return  !empty($this->attributes['updated_at']) ? date('d-M-Y',strtotime($this->attributes['updated_at'])) : '';
    }
    
    
    /**
     * Display a listing of the Revision.
     * @author : Hitesh Tank
     * @param Int $page
     * @param String $sorting_on
     * @param String $sorting_by (Possible value ASC or DESC)
     * @return \Illuminate\Http\Response
     */
    public static function getAllContent($perPage = '',
                                $params = array()){
        
        $reviseObj=self::select();
        $reviseObj->orderBy($params['order_column'],
                              $params['order_dir']);
        $reviseObj->where('purchase_order_id',$params['purchaseOrderId']);
        
        return $reviseObj->paginate($perPage);
    }
    /**
     * 
     * @param type $content
     * @return type
     */
    public function saveRevises($content){
        $count=self::where('purchase_order_id',$content['purchase_order_id'])->orderBy('id','desc')->first();
        $this->purchase_order_id=$content['purchase_order_id'];
        $this->purchase_order_number_sequence=$content['purchase_order_number_sequence'];
        $this->purchase_order_content=$content['purchase_order_content'];
        $this->sequence_number=(!empty($count) && @count($count)) ? ($count->sequence_number+1) : 1;
        $this->created_by=$content['created_by'];
        $this->modified_by=$content['modified_by'];
        return $this->save();
        
    }
}
