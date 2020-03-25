<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SupplierMaster;
use Carbon\Carbon;
use \Illuminate\Support\Facades\DB;
use App\Setting;

class PurchaseOrder extends Model {

    //
    protected
            $table   = 'purchase_order_master';
    protected
            $guarded = [];
    protected
            $dates   = ['exp_deli_date', 'po_cancel_date', 'po_date ', 'po_updated_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function setExpDeliDateAttribute($value) {
        $this->attributes['exp_deli_date'] = !empty($value) ? date('Y-m-d', strtotime($value)) : null;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function setPoCancelDateAttribute($value) {
        $this->attributes['po_cancel_date'] = !empty($value) ? date('Y-m-d', strtotime($value)) : null;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function setPoDateAttribute($value) {
        $this->attributes['po_date'] = !empty($value) ? date('Y-m-d', strtotime($value)) : null;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getExpDeliDateAttribute() {
        return !empty($this->attributes['exp_deli_date']) ? date('d-M-Y', strtotime($this->attributes['exp_deli_date'])) : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getPoCancelDateAttribute() {
        return !empty($this->attributes['po_cancel_date']) ? date('d-M-Y', strtotime($this->attributes['po_cancel_date'])) : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getPoUpdatedAtAttribute() {
        return !empty($this->attributes['po_updated_at']) ? date('d-M-Y h:i a', strtotime($this->attributes['po_updated_at'])) : '';
    }

    /**
     * @author  Hitesh Tank
     * @return type
     */
    public
            function getPoDateAttribute() {
        return !empty($this->attributes['po_date']) ? date('d-M-Y', strtotime($this->attributes['po_date'])) : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getBillingStreetAddress1() {
        return !empty($this->attributes['street_address1']) ? $this->attributes['street_address1'] : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getBillingStreetAddress2() {
        return !empty($this->attributes['street_address2']) ? $this->attributes['street_address2'] : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getBillingCountry() {
        return !empty($this->attributes['street_country']) ? $this->attributes['street_country'] : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getBillingState() {
        return !empty($this->attributes['billing_state']) ? $this->attributes['billing_state'] : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getBillingCity() {
        return !empty($this->attributes['billing_city']) ? $this->attributes['billing_city'] : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getBillingZipcode() {
        return !empty($this->attributes['billing_zipcode']) ? $this->attributes['billing_zipcode'] : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function getSubTotalAttribute() {
        return floatval($this->attributes['sub_total']);
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function product() {
        return $this->hasMany('App\PurchaseOrderProduct', 'po_id');
    }

    /**
     * @author Hitesh Tank
     * @return supplier object
     */
    public
            function supplier() {
        return $this->belongsTo(SupplierMaster::class, 'supplier_id')->withTrashed()->withDefault();
    }

    /**
     * @author Hitesh Tank
     * @return supplier contact object
     */
    public
            function supplierContact() {
        return $this->belongsTo(SupplierContact::class, 'supplier_contact')->withTrashed()->withDefault();
    }

    /**
     * @author Hitesh Tank
     * @return supplier contact object
     */
    public
            function wareHouse() {
        return $this->belongsTo(WareHouse::class, 'recev_warehouse')->withDefault();
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function purchaseOrderCountry() {
        return $this->belongsTo(Country::class, 'country_id')->withDefault();
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function booking() {
        return $this->belongsToMany(Booking::class, 'booking_purchase_orders', 'po_id');
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public
            function hasBooking() {
        return $this->hasOne(BookingPO::class, 'po_id');
    }

    /**
     * @author Hitesh Tank
     * @return string
     */
    public static
            function autoGeneratePO() {
        $po_detail = self::select('po_number')->orderBy('id', 'desc')->first();
        if (!empty($po_detail)) {
            $expNum = explode('-', $po_detail->po_number);
            return 'PO' . '-' . sprintf("%07d", $expNum[1] + 1);
        }
        else {
            return 'PO-0000001';
        }
    }

    /**
     * @author  Hitesh Tank
     * @param type $perPage
     * @param type $params
     */
    public static
            function getAllOrders($perPage = '', $params = []) {
        $obj = self::leftJoin('supplier_master', function($q) {
                    $q->on('purchase_order_master.supplier_id', 'supplier_master.id');
                })
                ->leftJoin('po_products', function($q) {
                    $q->on('purchase_order_master.id', 'po_products.po_id');
                })
                ->leftJoin('products', function($q) {
            $q->on('products.id', 'po_products.product_id');
        });

        $obj->leftJoin('booking_purchase_orders', function($q) {
            $q->on('purchase_order_master.id', 'booking_purchase_orders.po_id');
        });

        $obj->leftJoin('booking_po_products', function($q) {
            $q->on('purchase_order_master.id', 'booking_po_products.po_id');
        });
        $obj->leftJoin('booking_purchase_orders_discrepancy', function($q) {
            $q->on('booking_po_products.id', 'booking_purchase_orders_discrepancy.booking_po_products_id');
        });
        $obj->select('booking_purchase_orders.created_at as booking_date', 'booking_purchase_orders.booking_id', 'is_drop_shipping', 'is_outstanding_po', 'purchase_order_master.id', 'po_number', 'supplier_order_number', 'po_status', 'purchase_order_master.created_at', 'name');
        $obj->selectRaw('SUM(po_products.total_quantity*po_products.unit_price) as total_cost');
        if (!empty($params['search'])) {
            $searchString = $params['search'];
            $obj->where(function($q) use($searchString) {
                $q->where('po_number', 'like', "%" . $searchString . "%")
                        ->orWhere('supplier_order_number', 'like', "%" . $searchString . "%");
            });
        }
        if (!empty($params['advanceSearch']['po_status'])) {
            $obj->where('po_status', $params['advanceSearch']['po_status']);
        }
        if (!empty($params['advanceSearch']['supplier_category'])) {
            $obj->where('supplier_category', $params['advanceSearch']['supplier_category']);
        }
        if (!empty($params['advanceSearch']['supplier_name'])) {
            $obj->where('name', 'like', "%" . $params['advanceSearch']['supplier_name'] . "%");
        }
        if (!empty($params['advanceSearch']['uk_po']) || !empty($params['advanceSearch']['import_po'])) {
            $obj->where(function($q) use($params) {
                if (isset($params['advanceSearch']['uk_po'])) {
                    $q->where('po_import_type', 1);
                }
                if (isset($params['advanceSearch']['import_po'])) {
                    $q->orWhere('po_import_type', 2);
                }
            });
        }

        if (!empty($params['advanceSearch']['missing_photo']) || !empty($params['advanceSearch']['missing_information'])) {
            $obj->where(function($q) use($params) {
                if (isset($params['advanceSearch']['missing_photo'])) {
                    $q->where('products.mp_image_missing', 1);
                }
                if (isset($params['advanceSearch']['missing_information'])) {
                    $q->orWhere('products.info_missing', 1);
                }
            });
        }

        if (!empty($params['advanceSearch']['outstanding_po'])) {
            $obj->where(function($q) use($params) {
                if (isset($params['advanceSearch']['outstanding_po'])) {
                    $q->where('purchase_order_master.is_outstanding_po', 1);
                }
            });
        }

        if (!empty($params['advanceSearch']['pending_descripancy'])) {
            $obj->where(function($q) use($params) {
                if (isset($params['advanceSearch']['pending_descripancy'])) {
                    $q->where('booking_purchase_orders_discrepancy.status', 0);
                }
            });
        }



        $obj->orderBy($params['order_column'], $params['order_dir']);
        $obj->groupBy('po_number');
        $obj->distinct();
        return $obj->paginate($perPage);
    }

    /**
     * @author Hitesh Tank
     * @param type $request
     */
    public
            function updateContent($request) {
        $purchaseObj                        = $this::find($request->po_id);
        $purchaseObj->sub_total             = $request->sub_total;
        $purchaseObj->total_margin          = $request->total_margin;
        $purchaseObj->total_import_duty     = $request->total_import_duty;
        $purchaseObj->total_delivery_charge = $request->total_delivery_charge;
        $purchaseObj->total_space           = $request->total_space;
        $purchaseObj->cost_per_cube         = $request->cost_per_cube;
        $purchaseObj->total_number_of_cubes = $request->total_number_of_cubes;
        $purchaseObj->remaining_space       = $request->remaining_space;
        $purchaseObj->total_cost            = $request->total_cost;
        $purchaseObj->save();
    }

    public static
            function getTaxReports($perPage = '', $params = array()) {

        $reportOb = PurchaseOrder::select('purchase_order_master.*', 'supplier_master.name');

        $reportOb->Join('supplier_master', function($q) {
            $q->on('supplier_master.id', 'purchase_order_master.supplier_id');
        });

        $reportOb->leftJoin('po_products', function($q) {
            $q->on('po_products.po_id', 'purchase_order_master.id');
        });

        $reportOb->selectRaw('SUM(po_products.itd_vat) as amount_before_vat');

        $reportOb->selectRaw('SUM(IF(po_products.import_duty_in_amount IS NOT null,po_products.import_duty_in_amount,0)) as import_duty_pound');

        $reportOb->selectRaw('SUM(IF(po_products.vat_in_amount IS NOT NULL, po_products.vat_in_amount ,0)) as vat_import');

        $reportOb->selectRaw('SUM(IF(po_products.vat_in_amount IS NOT NULL, po_products.vat_in_amount,0)) as vat_uk');

        $reportOb->selectRaw('SUM(IF(po_products.zero_rate_value IS NOT null, po_products.zero_rate_value ,0)) as total_zero_rated');

        $reportOb->selectRaw('IF(purchase_order_master.po_import_type=1 ,"UK PO" ,"Import PO") as po_type');

        if (!empty($params['advance_search'])) {
            $advance_search_data = $params['advance_search'];
            if (!empty($advance_search_data['supplier_id'])) {

                $reportOb->where('purchase_order_master.supplier_id', $advance_search_data['supplier_id']);
            }
            if (!empty($advance_search_data['sku'])) {
                $sku = $advance_search_data['sku'];
                $reportOb->Join('products', function($q) use ($sku) {
                    $q->on('products.id', 'po_products.product_id');
                    $q->where('sku', $sku);
                });
                /* $productIds = \App\Products::where('sku', $advance_search_data['sku'])->pluck('id')->toArray();

                  $poIds = \App\PurchaseOrderProduct::whereIn('product_id', $productIds)->pluck('po_id')->toArray();
                  $reportOb->whereIn('id', $poIds); */
            }
            if (!empty($advance_search_data['vat_type'])) {

                $reportOb->whereIn('po_products.vat_type', $advance_search_data['vat_type']);
            }
            if (!empty($advance_search_data['country_id'])) {

                $reportOb->where('purchase_order_master.country_id', $advance_search_data['country_id']);
            }
            if (!empty($advance_search_data['po_import_type'])) {

                $reportOb->where('purchase_order_master.po_import_type', $advance_search_data['po_import_type']);
            }
            if (!empty($advance_search_data['from_date']) && !empty($advance_search_data['to_date'])) {

                $fromDate = date('Y-m-d', strtotime($advance_search_data['from_date']));
                $toDate   = date('Y-m-d', strtotime($advance_search_data['to_date']));
                $reportOb->whereBetween('purchase_order_master.po_date', [$fromDate, $toDate]);
            }
        }
        else {

            //  $reportOb->whereMonth('po_date', Carbon::now()->month);
        }
        $reportOb->groupBy('purchase_order_master.id');
        $reportOb->orderBy($params['order_column'], $params['order_dir']);
        ;
        return $reportOb->get();
    }

    /**
     * @author Hitesh TAnk
     * @param type $perPage
     * @param type $params
     * @return type
     */
    public
    static
            function getPOs($perPage, $params) {

        $ShortDatedConfigValue = Setting::getData(["po_bookings"]);

        if (isset($ShortDatedConfigValue) && !empty($ShortDatedConfigValue)) {
            $ShortDatedConfigValue = $ShortDatedConfigValue[0]->column_val;
        }
        else {
            $ShortDatedConfigValue = 0;
        }

        $query = self::leftJoin("po_products", function($q) {
                    $q->on('purchase_order_master.id', 'po_products.po_id');
                })
                ->leftJoin('products as prod', function($q) {
            $q->on('po_products.product_id', 'prod.id');
        });

        $query->where('supplier_id', $params['supplier_id']);
        if (!empty($params['search'])) {
            $query->where(function($q) use ($params) {
                $q->where('po_number', 'like', '%' . $params['search'] . '%');
                $q->orWhere('supplier_order_number', 'like', '%' . $params['search'] . '%');
            });
        }
        if ($params['cancelled_po'] == 1) {
            $query->orWhere('po_status', 10);
        }
        //  $query->whereIn('po_status', [1, 2, 3, 4, 5, 6]);
        $query->select(['purchase_order_master.id', 'purchase_order_master.is_drop_shipping', 'purchase_order_master.is_outstanding_po', 'po_number', 'supplier_order_number',
            'exp_deli_date', 'sub_total', 'po_status',
            DB::raw("count(po_products.po_id) as total_skus"),
            DB::raw('SUM(IF(po_products.best_before_date IS NOT NULL,IF(ABS(DATEDIFF(po_products.best_before_date, CURDATE())) <= "' . $ShortDatedConfigValue . '",1,0),0)) as short_dated'),
            DB::raw("SUM(IF(po_products.is_variant=1,1,0)) as total_variant"),
            DB::raw("SUM(po_products.total_quantity) as total_quantity"),
            DB::raw("SUM(IF(prod.is_essential = 1,1,0)) as essential_product"),
            DB::raw("SUM(IF(prod.is_seasonal = 1,1,0)) as seasonal_product"), "purchase_order_master.status"]);
        $query->groupBy('purchase_order_master.id');
        $query->orderBy($params['order_column'], $params['order_dir']);
        return $query->get();
    }

    /**
     * @author Hitesh Tank
     * currently not in used duer to hold
     */
    public
            function deliveryDetail($filters = [], $noDiscrepancy = 0) {
        $purchaseOrder = $this;
        $booking       = $purchaseOrder->booking;
        if (isset($booking) && !empty($booking) && @count($booking) > 0) {
            $query = $booking[0]->bookingProducts()->whereNull('parent_id')->whereHas('bookingPODiscrepancy', function($q) use($filters) {
                if ($filters) {
                    $q->whereIn('discrepancy_type', $filters);
                }
            });
            if ($noDiscrepancy == 1) {
                $query->where('difference', 0);
            }

            $booking->bookingProducts = $query->get();
        }
        return $booking;
    }

    /**
     * @author Hitesh Tank
     * @param type $descreptionsIds
     * @return type
     */
    public
    static
            function getOutstandPOItems($params) {
        $query = BookingPODiscrepancy::join('booking_po_products', function($q) {
                    $q->on('booking_purchase_orders_discrepancy.booking_po_products_id', 'booking_po_products.id');
                })
                ->join('booking_purchase_orders', function($q) {
                    $q->on('booking_po_products.booking_id', 'booking_purchase_orders.booking_id');
                })
                ->join('purchase_order_master', function($q) {
                    $q->on('booking_purchase_orders.po_id', 'purchase_order_master.id');
                })
                ->join('po_products', function($q) {
            $q->on('purchase_order_master.id', 'po_products.po_id');
        });

        $query->select("po_products.*");
        $query->where('purchase_order_master.id', $params['purchase_order_id']);
        $query->where('po_products.product_id', $params['product_id']);
        $query->groupBy('po_products.id');
        return $query->get();
    }

}
