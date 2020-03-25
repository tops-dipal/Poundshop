<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Range extends Model
{
    use SoftDeletes; 

    protected $table = 'range_master';

    protected $fillable =['category_name','parent_id','seasonal_status','seasonal_from','seasonal_to','created_by','modified_by','status'];
    protected $appends = [
        'parent','children'
    ];
   
    public function getChildrenAttribute()
    {
        
    }
  
     public function parent()
    {
        return $this->belongsTo('App\Range', 'parent_id','id');
    }

     public function children()
    {
        return $this->hasMany('App\Range', 'parent_id','id');
    }

    public function getParentsNames() {
       
        if($this->parent) {
            /*if($this->parent->parent_id !=$this->id && !is_null($this->parent->parent_id))
            {
                return $this->parent->getParentsNames(). ">" . $this->id;
            }
            else
            {
                return $this->id;
            }*/
            if($this->parent->parent_id !=$this->id)
            {
                return $this->parent->getParentsNames(). ">" . $this->id;
            }
            else
            {
                return $this->id;
            }
        } else {
            return $this->id;
        }
    }
    public function getChildrensNames() {
        if($this->children) {
            return $this->parent->getChildrensNames(). ">" . $this->id;
        } else {
            return $this->id;
        }
    }


    public function getParentPath() {
        if($this->parent) {
            return $this->parent->getParentPath(). " >> " . $this->category_name;
        } else {
            return $this->category_name;
        }
    }

    public function magentoCategories(){
        return $this->belongsToMany(MagentoCategories::class, 'category_mappings', 'range_id', 'magento_category_id');

    }

    protected static function boot() 
    {
      parent::boot();

      static::deleting(function($ranges) {
         foreach ($ranges->children()->get() as $post) {
            $post->delete();
         }
      });
    }


    public function getChildsNames() {
        if($this->children) {
            return $this->children->getChildsNames(). ">" . $this->id;
        } else {
            return $this->id;
        }
    }

    public static function searchRange($keyword)
    {
        if(!empty($keyword))
        {
            $object = self::select(
                                    'id',
                                    'category_name',
                                    'parent_id',
                                    'path',

                                );
            
            $object->where('category_name','like',"%".$keyword."%");
            
            $result = $object->get();
            
            return $result->makeHidden(['parent', 'children'])->toArray();

        } 
        else
        {
            return array();
        }   
    }

    public static function get_all_range()
    {
        
    }

    public static function getAllRange(){
        $object=self::select('*');
        $object->orderBy('category_name','ASC');
        return $object->get()->makeHidden(['parent', 'children']);
    }

    public static function getAllRangeWithMappedCategory(){
        return Range::with('magentoCategories')->orderBy('category_name', 'asc')->get()->makeHidden(['parent', 'children']);
    }

    
}
