<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QCChecklist extends Model
{
	use SoftDeletes; 
	public $table="qc_checklists";
    public static function getAllQCChecklist($perPage = '',$params = array()){
        
        $object=self::select();
        $object->orderBy($params['order_column'],$params['order_dir']);
        
        if (!empty($params['search'])) {
           $object->orWhere('name','like',"%".$params['search']."%");
          
        }
        //echo $object->toSQL();exit;
        return $object->paginate($perPage);
    }
    public function checklistPoints()
    {
        return $this->hasMany('\App\ChecklistPoint','qc_id');
    }
     public function delete()
    {
       
        $this->checklistPoints()->delete();
        
        return parent::delete();
    }
}
