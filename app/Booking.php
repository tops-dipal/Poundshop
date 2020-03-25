<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\BookingPO;
use App\SupplierMaster;
use App\Slot;

class Booking extends Model {

    protected
            $table   = "bookings";
    protected
            $guarded = [];
    protected
            $dates   = ['book_date', 'created_at', 'updated_at'];
    public
            $bookObj    = null;

    public function _construct() {
        $this->bookObj = new self;
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public function getBookDateAttribute() 
    {
        return !empty($this->attributes['book_date']) ? date('d-M-Y', strtotime($this->attributes['book_date'])) : '';
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public function setBookDateAttribute($value) {
        $this->attributes['book_date'] = !empty($value) ? date('Y-m-d', strtotime($value)) : null;
    }

    /**
     * @author Hitesh Tank
     * @return type
     * hasmany with booking po
     */
    public function bookingPOs() {
        return $this->hasMany(BookingPO::class, "booking_id");
    }

    /**
     * @author Hitesh tank
     * @return type
     * hasmany relationship with bookin products
     */
    public function bookingProducts() {
        return $this->hasMany(BookingPOProducts::class, 'booking_id');
    }

    /**
     * @author Hitesh Tank
     * @return type
     * has belongs supplier with deleted
     */
    public function supplier() {
        return $this->belongsTo(SupplierMaster::class, 'supplier_id')->withDefault();
    }

    /**
     * @author Hitesh Tank
     * @return type
     * has belongs slot with deleted
     */
    public function slot() {
        return $this->belongsTo(Slot::class, 'slot_id')->withDefault();
    }

    public function bookingPallets() {
        return $this->hasMany('App\BookingPallet', 'booking_id');
    }

    /**
     * @author Hitesh Tank
     * @return type
     */
    public function hasPurchaseOrder() {
        return $this->belongsToMany(PurchaseOrder::class, "booking_purchase_orders", "booking_id");
    }

    /**
     * @author Hitesh Tank
     * @return string
     */
    public static function getAutoGenerateBookingRefID() {
        $booking = self::select('booking_ref_id')->orderBy('id', 'desc')->first();
        if (!empty($booking)) {
            $expNum = explode('-', $booking->booking_ref_id);
            return 'BR' . '-' . sprintf("%09d", $expNum[1] + 1);
        }
        else {
            return 'PB-000000001';
        }
    }

    public function getDeliveryNotesPictureAttribute() {
        if (!empty($this->attributes['delivery_notes_picture']))
            return url('/storage/uploads') . '/' . $this->attributes['delivery_notes_picture'];
        else
            return url('/img/no-img-black.png');
    }

    public static function getBookings($perPage = '', $params = array()) {
        $ShortDatedConfigValue = Setting::getData(["po_bookings"]);

        if (isset($ShortDatedConfigValue) && !empty($ShortDatedConfigValue)) {
            $ShortDatedConfigValue = $ShortDatedConfigValue[0]->column_val;
        }
        else {
            $ShortDatedConfigValue = 0;
        }
        $object = self::select('bookings.*');
        $object->selectRaw('count(distinct bookings.id) as total_bookings');
        $object->selectRaw('SUM(distinct bookings.num_of_pallets) as total_pallets');
        $object->selectRaw('SUM(bookings.total_qty_received) as total_qty_received');
        $object->selectRaw('SUM(bookings.total_new_products) as total_new_products');
        $object->selectRaw('SUM(bookings.total_over_qty) as total_over_qty');
        $object->selectRaw('SUM(bookings.total_short_qty) as total_short_qty');
        $object->selectRaw('SUM(bookings.total_value_received) as total_value_received');
        $object->selectRaw('COUNT(DISTINCT CASE WHEN products.is_essential = 1 THEN products.id END) AS total_essential');
        $object->selectRaw('COUNT(DISTINCT CASE WHEN products.is_seasonal = 1 THEN products.id END) AS total_seasonal');
        $object->selectRaw('COUNT(DISTINCT CASE WHEN products.is_promotional = 1 THEN products.id END) AS total_promotion');
        $object->selectRaw('COUNT(DISTINCT(products.sku)) as total_skus');
        $object->selectRaw('SUM(distinct purchase_order_master.sub_total) as total_po_value'); //having issue
        $object->selectRaw('SUM(po_products.total_quantity) as total_product_qty');
        $object->selectRaw('COUNT(DISTINCT CASE WHEN po_products.is_variant = 1 THEN po_products.product_id END) AS total_variants_val');
        $object->selectRaw('SUM(IF(po_products.best_before_date IS NOT NULL,IF(ABS(DATEDIFF(po_products.best_before_date, CURDATE())) <= "' . $ShortDatedConfigValue . '",1,0),0)) as short_dated');

        $object->selectRaw('COUNT(DISTINCT CASE WHEN purchase_order_master.is_drop_shipping = 1 THEN po_products.product_id END) AS total_dropshipping');
        if (empty($params['book_date'])) {

            $object->where('bookings.book_date', '>=', Carbon::now()->startOfWeek())->where('bookings.book_date', '<=', Carbon::now()->endOfWeek());
        }
        else {
            $week = get_week_num($params['book_date']);

            $result = x_week_range(date('Y-m-d', strtotime($params['book_date'])));
            $object->whereBetween('bookings.book_date', [$result['start_date'], $result['end_date']]);
        }
        $object->leftJoin('booking_purchase_orders', 'booking_purchase_orders.booking_id', '=', 'bookings.id');
        $object->leftJoin('purchase_order_master', 'purchase_order_master.id', '=', 'booking_purchase_orders.po_id');
        $object->leftJoin('po_products', 'po_products.po_id', '=', 'purchase_order_master.id');
        $object->leftJoin('products', 'products.id', '=', 'po_products.product_id');
        $object->join('supplier_master', 'supplier_master.id', '=', 'bookings.supplier_id');
        if (!empty($params['search'])) {
            $object->where(function($q) use($params) {
                $q->where('bookings.booking_ref_id', 'like', "%" . $params['search'] . "%");
                $q->orWhere('supplier_master.name', 'like', "%" . $params['search'] . "%");
                $q->orWhere('purchase_order_master.po_number', 'like', "%" . $params['search'] . "%");
            });
        }
        if (!empty($params['advance_search'])) {
            $advance_search_data = $params['advance_search'];

            if (!empty($advance_search_data['booking_status'])) {
                $object->whereIn('bookings.status', $advance_search_data['booking_status']);
            }
        }
        $object->groupBy('bookings.book_date');
        if(empty($params['order_column']))
        {
            $object->orderBy('book_date','DESC');
        }
        else
        {
            $object->orderBy($params['order_column'], $params['order_dir']);
        }
     
        //   dd($object->toSql());

        return $object->paginate($perPage);
    }

    public static function getBookingsDaywise($perPage = '', $params = array(), $time = '') {
        $object = self::select('bookings.*', 'slots.from as slot_from', 'slots.to as slot_to', 'supplier_master.name as supplier_name','supplier_master.id as supplier_id');
        $object->selectRaw('GROUP_CONCAT(DISTINCT(purchase_order_master.po_number) SEPARATOR "<br/>") as po_list');
        $object->selectRaw('SUM(po_products.total_quantity) as total_product_qty');
        //$object->selectRaw('SUM(po_products.is_variant) as total_variants_val');
        $object->selectRaw('COUNT(DISTINCT CASE WHEN po_products.is_variant = 1 THEN po_products.product_id END) AS total_variants_val');
        $object->selectRaw('SUM(purchase_order_master.sub_total) as total_po_value'); //having issue
        $object->selectRaw('COUNT(DISTINCT(products.sku)) as total_skus');
        // $object->selectRaw('SUM(case products.is_essential when 1 then 1 else 0 end) as total_essential');
        // $object->selectRaw('SUM(case products.is_seasonal when 1 then 1 else 0 end) as total_seasonal');
        $object->selectRaw('COUNT(DISTINCT CASE WHEN products.is_essential = 1 THEN products.id END) AS total_essential');
        $object->selectRaw('COUNT(DISTINCT CASE WHEN products.is_seasonal = 1 THEN products.id END) AS total_seasonal');

        $object->where('book_date', $params['book_date']);

        if (!empty($time)) {
            if ($time == 1) { //AM Data only
                $object->where('slots.from', '>=', '00:00:00');
                $object->where('slots.from', '<=', '11:59:59');
            }
            else {
                //Pm Data only
                $object->where('slots.from', '>=', '12:00:00');
                $object->where('slots.from', '<=', '23:59:59');
            }
        }

        //if search is applicable
        if (!empty($params['search'])) {
            $object->where(function($q) use($params) {
                $q->where('bookings.booking_ref_id', 'like', "%" . $params['search'] . "%");
                $q->orWhere('supplier_master.name', 'like', "%" . $params['search'] . "%");
                $q->orWhere('purchase_order_master.po_number', 'like', "%" . $params['search'] . "%");
            });
        }

        if (!empty($params['advance_search'])) {
            $advance_search_data = $params['advance_search'];

            if (!empty($advance_search_data['booking_status'])) {
                $object->whereIn('bookings.status', $advance_search_data['booking_status']);
            }
        }

        $object->Join('slots', 'slots.id', '=', 'bookings.slot_id');
        $object->Join('supplier_master', 'supplier_master.id', '=', 'bookings.supplier_id');
        $object->LeftJoin('booking_purchase_orders', 'booking_purchase_orders.booking_id', '=', 'bookings.id');
        $object->LeftJoin('purchase_order_master', 'purchase_order_master.id', '=', 'booking_purchase_orders.po_id');
        $object->LeftJoin('po_products', 'po_products.po_id', '=', 'purchase_order_master.id');
        $object->LeftJoin('products', 'products.id', '=', 'po_products.product_id');
        $object->groupBy('bookings.id');
        $object->orderBy($params['order_column'], $params['order_dir']);
        //dd($object->toSql());
        if (isset($perPage) && !empty($perPage) && $perPage != '-1') {
            return $object->paginate($perPage);
        }
        else {
            return $object->get();
        }
    }

    public function supplierDetails() {
        return $this->belongsTo(SupplierMaster::class, 'supplier_id');
    }

    /**
     * @author Hitesh Tank
     * @param type $perPage
     * @param type $params
     * @return type
     */
    public static function getBookingPOs($perPage, $params = []) {
        $ShortDatedConfigValue = Setting::getData(["po_bookings"]);

        if (isset($ShortDatedConfigValue) && !empty($ShortDatedConfigValue)) {
            $ShortDatedConfigValue = $ShortDatedConfigValue[0]->column_val;
        }
        else {
            $ShortDatedConfigValue = 0;
        }

        $query = self::join("booking_purchase_orders", function($q) {
                    $q->on('bookings.id', 'booking_purchase_orders.booking_id');
                });
        $query->join("purchase_order_master", function($q) {
            $q->on('booking_purchase_orders.po_id', 'purchase_order_master.id');
        });
        $query->leftJoin("po_products", function($q) {
                    $q->on('purchase_order_master.id', 'po_products.po_id');
                })
                ->leftJoin('products as prod', function($q) {
                    $q->on('po_products.product_id', 'prod.id');
                });
        $query->where('bookings.id', $params['booking_id']);
        $query->select(['purchase_order_master.id', 'purchase_order_master.is_drop_shipping', 'purchase_order_master.is_outstanding_po', 'po_number', 'supplier_order_number',
            'exp_deli_date', 'sub_total', 'po_status', 'booking_purchase_orders.id as selected_booking_po_id',
            DB::raw("count(po_products.po_id) as total_skus"),
            DB::raw("SUM(IF(po_products.is_variant=1,1,0)) as total_variant"),
            DB::raw("sum(po_products.total_quantity) as total_quantity"),
            DB::raw("SUM(IF(prod.is_essential = 1,1,0)) as essential_product"),
            DB::raw('SUM(IF(po_products.best_before_date IS NOT NULL,IF(ABS(DATEDIFF(po_products.best_before_date, CURDATE())) <= "' . $ShortDatedConfigValue . '",1,0),0)) as short_dated'),
            DB::raw("SUM(IF(prod.is_seasonal = 1,1,0)) as seasonal_product"), "purchase_order_master.status"]);
        $query->groupBy('purchase_order_master.id');
        $query->orderBy($params['order_column'], $params['order_dir']);
        return $query->get();
    }

    public function supplier_contact_data($booking_id) {
        $object = self::select('bookings.id', 'supplier_contact.name', 'supplier_contact.email');
        $object->where('bookings.id', $booking_id);
        $object->LeftJoin('booking_purchase_orders', 'booking_purchase_orders.booking_id', '=', 'bookings.id');
        $object->LeftJoin('purchase_order_master', 'purchase_order_master.id', '=', 'booking_purchase_orders.po_id');
        $object->LeftJoin('supplier_contact', 'supplier_contact.id', '=', 'purchase_order_master.supplier_contact');
        $object->groupBy('supplier_contact.id');
        return $object->get();
    }

    public function wareHouseDetails() {
        return $this->belongsTo(WareHouse::class, 'warehouse_id');
    }

    public function getWeekDetail($params = []) {
        $bookingObj = self::select('bookings.id as booking_id', 'bookings.book_date as book_date');
        $this->selectColumn($bookingObj);
        $this->bookingPurchaseOrder($bookingObj, 'leftJoin');
        $this->purchaseOrder($bookingObj, 'leftJoin');
        $this->purchaseOrderProducts($bookingObj, 'leftJoin');
        $this->products($bookingObj, 'leftJoin');
        $this->whereDateBetween($bookingObj, $params);
        $this->groupBy($bookingObj, 'bookings.book_date');
        $this->orderByBook($bookingObj, $params);
        return $bookingObj->get();
    }

    /**
     * join purpose
     * @author Hitesh Tank
     * @param type $bookingObj
     * @param type $join
     */
    private function bookingPurchaseOrder(&$bookingObj, $join) {
        $bookingObj->{$join}('booking_purchase_orders', function($q) {
            $q->on("bookings.id", "booking_purchase_orders.booking_id");
        });
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     * @param type $join
     */
    private function purchaseOrder(&$bookingObj, $join) {
        $bookingObj->{$join}('purchase_order_master', function($q) {
            $q->on("booking_purchase_orders.po_id", "purchase_order_master.id");
        });
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     * @param type $join
     */
    private function purchaseOrderProducts(&$bookingObj, $join) {
        $bookingObj->{$join}('po_products', function($q) {
            $q->on("purchase_order_master.id", "po_products.po_id");
        });
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     * @param type $join
     */
    private function products(&$bookingObj, $join) {
        $bookingObj->{$join}('products', function($q) {
            $q->on("po_products.product_id", "products.id");
        });
    }

    /**
     * @author Hitesh Tank
     * group by condition
     * @param Mixed $params
     * @param Query Object reference $bookingObj
     */
    private function groupBy(&$bookingObj, $columnName) {
        $bookingObj->groupBy($columnName);
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     * @param type $params
     */
    private function whereDateBetween(&$bookingObj, $params) {
        if (!empty($params['book_date'])) {
            $xDates = x_week_range(date('Y-m-d', strtotime($params['book_date']))); // start week date
            $bookingObj->where('bookings.book_date', '>=', $xDates['start_date'])->where('bookings.book_date', '<=', $xDates['end_date']);
        }
        else {
            $bookingObj->where('bookings.book_date', '>=', Carbon::now()->startOfWeek())->where('bookings.book_date', '<=', Carbon::now()->endOfWeek());
        }
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     * @param type $params
     */
    private function orderByBook(&$bookingObj, $params) {
        $bookingObj->orderBy($params['order_column'], $params['order_dir']);
    }

    private function selectColumn(&$bookingObj) 
    {
        $ShortDatedConfigValue = Setting::getData(["po_bookings"]);
        if (isset($ShortDatedConfigValue) && !empty($ShortDatedConfigValue)) {
            $ShortDatedConfigValue = $ShortDatedConfigValue[0]->column_val;
        }
        else {
            $ShortDatedConfigValue = 0;
        }
        // $this->weekDate($bookingObj);
        $this->noOfBooking($bookingObj);
        $this->noOfPallets($bookingObj);
        $this->noOfSkus($bookingObj);
        $this->noOfVariants($bookingObj);
        $this->noOfEssentialProducts($bookingObj);
        $this->noOfSeasonalProducts($bookingObj);
        $this->noOfShortDatedProducts($bookingObj, $ShortDatedConfigValue);
        $this->noOfTotalQuantity($bookingObj);
        $this->totalPurchaseOrderValue($bookingObj);
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function weekDate(&$bookingObj) {
        $bookingObj->selectRaw('DATE_FORMAT(bookings.week_date, "%W %d-%M-%Y")  as weekDate');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function noOfEssentialProducts(&$bookingObj) {
        $bookingObj->selectRaw('SUM(DISTINCT CASE WHEN products.is_essential = 1 THEN 1 ELSE 0 END) AS noOfEssentialProducts');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function noOfSeasonalProducts(&$bookingObj) {
        $bookingObj->selectRaw('SUM(DISTINCT CASE WHEN products.is_seasonal = 1 THEN 1 ELSE 0 END) AS noOfSeasonalProducts');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     * @param type $shorDateVal
     */
    private function noOfShortDatedProducts(&$bookingObj, $shorDateVal) {
        $bookingObj->selectRaw('SUM(IF(po_products.best_before_date IS NOT NULL,IF(ABS(DATEDIFF(po_products.best_before_date, CURDATE())) <= "' . $shorDateVal . '",1,0),0)) as noOfShortDatedProducts');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function noOfTotalQuantity(&$bookingObj) {
        $bookingObj->selectRaw('IF(SUM(po_products.total_quantity) > 0 ,SUM(po_products.total_quantity),0) as noOfTotalQuantity');
    }

    private function totalPurchaseOrderValue(&$bookingObj) {
        $bookingObj->selectRaw('SUM(purchase_order_master.sub_total) as totalPurchaseOrderValue');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function noOfBooking(&$bookingObj) {
        $bookingObj->selectRaw('count(distinct bookings.id) as noOfBooking');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function noOfPallets(&$bookingObj) {
        $bookingObj->selectRaw('SUM(bookings.num_of_pallets) as noOfPallets');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function noOfSkus(&$bookingObj) {
        $bookingObj->selectRaw('COUNT(DISTINCT(products.sku)) as noOfSkus');
    }

    /**
     * @author Hitesh Tank
     * @param type $bookingObj
     */
    private function noOfVariants(&$bookingObj) {
        $bookingObj->selectRaw('SUM(DISTINCT CASE WHEN po_products.is_variant = 1 THEN 1 ELSE 0 END) AS noOfVariants');
    }

    public function saveDeliveryPic(\App\Http\Requests\Api\Bookings\BookingPORequest $request) {
        if ($request->file('delivery_notes_picture')) {
            $bookingObj = Booking::find($request->booking_id);
            $folder     = 'bookings';
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0777, true);
            }
            $uploadedFile = $request->file('delivery_notes_picture');
            $extension    = strtolower($uploadedFile->getClientOriginalExtension());
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $orientation = exif_read_data($uploadedFile);
            }
            $name = time() . '' . $uploadedFile->getClientOriginalName();
            $path = Storage::putFileAs(('bookings'), $uploadedFile, $name);
            if (!empty($path)) {
                $folder = 'bookings';
                Storage::makeDirectory($folder, 0777, true);
                $folder = 'bookings/thumbnail/';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }

                $thumbName1   = explode('/', $path);
                $thumbName    = $thumbName1[1];
                $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'bookings/' . $thumbName;

                $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'bookings/thumbnail/' . $thumbName;
                //   echo $thumbPath;exit;
                $image     = new ImageResize($originalPath);
                $image->resize(100, 100, true);
                $image->save($thumbPath);

                Storage::delete($bookingObj->delivery_notes_picture);
                if (isset($bookingObj->delivery_notes_picture) && !empty($bookingObj->delivery_notes_picture)) {
                    $thumbName = explode('/', $bookingObj->profile_pic)[1];
                    Storage::delete('bookings/thumbnail/' . $thumbName);
                }
                return $path;
            }
        }
        else {
            return '';
        }
    }

    public static function setComplete($booking_id, $perform_pending_logic = false, $booking_details = array()) 
    {
        $response = array(
                        'status' => true,
                        'msg'    => 'Booking successfully set as complete',
                    );

        if(empty($booking_details))
        {    
            $booking_details = self::find($booking_id);
        }
        
        if(empty($booking_details->delivery_note_number))
        {
            $response = array(
                        'status' => false,
                        'msg'    => 'Please add Delivery Note Number to complete the booking.',
                    );

            return $response;
        }   

        if ($perform_pending_logic == true) {
            
            $pending_products = $booking_details->bookingProducts()
                            ->where('return_to_supplier', '0')
                            ->where('status', '0')
                            ->whereNull('parent_id')
                            ->pluck('id')
                            ->toArray();
            
            if (empty($pending_products)) {
                $perform_pending_logic = false;
            }
        }

        if ($perform_pending_logic == false) 
        {
            $po_ids = $booking_details->bookingPOs->pluck('po_id')->toArray();

            if (!empty($po_ids)) {
                $db_update_po['po_status'] = '9';

                \App\PurchaseOrder::whereIn('id', $po_ids)->update($db_update_po);
            }

            $total_completed_products = !empty($booking_details->total_completed_products) ? $booking_details->total_completed_products : 0;

            $total_products = !empty($booking_details->total_products) ? $booking_details->total_products : 0;

            if (!empty($total_products)) {
                $booking_details->completed = ($total_completed_products / $total_products) * 100;

                $booking_details->completed = $booking_details->completed > 100 ? 100 : $booking_details->completed;
            }
            else {
                $booking_details->completed = 0;
            }

            $booking_details->status = 6;
            
            $booking_details->completed_date = date('Y-m-d H:i:s');

            // CALCULATE VARIABLES
            $products_wise_data          = BookingPO::getBookingProducts($booking_id);
            
            $total_qty_received          = 0;
            
            $total_is_variant            = 0;
            
            $total_value                 = 0;
            
            $booking_po_product_id_array = array();
            
            $total_shortage              = 0;
            
            $total_over                  = 0;
            
            $total_damag_trand           = 0;
            
            $total_new_product           = 0;

            $total_qty_ordered           = 0;
            
            $total_delivery_note_qty     = 0;

            $total_quantity_instock      = 0;

            $total_instock_payable       = 0;

            if (!empty($products_wise_data)) {
                foreach ($products_wise_data as $row) {
                    $booking_po_product_id_array[] = $row->booking_po_product_id;
                    
                    $instock_qty = 0;

                    $total_qty_received            = $total_qty_received + $row->qty_received;
                    
                    $total_qty_ordered             = $total_qty_ordered + $row->total_quantity;

                    $total_delivery_note_qty       = $total_delivery_note_qty + $row->delivery_note_qty;

                    $value_receipt                 = $row->qty_received * $row->unit_price;
                    
                    $total_value                   = $total_value + $value_receipt;
                        
                    $instock_qty                    = $row->pick_pallet_qty + $row->bulk_pallet_qty;
                    
                    if($row->is_photobooth == 1)
                    {
                        $instock_qty = $instock_qty + 1;    
                    }    

                    $total_quantity_instock        = $total_quantity_instock + $instock_qty;

                    $total_instock_payable          = $total_instock_payable + ($row->unit_price * $instock_qty);

                    if (empty($row->is_listed_on_magento)) {
                        $total_new_product = $total_new_product + 1;
                    }

                    if($row->is_variant == 1)
                    {    
                        $total_is_variant          = $total_is_variant + 1;
                    }
                }
            }

            if (!empty($booking_po_product_id_array)) {

                $po_desc_list = BookingPODiscrepancy::whereIn('booking_po_products_id', $booking_po_product_id_array)->get();
                
                if (!empty($po_desc_list)) {
                    foreach ($po_desc_list as $row) {
                        if ($row->discrepancy_type == 1) {
                            $total_shortage = $total_shortage + $row->qty;
                        }
                        else if ($row->discrepancy_type == 2) {
                            $total_over = $total_over + $row->qty;
                        }
                        else if ($row->discrepancy_type == 4 || $row->discrepancy_type == 6) {
                            $total_damag_trand = $total_damag_trand + $row->qty;
                        }
                    }
                }
            }

            $booking_details->total_qty_received = $total_qty_received;
            
            $booking_details->total_value_received   = $total_value;
            
            $booking_details->total_variants         = $total_is_variant;
            
            $booking_details->total_new_products     = $total_new_product;
            
            $booking_details->total_damage_trade_qty = $total_damag_trand;
            
            $booking_details->total_short_qty        = $total_shortage;
            
            $booking_details->total_over_qty         = $total_over;
            
            $booking_details->total_diff_po_note     = $total_qty_ordered - $total_delivery_note_qty;

            $booking_details->total_qty_instock     = $total_quantity_instock;

            $booking_details->total_value_payable     = $total_instock_payable;

            $booking_details->save();
        }

        return $response;
        
    }

    public static function getPutAway($perPage = '', $params = array()) 
    {
        $ShortDatedConfigValue = Setting::getData(["po_bookings"]);

        if (isset($ShortDatedConfigValue) && !empty($ShortDatedConfigValue)) 
        {
            $ShortDatedConfigValue = $ShortDatedConfigValue[0]->column_val;
        }
        else 
        {
            $ShortDatedConfigValue = 0;
        }

        $object = self::select('bookings.id', 'bookings.booking_ref_id', 'bookings.completed', 'bookings.status', 'supplier_master.name as supplier_name');

        $object->selectRaw('COUNT(DISTINCT CASE WHEN products.is_promotional = 1 THEN products.id END) AS total_promotion');
        
        $object->selectRaw('COUNT(DISTINCT CASE WHEN products.is_seasonal = 1 THEN products.id END) AS total_seasonal');
        
        $object->selectRaw('COUNT(DISTINCT CASE WHEN lm.type_of_location = 3 THEN lm.id END) as total_pick_put_away');
        
        $object->selectRaw('COUNT(DISTINCT CASE WHEN lm.type_of_location = 4 THEN lm.id END) as total_bulk_put_away');
        
        $object->selectRaw('COUNT(DISTINCT CASE WHEN lm.type_of_location = 3 THEN booking_po_products.product_id END) as total_pallet_pick_skus');
        
        $object->selectRaw('COUNT(DISTINCT CASE WHEN lm.type_of_location = 4 THEN booking_po_products.product_id END) as total_pallet_bulk_skus');
        
        $object->selectRaw('COUNT(DISTINCT CASE WHEN purchase_order_master.is_drop_shipping = 1 THEN booking_po_products.product_id END) as total_dropshipping_products');
        
        $object->selectRaw('COUNT(
                                DISTINCT (
                                    CASE 
                                        WHEN (booking_po_products.is_best_before_date = 1)
                                            AND (booking_po_product_locations.best_before_date IS NOT NULL)
                                            AND (Abs(Datediff(booking_po_product_locations.best_before_date, Curdate())) <= '.$ShortDatedConfigValue.')
                                        THEN 
                                            booking_po_products.product_id
                                        ELSE
                                            NULL
                                    END
                                )
                            ) 
                            as short_dated');

        $object->selectRaw('COUNT(DISTINCT CASE WHEN locations_assign.id IS NULL THEN booking_po_products.product_id END) as total_without_pick_products');
        
        $object->Join('supplier_master', 'supplier_master.id', '=', 'bookings.supplier_id');
        
        $object->LeftJoin('booking_purchase_orders', 'booking_purchase_orders.booking_id', '=', 'bookings.id');
        
        $object->LeftJoin('purchase_order_master', 'purchase_order_master.id', '=', 'booking_purchase_orders.po_id');
        
        // $object->LeftJoin('po_products', 'po_products.po_id', '=', 'purchase_order_master.id');
        
        $object->leftJoin('booking_po_products', 'booking_po_products.po_id', '=', 'purchase_order_master.id');

        $object->LeftJoin('products', 'products.id', '=', 'booking_po_products.product_id');        
        
        $object->LeftJoin('locations_assign', function($join){
            $join->on('locations_assign.product_id','=', 'products.id');
            // $join->whereIn('locations_assign.location_id',array(1,2,5,6,7));
        });  
        
        $object->LeftJoin('booking_po_product_case_details', 'booking_po_product_case_details.booking_po_product_id', '=', 'booking_po_products.id');

         $object->LeftJoin('booking_po_product_locations', 'booking_po_product_case_details.id', '=', 'booking_po_product_locations.case_detail_id');

        $object->LeftJoin('locations_master as lm', 'lm.id', '=', 'booking_po_product_locations.location_id');

        $object->whereColumn('booking_po_product_locations.qty','!=','booking_po_product_locations.put_away_qty');
        
        $object->whereIn('lm.type_of_location',array(3,4));

        //if search is applicable
        if (!empty($params['search'])) 
        {
            $object->where(function($q) use($params) 
            {
                $q->where('bookings.booking_ref_id', 'like', "%" . $params['search'] . "%");
                $q->orWhere('supplier_master.name', 'like', "%" . $params['search'] . "%");
                $q->orWhere('purchase_order_master.po_number', 'like', "%" . $params['search'] . "%");
            });
        }   

         //if advance_search is applicable
        if (!empty($params['advance_search'])) 
        {
            if(!empty($params['advance_search']['warehouse_id']))
            {
                $object->where('bookings.warehouse_id', $params['advance_search']['warehouse_id']);   
            }    
        }    

        $object->groupBy('bookings.id');

        $object->orderBy($params['order_column'], $params['order_dir']);
        
        if (isset($perPage) && !empty($perPage) && $perPage != '-1') 
        {
            return $object->paginate($perPage);
        }
        else 
        {
            return $object->get();
        }
    }



    public static function excessQtyReport($perPage = '', $params = array(), $global_result = false) 
    {
        $qty_case_select = '(CASE
                                WHEN booking_po_products.is_photobooth = 1
                                    THEN (booking_po_products.pick_pallet_qty + booking_po_products.bulk_pallet_qty + 1) - po_products.total_quantity
                                ELSE    
                                    (booking_po_products.pick_pallet_qty + booking_po_products.bulk_pallet_qty) - po_products.total_quantity
                                END
                            )';

        $self_obj = self::selectRaw('
                            0 as confirmed_with_supplier,
                            bookings.start_date,
                            bookings.id as booking_id,
                            bookings.completed_date,
                            bookings.booking_ref_id,
                            supplier_master.name as supplier_name,
                            COUNT(DISTINCT booking_po_products.product_id) as sku_count,
                            SUM('.$qty_case_select.') as quantity,
                            SUM('.$qty_case_select.' * po_products.unit_price) as value
                        ');

        $self_obj->join('supplier_master', 'supplier_master.id', '=', 'bookings.supplier_id');

        $self_obj->join('booking_po_products', 'booking_po_products.booking_id', '=', 'bookings.id');

        $self_obj->join('po_products','booking_po_products.po_product_id','=','po_products.id');

        $self_obj->whereRaw('po_products.total_quantity < (CASE
                            WHEN booking_po_products.is_photobooth = 1
                                THEN booking_po_products.pick_pallet_qty + booking_po_products.bulk_pallet_qty + 1
                            ELSE    
                                booking_po_products.pick_pallet_qty + booking_po_products.bulk_pallet_qty
                            END
                        )');
        
        if(!empty($params['search']))
        {
            $self_obj->join('purchase_order_master','purchase_order_master.id','=','po_products.po_id');  

            $self_obj->where(function($query) use ($params){
                $query->where('bookings.booking_ref_id', $params['search']);
                $query->orWhere('purchase_order_master.po_number', $params['search']);
                $query->orWhere('supplier_master.name', 'like', "%" . $params['search'] . "%");
            });    
        }

        if(!empty($params['advanceSearch']['filter_confirm_with_supplier']) && false)
        {
            $self_obj->where('confirmed_with_supplier', $params['advanceSearch']['filter_confirm_with_supplier']);   
        } 

        if(!empty($params['advanceSearch']['filter_with_date_from']))
        {
            $self_obj->whereDate('bookings.start_date','>=', db_date($params['advanceSearch']['filter_with_date_from']));
        }

        if(!empty($params['advanceSearch']['filter_with_date_to']))
        {
            $self_obj->whereDate('bookings.start_date','<=', db_date($params['advanceSearch']['filter_with_date_to']));
        }    

        if($global_result === false)
        {    
            $self_obj->groupBy('bookings.id');

            if(empty($params['order_column']))
            {
                $self_obj->orderBy('start_date','DESC');
            }
            else
            {
                $self_obj->orderBy($params['order_column'], $params['order_dir']);
            }
            
            return $self_obj->paginate($perPage);
        }    
        else
        {
            return $self_obj->first();
        }    
    }    
}