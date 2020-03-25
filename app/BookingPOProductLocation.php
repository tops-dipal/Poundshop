<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPOProductLocation extends Model {

    protected
            $table   = "booking_po_product_locations";
    protected
            $guarded = [];

    public
            function locationDetails() {
        return $this->belongsTo(Locations::class, "location_id");
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @param type $perPage
     * @return type
     */
    public
            function getPutAway($params, $perPage = 10) {

        $query = $this->select(["locations_master.site_id as warehouse_id", "po_products.supplier_sku", "products.title", "products.sku", "products.main_image_internal_thumb", "booking_po_products.status as pending_products", "booking_po_products.product_id",
            "booking_po_products.booking_id", "booking_po_products.barcode", "booking_po_products.po_id",
            "booking_po_product_case_details.id", "locations_master.location", "locations_master.id"]);
        $query->selectRaw("(sum(booking_po_product_locations.qty) - sum(booking_po_product_locations.put_away_qty)) as pending_qty");
        $query->join('locations_master', function($q) {
            $q->on('booking_po_product_locations.location_id', 'locations_master.id');
        });
        $query->leftJoin('booking_po_product_case_details', function($q) {
            $q->on('booking_po_product_locations.case_detail_id', 'booking_po_product_case_details.id');
        });
        $query->leftJoin('booking_po_products', function($q) {
            $q->on('booking_po_product_case_details.booking_po_product_id', 'booking_po_products.id');
        });

        $query->leftJoin('po_products', function($q) {
            $q->on('booking_po_products.po_product_id', 'po_products.id');
        });

        $query->leftJoin('products', function($q) {
            $q->on('booking_po_products.product_id', 'products.id');
        });
        $query->where('locations_master.location', $params['location']);
        $query->whereIn('locations_master.type_of_location', [3, 4]); // pick/bulk or putaway location only


        if (isset($params['productSearch']) && !empty($params['productSearch'])) {
            $searchString = trim($params['productSearch']);
            $query->where(function($q) use($searchString) {
                $q->where('booking_po_products.barcode', '=', $searchString);
                $q->orWhere('booking_po_product_case_details.barcode', '=', $searchString);
                $q->orWhere('products.title', '=', $searchString);
                $q->orWhere('products.sku', '=', $searchString);
                $q->orWhere('po_products.supplier_sku', '=', $searchString);
            });
        }

        $query->groupBy('booking_po_products.product_id');
        $query->havingRaw('pending_qty <> ?', [0]);
        if ($params['sortBy'] == "title")
            $query->orderBy("products.title", $params['sortDirection']);

        if ($params['sortBy'] == "qty")
            $query->orderBy("pending_qty", $params['sortDirection']);

        if ($params['sortBy'] == "goods-in")
            $query->orderBy('booking_po_product_locations.created_at', $params['sortDirection']);
        return $query->get();
    }

}
