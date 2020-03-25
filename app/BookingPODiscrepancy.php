<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPODiscrepancy extends Model {

    protected
            $table   = "booking_purchase_orders_discrepancy";
    protected
            $guarded = [];

    public
            function bookingProduct() {
        return $this->belongsTo(BookingPOProducts::class, 'booking_po_products_id');
    }

    function get_product_desc_data($po_product_id) {
        $self_object = self::select('booking_purchase_orders_discrepancy.id', 'booking_purchase_orders_discrepancy.qty', 'booking_purchase_orders_discrepancy.discrepancy_type');
        $self_object->selectRaw('GROUP_CONCAT(booking_purchase_orders_discrepancy_image.id SEPARATOR "||") as desc_image_id');
        $self_object->selectRaw('GROUP_CONCAT(booking_purchase_orders_discrepancy_image.image SEPARATOR "||") as desc_image_url');
        $self_object->leftJoin('booking_purchase_orders_discrepancy_image', function($join) {
            $join->on('booking_purchase_orders_discrepancy_image.book_pur_desc_id', '=', 'booking_purchase_orders_discrepancy.id');
        });

        $self_object->where('booking_purchase_orders_discrepancy.booking_po_products_id', (int) $po_product_id);

        $self_object->groupBy('booking_purchase_orders_discrepancy.id');
        $result = $self_object->get();
        return $result;
    }

    function get_product_desc_image_data($bookingProductsIds) {
        $self_object = self::select('booking_purchase_orders_discrepancy.*');
        $self_object->selectRaw('GROUP_CONCAT(booking_purchase_orders_discrepancy_image.image SEPARATOR "||") as desc_image_url');
        $self_object->leftJoin('booking_purchase_orders_discrepancy_image', function($join) {
            $join->on('booking_purchase_orders_discrepancy_image.book_pur_desc_id', '=', 'booking_purchase_orders_discrepancy.id');
        });
        $self_object->whereIn('booking_purchase_orders_discrepancy.booking_po_products_id', $bookingProductsIds);
        $self_object->groupBy('booking_purchase_orders_discrepancy.id');
        $result = $self_object->get();
        return $result;
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @desc : update the row
     */
    public
            function updateDiscrepancy($params) {
        self::where('status', 0)->where('id', $params['discrepancy_id'])->update(['status' => $params['status'], 'modified_by' => $params['user']->id, 'updated_at' => \Carbon\Carbon::now()]);
    }

    public function DiscrepancyImages(){
        return $this->hasMany(BookingPurchaseOrdersDiscrepancyImage::class, "book_pur_desc_id");
    }
}
