<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Products;
use App\PurchaseOrder;

class SupplierMaster extends Model {

    use SoftDeletes;

    protected
            $table = 'supplier_master';
    // auto fillable values
    protected
            $fillable = [
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

    public static
            function getAllSupplier($perPage = '', $params = array()) {

        $object = self::select('supplier_master.*',
        'supplier_contact.name as prime_contact',
        'supplier_contact.email as prime_email',
        'supplier_contact.phone as prime_phone',
        'cities.name as prime_city',
        );

        $object->orderBy($params['order_column'], $params['order_dir']);

        $object->leftJoin('cities', 'cities.id', 'supplier_master.city_id');

        $object->leftJoin('supplier_contact', function($join) {
            $join->on('supplier_contact.supplier_id', '=', 'supplier_master.id')
                    ->where('supplier_contact.is_primary', 1)
                    ->whereNull('supplier_contact.deleted_at');
        });

        if (!empty($params['search'])) {

            $object->orWhere('supplier_master.name', 'like', "%" . $params['search'] . "%");
            $object->orWhere('supplier_master.account_no', 'like', "%" . $params['search'] . "%");
            $object->orWhere('supplier_contact.name', 'like', "%" . $params['search'] . "%");
            $object->orWhere('cities.name', 'like', "%" . $params['search'] . "%");
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
    public
            function SupplierContact() {
        return $this->hasMany('App\SupplierContact', 'supplier_id')->where('status', 1);
    }

    public static
            function getAllSupplierList() {
        return self::select('*')->where('status', 1)->orderBy('name')->get();
    }

    /**
     *
     * @return supplier products type
     */
    public
            function supplierProducts() {
        return $this->belongsToMany(Products::class, 'product_supplier', 'supplier_id', 'product_id')->withTimestamps();
    }

    public static function supplierRatings($supplierId)
    {
       
        $object=PurchaseOrder::select('purchase_order_master.po_date',
        'purchase_order_master.po_number','purchase_order_master.sub_total','purchase_order_master.id as poo_id');

        $object->selectRaw('YEAR(purchase_order_master.po_date) as year');
       // $object->selectRaw('SUM(purchase_order_master.sub_total) as total_po_value');

       $object->selectRaw('(select sum(sub_total) from purchase_order_master where  supplier_id='.$supplierId.' and po_status=9 GROUP By YEAR(po_date)) as total_po_value');

        $object->Join('po_products', function($join) {
            $join->on('po_products.po_id', '=', 'purchase_order_master.id');
        });

       /* $object->Join('booking_purchase_orders', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'purchase_order_master.id');
        });*/

      /*  $object->Join('bookings', function($join) {
            $join->on('bookings.id', '=', 'booking_purchase_orders.booking_id');
        });*/

        $object->Join('booking_po_products', function($join) {
            $join->on('booking_po_products.po_id', '=', 'purchase_order_master.id');
             $join->on('booking_po_products.po_product_id', '=', 'po_products.id');
            $join->whereNULL('booking_po_products.parent_id');
            //$join->groupBy('booking_po_products.po_id','booking_po_products.booking_id');
        });
        $object->selectRaw('SUM(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty) as pick_bulk_pallet_qty');

       

        $object->selectRaw('IF(booking_po_products.is_photobooth=0, SUM(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty),SUM(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty)+1) as item_delivered');

        $object->selectRaw('SUM(IF(booking_po_products.pick_pallet_qty!=0 || booking_po_products.bulk_pallet_qty!=0,po_products.unit_price,0)) as sum_unit_price');

         //$object->selectRaw('IF(booking_po_products.is_photobooth=0, SUM(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty)*SUM(IF(booking_po_products.pick_pallet_qty!=0 || booking_po_products.bulk_pallet_qty!=0,po_products.unit_price,1)),SUM(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty)+1)*SUM(IF(booking_po_products.pick_pallet_qty!=0 || booking_po_products.bulk_pallet_qty!=0,po_products.unit_price,1)) as deli');
        $object->selectRaw('SUM(IF(booking_po_products.is_photobooth=0, (booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty)*po_products.unit_price,(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty+1)*po_products.unit_price)) as deli_val');

        $object->selectRaw('(SUM(IF(booking_po_products.is_photobooth=0, (booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty)*po_products.unit_price,(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty+1)*po_products.unit_price))/(select sum(sub_total) from purchase_order_master where  supplier_id='.$supplierId.' and po_status=9 GROUP By YEAR(po_date))*100) as percentage');

        $object->selectRaw('(IF(booking_po_products.is_photobooth=0, SUM(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty),SUM(booking_po_products.pick_pallet_qty+booking_po_products.bulk_pallet_qty)+1)/SUM(po_products.total_quantity))*100 as percentage1');
       // $object->selectRaw('(item_delivered*sum_unit_price) as delivered_value');
         //$object->selectRaw('item_delivered*SUM(po_products.unit_price) as delivered_value');

        $object->selectRaw('SUM(po_products.total_quantity) as items_ordered');
        
        $object->where('purchase_order_master.supplier_id',$supplierId);
        $object->where('purchase_order_master.po_status',9);
        $object->groupBy('year');
       
        return $object->get();
    }

    public static function getSummaryOfEmptyPallets($supplierId){

        $object=\App\Pallet::select('pallets_master.id','pallets_master.name','booking_pallets.pallet_type','booking_pallets.num_of_pallets');
        $object->Join('booking_pallets', function($join) {
            $join->on('booking_pallets.pallet_id', '=', 'pallets_master.id');
             $join->groupBy('booking_pallets.pallet_type');
        });
        $object->Join('bookings', function($join) use ($supplierId){
            $join->on('bookings.id', '=', 'booking_pallets.booking_id');
            $join->where('bookings.supplier_id',$supplierId);
        });
        $object->selectRaw('SUM(IF(booking_pallets.pallet_type=1,(booking_pallets.num_of_pallets),0)) as total_received_pallets');

        $object->selectRaw('SUM(IF(booking_pallets.pallet_type=2,(booking_pallets.num_of_pallets),0)) as total_return_pallets');

        $object->groupBy('pallets_master.id');
        
        if(count( $object->get())==0)
        {
            return \App\Pallet::select('name','id')->get();
        }
        else
        {
            return $object->get();
        }
        
    }

    public static function getEmptyPallets($supplierId){

        $object=\App\Booking::with(['bookingPallets'=>function($q){
            
            $q->select('booking_pallets.*','pallets_master.name','pallets_master.id as pm_id');
            $q->selectRaw('SUM(IF(booking_pallets.pallet_type=1,(booking_pallets.num_of_pallets),0)) as total_received_pallets');
            $q->selectRaw('SUM(IF(booking_pallets.pallet_type=2,(booking_pallets.num_of_pallets),0)) as total_return_pallets');

            $q->selectRaw('SUM(IF(booking_pallets.pallet_type=2,(booking_pallets.num_of_pallets),0)) - SUM(IF(booking_pallets.pallet_type=1,(booking_pallets.num_of_pallets),0)) as total_pallet');
          
            $q->leftJoin('pallets_master', function($join){
                $join->on('pallets_master.id', '=', 'booking_pallets.pallet_id');
            });
            $q->groupBy(['booking_pallets.pallet_id','booking_pallets.booking_id']);
        }])->select('bookings.id','bookings.booking_ref_id','bookings.arrived_date');
     
        $object->where('bookings.supplier_id',$supplierId);

        $object->groupBy('bookings.id');
       
        return $object->get();

      
    }

}
