<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Products;

class SupplierMaster extends Model
{
    use SoftDeletes; 

    protected $table = 'supplier_master';

    // auto fillable values
    protected $fillable = [
                            'name',
                            'account_no',
                            'min_po_amt',
                            'avg_lead_time',
                            'supplier_category',
                            'credit_limit_allowed',
                            'address_line1',
                            'address_line2',
                            'country_id',
                            'state_id',
                            'city_id',
                            'zipcode',
                            'date_rel_start',
                            'comment',
                            'payment_term',
                            'payment_days',
                            'allow_overall_discount',
                            'overall_percent_discount',
                            'allow_period_discount',
                            'period_discount_days',
                            'period_percent_discount',
                            'allow_retro_discount',
                            'retro_amount',
                            'retro_from_date',
                            'retro_to_date',
                            'retro_percent_discount',
                            'beneficiary_name',
                            'bene_address1',
                            'bene_address2',
                            'bene_country',
                            'bene_state',
                            'bene_city',
                            'bene_zipcode',
                            'bene_account_no',
                            'bene_bank_name',
                            'bank_address1',
                            'bank_address2',
                            'bank_country',
                            'bank_state',
                            'bank_city',
                            'bank_zipcode',
                            'bank_swift_code',
                            'bank_iban_no',
                            'term_condition',
                            'created_by',
                            'modified_by',
                            'status',
                          ];
    /**
     * Supplier Listing
     * @author : Shubham Dayma
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
                           
    public static function getAllSupplier($perPage = '', $params = array()){
        
        $object=self::select('supplier_master.*', 
                              'supplier_contact.name as prime_contact',
                              'supplier_contact.email as prime_email',
                              'supplier_contact.phone as prime_phone',
                              'cities.name as prime_city',
                            );
        
        $object->leftJoin('cities', 'cities.id', 'supplier_master.city_id');

        $object->leftJoin('supplier_contact', function($join){
            $join->on('supplier_contact.supplier_id', '=', 'supplier_master.id')
                  ->where('supplier_contact.is_primary', 1)
                  ->whereNull('supplier_contact.deleted_at');
        });

        if (!empty($params['search'])) {
           $object->orWhere('supplier_master.name','like',"%".$params['search']."%");
           $object->orWhere('supplier_master.account_no','like',"%".$params['search']."%");
           $object->orWhere('supplier_contact.name','like',"%".$params['search']."%");
        }

        if (!empty($params['advanceSearch']['filter_city_country'])) {
          
          $object->leftJoin('countries', 'countries.id', 'supplier_master.country_id');
          
          $object->where(function($q) use ($params) {
            $q->where('countries.name','like',"%".$params['advanceSearch']['filter_city_country']."%");
            $q->orWhere('cities.name','like',"%".$params['advanceSearch']['filter_city_country']."%");
          });
        }  

        if (!empty($params['advanceSearch']['filter_by_category'])) {
          $object->where('supplier_master.supplier_category', $params['advanceSearch']['filter_by_category']);
        }

        if (!empty($params['advanceSearch']['filter_suppliers_over_credit_limit'])) {
          // $object->where('supplier_master.allow_retro_discount', 1);
        }

        if (!empty($params['advanceSearch']['filter_suppliers_with_retro_discount'])) {
          $object->where('supplier_master.allow_retro_discount', 1);
        }  

        if($params['order_column'] == 'supplier_category')
        {   
          $object->orderByRaw('FIELD(supplier_master.supplier_category, 3,4,2,1) '.$params['order_dir']);
        }
        else
        {
          $object->orderBy($params['order_column'],
                              $params['order_dir']);
        }  

        return $object->paginate($perPage);
    }

    /**
     * Defining relationship with supplier contacts
     * @author : Shubham Dayma
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function SupplierContact(){
      return $this->hasMany('App\SupplierContact', 'supplier_id')->where('status',1);
    }
    
    public static function getAllSupplierList(){
        return self::select('*')->where('status',1)->orderBy('name')->get();
    }
    

    /**
     * 
     * @return supplier products type
     */
    public function supplierProducts(){
        return $this->belongsToMany(Products::class,'product_supplier','supplier_id','product_id')->withTimestamps();
    }
    
    
}
