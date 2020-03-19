<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class ProductBarcode extends Model
{
    protected $table = 'product_barcodes';

    protected $fillable = [
                            'product_id',
                            'barcode_type',
                            'barcode',
                            'case_quantity',
                            'created_by',
                            'modified_by',
                          ];
     protected $dates = ['created_at', 'updated_at'];
     
    /**
     * 
     * @param type $barcodes
     * @return boolean
     */
    public function uniqueBarCode($barcodes){
        foreach($barcodes as $barcode){
            
            if($this->where('barcode',$barcode)->first()){
                return false;
            }
        }
        return true;
    }
    
    public function addBarcodes($datas){
        $bulkInsert=[];
        foreach($datas['barcodes'] as $key=>$value){
           $bulkInsert[]=[
                'product_id'=>$datas['products'][$key],
                'barcode'=>$value,
                'created_by'=>$datas['requestObj']->user->id,
                'modified_by'=>$datas['requestObj']->user->id,
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ];
        }
        $this::insert($bulkInsert);
    }
    


}
