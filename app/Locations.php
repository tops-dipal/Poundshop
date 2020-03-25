<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model {

//
    protected
            $table   = 'locations_master';
    protected
            $appends = [
        'location_type'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected
            $guards;

    /**
     * Display a listing of the Contient.
     * @author : Mohit Trivedi
     * @param Int $page
     * @param String $sorting_on
     * @param String $sorting_by (Possible value ASC or DESC)
     * @return \Illuminate\Http\Response
     */
    public static
            function getAllLocations($perPage = '', $params = array()) {
        $palletOb = self::select('locations_master.*', 'warehouse_master.name as site_name');
        $palletOb->selectRaw("IF(locations_master.id=locations_assign.location_id,count('locations_assign.id'),0) as not_allowed_edit_delete_status");
        $palletOb->orderBy($params['order_column'], $params['order_dir']);
        $i        = 0;
        if (!empty($params['search'])) {
            $palletOb->where(function($q) use($params) {
                $q->where('aisle', 'like', "%" . $params['search'] . "%");
                $q->orWhere('rack', 'like', "%" . $params['search'] . "%");
                $q->orWhere('floor', 'like', "%" . $params['search'] . "%");
                $q->orWhere('box', 'like', "%" . $params['search'] . "%");
                $q->orWhere('location', 'like', "%" . $params['search'] . "%");
            });
        }

        if (!empty($params['advance_search'])) {
            $advance_search_data = $params['advance_search'];

            if (!empty($advance_search_data['fil_aisle'])) {
                $palletOb->where('aisle', $advance_search_data['fil_aisle']);
            }

            if (!empty($advance_search_data['fil_rack'])) {
                $palletOb->where('rack', $advance_search_data['fil_rack']);
            }

            if (!empty($advance_search_data['fil_floor'])) {
                $palletOb->where('floor', $advance_search_data['fil_floor']);
            }

            if (!empty($advance_search_data['fil_box'])) {
                $palletOb->where('box', $advance_search_data['fil_box']);
            }

            if (!empty($advance_search_data['fil_location'])) {
                $palletOb->where('location', $advance_search_data['fil_location']);
            }

            if (!empty($advance_search_data['fil_site_id'])) {
                $palletOb->where('site_id', $advance_search_data['fil_site_id']);
            }

            if (!empty($advance_search_data['fil_location_type'])) {
                $palletOb->where('type_of_location', $advance_search_data['fil_location_type']);
            }

            if (!empty($advance_search_data['fil_status']) || $advance_search_data['fil_status'] == '0') {
                $palletOb->where('status', $advance_search_data['fil_status']);
            }
        }
        $palletOb->leftJoin("locations_assign", function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
        });
        $palletOb->leftJoin("warehouse_master", function($q) {
            $q->on('warehouse_master.id', 'locations_master.site_id');
        });
        $palletOb->leftJoin("replen_user_pallet", function($q) {
            $q->on('replen_user_pallet.location_id', 'locations_master.id');
        });
        $palletOb->groupBy('locations_master.id');
        return $palletOb->paginate($perPage);
    }

    public static
            function getAllLocationsSelected($perPage = '', $params = array()) {
        $palletOb = self::select('id');
        $palletOb->orderBy($params['order_column'], $params['order_dir']);
        $i        = 0;
        if (!empty($params['search'])) {
            $palletOb->where(function($q) use($params) {
                $q->where('aisle', 'like', "%" . $params['search'] . "%");
                $q->orWhere('rack', 'like', "%" . $params['search'] . "%");
                $q->orWhere('floor', 'like', "%" . $params['search'] . "%");
                $q->orWhere('box', 'like', "%" . $params['search'] . "%");
                $q->orWhere('location', 'like', "%" . $params['search'] . "%");
            });
        }

        if (!empty($params['advance_search'])) {
            $advance_search_data = $params['advance_search'];

            if (!empty($advance_search_data['fil_aisle'])) {
                $palletOb->where('aisle', $advance_search_data['fil_aisle']);
            }

            if (!empty($advance_search_data['fil_rack'])) {
                $palletOb->where('rack', $advance_search_data['fil_rack']);
            }

            if (!empty($advance_search_data['fil_floor'])) {
                $palletOb->where('floor', $advance_search_data['fil_floor']);
            }

            if (!empty($advance_search_data['fil_box'])) {
                $palletOb->where('box', $advance_search_data['fil_box']);
            }

            if (!empty($advance_search_data['fil_location'])) {
                $palletOb->where('location', $advance_search_data['fil_location']);
            }

            if (!empty($advance_search_data['fil_site_id'])) {
                $palletOb->where('site_id', $advance_search_data['fil_site_id']);
            }

            if (!empty($advance_search_data['fil_location_type'])) {
                $palletOb->where('type_of_location', $advance_search_data['fil_location_type']);
            }

            if (!empty($advance_search_data['fil_status']) || $advance_search_data['fil_status'] == '0') {
                $palletOb->where('status', $advance_search_data['fil_status']);
            }
        }

        return $palletOb->paginate($perPage);
    }

    public static
            function searchByKeyword($keyword, $warehouse_id = "") {
        if (!empty($keyword)) {
            $where_array = array(
                'location' => $keyword,
                'status'   => 1,
            );

            if (!empty($warehouse_id)) {
                $where_array['site_id'] = $warehouse_id;
            }

            return self::select('id', 'location', 'type_of_location', 'site_id')->where(
                            $where_array)->first();
        }
        else {
            return array();
        }
    }

    /**
     * @author Hitesh Tank
     * @param type $keyword
     * @return type
     */
    public static
            function searchByPickBulkLocation($keyword) {
        if (!empty($keyword)) {
            $where_array = array(
                'location' => $keyword,
                'status'   => 1,
            );

            return self::select('id', 'location', 'type_of_location', 'site_id')->where($where_array)->whereIn('type_of_location', [3, 4])->first();
        }
        else {
            return array();
        }
    }

    /**
     * @author Hitesh tank
     * @param type $params
     * @return type
     */
    public static
            function getScannedPalletLocationAssignDetail($params = []) {
        $query = self::join("locations_assign", function($q) {
                    $q->on('locations_master.id', 'locations_assign.location_id');
                });
        $query->where('location', $params['location']);
        if (!empty($params['booking_id']))
            $query->where('booking_id', $params['booking_id']);
        if (!empty($params['po_id']))
            $query->where('po_id', $params['po_id']);
        $query->where('putaway_type', $params['request_putaway_type']);
        $query->where('product_id', $params['product_id']);
        $query->where('warehouse_id', $params['warehouse_id']);
        $query->select('locations_assign.*');
        return $query->first();
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     */
    public static
            function getScannedPalletProductLocationTransactionData($params = []) {
        $query = LocationAssignTrans::join("product_barcodes", function($q) {
                    $q->on('location_assign_trans.barcode_id', 'product_barcodes.id');
                });
        $query->select('location_assign_trans.*');
        $query->where('location_assign_trans.id', $params['location_transaction_id']);
        return $query->first();
        //$query->where('product_barcodes.barcode', $params['barcode']);
        //$query->where('location_assign_trans.loc_ass_id', $params['loc_ass_id']);
//        $query->where('case_type', $params['case_type']);
//        if (empty($params['best_before_date']))
//            $query->whereNull('location_assign_trans.best_before_date');
//        else
//            $query->where('location_assign_trans.best_before_date', date('Y-m-d', strtotime($params['best_before_date'])));
//
    }

    /**
     * @author Hitesh tAnk
     * @param type $params
     * @return type
     */
    public static
            function getPutAwayProductCaseDetail($params) {

        $query = LocationAssignTrans::where('loc_ass_id', $params['locationData']->id)->where('case_type', $params['case_type']);

        if (!empty($params['total_boxes'])) {
            $query->where('total_boxes', $params['total_boxes']);
        }
        if (!empty($params['qty_box'])) {
            $query->where('qty_per_box', $params['qty_box']);
        }

        if (empty($params['best_before_date'])) {
            $query->whereNull('best_before_date');
        }
        else {
            $query->where('best_before_date', date('Y-m-d', strtotime($params['best_before_date'])));
        }

        $query->where('barcode_id', $params['barcode_product_detail_id']);

        return $query->first();
    }

    public
            function locationAssign() {
        return $this->hasMany(LocationAssign::class, 'location_id')->orderBy('created_at', 'desc');
    }

    public
            function getLocationTypeAttribute() {

        return !empty($this->type_of_location) ? LocationType($this->type_of_location) : "";
    }

    public static
            function getLocationWithPrefix($location_id = '') {
        $locationObj = self::select('locations_master.id', 'locations_master.aisle', 'locations_master.rack', 'locations_master.floor', 'locations_master.box', 'locations_master.type_of_location', 'warehouse_loc_type_prefix.prefix');
        $locationObj->leftJoin("warehouse_loc_type_prefix", function($q) {
            $q->on('warehouse_loc_type_prefix.location_type', 'locations_master.type_of_location');
            $q->on('warehouse_loc_type_prefix.warehouse_id', 'locations_master.site_id');
        });
        $locationObj->groupBy('locations_master.id');

        if (!empty($location_id)) {
            $locationObj->where('locations_master.id', $location_id);
        }

        return $locationObj->get();
    }

    public static
            function getPhotoBoothLocation($warehouse_id = "") {
        if (!empty($warehouse_id)) {
            $self = self::select('*');
            $self->where('site_id', $warehouse_id);
            $self->where('type_of_location', '11');
            $self->orderBy('id', 'desc');
            return $self->first()->toArray();
        }
        else {
            return array();
        }
    }

}
