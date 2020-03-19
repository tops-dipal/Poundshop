<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierContact extends Model
{
    use SoftDeletes; 

    protected $table = 'supplier_contact';
    
    // auto fillable values    
    protected $fillable = [
    						'supplier_id',
    						'name',
    						'email',
    						'phone',
                            'mobile',
    						'designation',
    						'is_primary',
    						'created_by',
    						'modified_by',
    					];
}
