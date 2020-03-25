<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

//Authentication API
Route::post('login', 'Api\AuthController@appLogin')->name('user.login');
//Refresh token
Route::post('refresh-token', 'Api\UsersController@refreshToken');
Route::post('forgot-password', 'Api\ForgotpasswordController@sendResetLinkEmail')->name('user.forgot-password');

//Magento Apis Url

Route::resource('api-magento-category', 'Api\MagentoCateController');

Route::resource('api-magento-product', 'Api\MagentoProController');

Route::resource('api-magento-product-detail', 'Api\MagentoProDetailController');

Route::resource('api-magento-product-detail-upd', 'Api\MagentoProDetailUpdController');

Route::resource('api-magento-product-qty-update', 'Api\MagentoQtyUpdController');

Route::resource('api-magento-product-price-update', 'Api\MagentoPriceUpdController');

Route::resource('api-magento-product-delete', 'Api\MagentoProductDelController');

Route::resource('api-magento-product-post', 'Api\MagentoProPostController');

Route::resource('api-magento-product-revise', 'Api\MagentoProReviseController');

Route::resource('api-magento-product-merge', 'Api\ProductMergeController');

Route::resource('api-magento-product-enable', 'Api\MagentoEnabledUpdController');

Route::get('api-replan-process/{id}', 'Api\ReplenCronController@processReplan');

Route::get('api-replan-process', 'Api\ReplenCronController@processReplan');


Route::group(['middleware' => ['auth:api']], function() {
    // common
    Route::get('api-common-find', 'Api\CommonApiController@find');
    Route::get('api-common-get', 'Api\CommonApiController@get');
    Route::get('api-common-helper-function', 'Api\CommonApiController@helperFunction');
    Route::get('api-common-param-variables', 'Api\CommonApiController@paramVariables');

    // User Role & permissions
    Route::get('permissions-list', 'Api\RoleController@permissionList');
    Route::delete('delete-user-role', 'Api\RoleController@deleteUserRole');

    // Cartons
    Route::resource('api-cartons', 'Api\CartonsController');
    Route::post('api-cartons-remove-multiple', 'Api\CartonsController@destroyMany');

    // totes
    Route::resource('api-totes', 'Api\TotesController');
    Route::post('api-totes-remove/{id}', 'Api\TotesController@destroy');
    Route::post('api-totes-remove-multiple', 'Api\TotesController@destroyMany');

    // Suppliers
    Route::resource('api-supplier', 'Api\SupplierController')->except('destory');
    Route::post('api-supplier-remove', 'Api\SupplierController@destroy');
    Route::post('api-supplier-email', 'Api\SupplierController@SendEmail');
    Route::get('api-supplier-contacts', 'Api\SupplierController@supplierContacts');

    Route::post('api-supplier-save-general-info', 'Api\SupplierController@save_general_info');
    Route::post('api-supplier-set-default-contact', 'Api\SupplierController@save_default_contacts');
    Route::post('api-supplier-save-contacts', 'Api\SupplierController@save_contacts');
    Route::post('api-supplier-destory-contacts', 'Api\SupplierController@destory_contacts');
    Route::post('api-supplier-save-payment-info', 'Api\SupplierController@save_payment_info');
    Route::post('api-supplier-save-terms-condition', 'Api\SupplierController@save_terms_and_condition');

    // Pallets
    Route::resource('api-pallets', 'Api\PalletsController');
    Route::post('api-pallets-remove/{id}', 'Api\PalletsController@destroy');
    Route::post('api-pallets-remove-multiple', 'Api\PalletsController@destroyMany');

    // Users
    Route::resource('api-users', 'Api\UsersController');
    Route::post('api-users-remove/{id}', 'Api\UsersController@destroy');
    Route::post('api-users-remove-multiple', 'Api\UsersController@destroyMany');
    Route::post('api-users-remove-image', 'Api\UsersController@removeImage');
    Route::post('api-attachment-delete/{id}', 'Api\UsersController@deleteAttachments');

    //Purchase Orders
    Route::resource('api-purchase-orders', 'Api\PurchaseOrderController');
    Route::post('api-purchase-orders/update-terms', 'Api\PurchaseOrderController@updateTerms')->name('api-purchase-orders.update-terms');
    Route::post('api-purchase-orders-remove-multiple', 'Api\PurchaseOrderController@destroyMany');
    Route::post('api-purchase-orders-item-save', 'Api\PurchaseOrderController@poItemSave');
    Route::post('api-purchase-orders-item-remove', 'Api\PurchaseOrderController@destroyItem')->name('purchase-order.item-delet');
    Route::post('api-purchase-orders-item-remove-multiple', 'Api\PurchaseOrderController@destroyItemMany')->name('purchase-order.item-deletemany');
    Route::get('api-purchase-order-download-pdf', 'Api\PurchaseOrderController@downloadPO')->name('purchase-order.download-pdf');
    Route::post('api-purchase-order-send-email-pdf', 'Api\PurchaseOrderController@sendPO')->name('purchase-order.email-pdf');
    Route::post('api-purchase-order-recalculate-items', 'Api\PurchaseOrderController@reCalculateItems')->name('purchase-order.re-calculate-items');
    Route::resource('api-purchase-orders-revise', 'Api\PurchaseOrderRevisesController');

    //Purchase Order Move To New Po
    Route::post('api-move-product-to-existing-po', 'Api\PurchaseOrderController@moveProductToExistingPo')->name('api-move-po-product-to-existing-po');

    Route::post('api-move-product-to-new-po', 'Api\PurchaseOrderController@moveProductToNewPO')->name('api-move-po-product-to-new-po');

    //Purchase order delivery module

    Route::get('delivery/deliver-filters', 'Api\PurchaseOrderController@deliveryFilters')->name('delivery.delivery-filters');
    Route::get('delivery/keep-return-to-supplier-detail', 'Api\PurchaseOrderController@productLocationDetail')->name('delivery.product-location-detail');
    Route::post('delivery/update-keep-return-supplier', 'Api\PurchaseOrderController@updateProductDiscrepancy')->name('delivery.updatediscrepancy');
    Route::post('api-po/delivery-debitenote', 'Api\PurchaseOrderController@debitNote')->name('delivery.debite-note');
    Route::post('api-po/delivery-return-supplier', 'Api\PurchaseOrderController@returnSupplier')->name('delivery.return-supplier');
    Route::post('api-po/delivery-keepit', 'Api\PurchaseOrderController@keepIt')->name('delivery.keep-it');
    Route::post('api-po/delivery-cancelled', 'Api\PurchaseOrderController@cancelled')->name('delivery.cancelled');
    Route::post('api-po/delivery-move-new-po', 'Api\PurchaseOrderController@moveNewPO')->name('delivery.move-new-po');
    //Region For Country State and City
    Route::get('api-countries', 'Api\RegionController@getAllCountry');
    Route::get('api-states/{id}', 'Api\RegionController@getAllState');
    Route::get('api-cities/{id}', 'Api\RegionController@getAllCity');
    Route::post('api-cities-list/{id}', 'Api\RegionController@getCityList');



    // Range
    Route::resource('api-range', 'Api\RangeController');
    Route::post('api-range-remove/{id}', 'Api\RangeController@destroy');
    Route::post('api-range-search', 'Api\RangeController@searchByKeyword');

    // Pallets
    Route::resource('api-warehouse', 'Api\WarehouseController');
    Route::post('api-warehouse-remove/{id}', 'Api\WarehouseController@destroy');
    Route::post('api-warehouse-remove-multiple', 'Api\WarehouseController@destroyMany');
    // Products
    Route::resource('api-product', 'Api\ProductsController')->except('destory');
    Route::post('api-product-remove', 'Api\ProductsController@destroy');
    Route::post('api-product-save-buying-range', 'Api\ProductsController@saveBuyingRange');
    Route::post('api-product-save-stock-file', 'Api\ProductsController@saveStockFile');
    Route::post('api-product-save-images', 'Api\ProductsController@saveImages');
    Route::post('api-product-delete-image', 'Api\ProductsController@deleteImage')->name('delete-image');
    Route::post('api-product-save-supplier', 'Api\ProductsController@addSupplier');
    Route::post('api-product-update-suppliers', 'Api\ProductsController@saveSuppliers');
    Route::post('api-product-supplier-remove', 'Api\ProductsController@productSupplierDestroy');
    Route::post('api-product-save-barcodes', 'Api\ProductsController@saveBarcodes');
    Route::post('api-product-barcode-remove', 'Api\ProductsController@productBarcodeDestroy');
    Route::post('api-product-save-warehouse', 'Api\ProductsController@saveWarehouse');
    Route::post('api-product-save-variation', 'Api\ProductsController@saveVariations');
    Route::get('api-product-get-sku', 'Api\ProductsController@getSku');
    Route::get('api-product-search', 'Api\ProductsController@searchProducts')->name('products.search');
    Route::get('api-inner-outer-barcode-details', 'Api\ProductsController@innerOuterDetails');
    Route::get('api-product-locations-detail-by-barcode', 'Api\ProductsController@productLocationDetailByBarcode');

    // Locations
    Route::resource('api-locations', 'Api\LocationsController')->except('destory');

    Route::post('api-locations-remove/{id}', 'Api\LocationsController@destroy');
    Route::post('api-locations-remove-multiple', 'Api\LocationsController@destroyMany');
    Route::post('api-locations-active-multiple', 'Api\LocationsController@activeMany');
    Route::post('api-locations-inactive-multiple', 'Api\LocationsController@inactiveMany');
    Route::post('api-locations-setting-save', 'Api\LocationsController@locationSetting')->name('api-locations-setting-save');
    Route::post('api-locations-inline-update', 'Api\LocationsController@inlineUpdate');
    Route::post('api-locations-row-update', 'Api\LocationsController@rowUpdate');
    Route::post('api-locations-row-copy', 'Api\LocationsController@rowCopy');
    Route::get('api-location-by-keyword', 'Api\LocationsController@locationBykeyword')->name('location.keyword');
    Route::get('api-location-by-keyword-suggestion', 'Api\LocationsController@locationBykeywordSuggestion')->name('location.keyword-suggestion');
    Route::get('api-location-auto-suggest-on-input', 'Api\LocationsController@locationAutoSuggestionOnInput');
    //Route::post('api-setting-location-save','Api\LocationsController@locationSetting')->name('api-setting-location-save');
    // Commodity Codes
    Route::resource('api-commodity-codes', 'Api\CommodityCodesController')->except('destory');
    Route::post('api-commodity-codes-remove/{id}', 'Api\CommodityCodesController@destroy');
    Route::post('api-commodity-codes-remove-multiple', 'Api\CommodityCodesController@destroyMany');


    // Import duty
    Route::resource('api-import-duty', 'Api\ImportDutyController')->except('destory');
    Route::post('api-import-duty-remove/{id}', 'Api\ImportDutyController@destroy');
    Route::post('api-import-duty-remove-multiple', 'Api\ImportDutyController@destroyMany');
    Route::post('api-import-duty-desc-code', 'Api\ImportDutyController@getDescCode');

    // Setting
    Route::resource('api-setting', 'Api\SettingController');
    Route::get('api-setting-terms', 'Api\SettingController@getTerms');
    Route::post('api-setting-store-terms', 'Api\SettingController@storeTerms');
    Route::post('api-setting-update-terms', 'Api\SettingController@updateTerms');

    // References
    Route::resource('api-reference', 'Api\ReferenceController');

    //Tax Payment Report On PO
    Route::any('api-tax-payment-report-po', 'Api\PurchaseOrderController@taxPaymentReport');


    //Category Mapping
    Route::resource('api-category-mapping', 'Api\CategoryMappingController');
    Route::post('api-category-mapping-remove/{id}', 'Api\CategoryMappingController@destroy');
    Route::post('api-category-mapping-remove-multiple', 'Api\CategoryMappingController@destroyMany');

    //Magento Listing Manager
    Route::resource('api-listing-manager-magento', 'Api\ListingManagerController')->except('update');
    Route::post('api-listing-manager-magento/{id}', 'Api\ListingManagerController@update')->name('api-listing-manager-magento.update');
    Route::resource('api-listing-manager', 'Api\ListingManagerController');
    Route::get('api-listing-manager-already-listed', 'Api\ListingManagerController@alreadyListedRecords');
    Route::get('api-listing-manager-to-be-listed', 'Api\ListingManagerController@index');
    Route::get('api-listing-manager-inprogress', 'Api\ListingManagerController@inProgressRecords');
    Route::post('api-listing-manager-product-delist-many', 'Api\ListingManagerController@delistMagentoProduct');
    Route::post('api-listing-manager-product-list-many', 'Api\ListingManagerController@addToList');
    Route::post('api-magento-qty-log-store', 'Api\ListingManagerController@storeMagentoQtyLog');
    Route::post('api-magento-price-log-store', 'Api\ListingManagerController@storeMagentoSellPriceLog');
    Route::post('api-magento-product-enable-disabled', 'Api\ListingManagerController@makeMagentoProductEnableDisabled');
    Route::post('api-listing-manager-magento-set-date-to-go-live', 'Api\ListingManagerController@setDateToGoLive');


    //Booking
    Route::get('api-booking-pos', 'Api\BookingsController@getPurchaseOrders')->name('bookings.search-po');

    //Slot Master
    Route::resource('api-slot', 'Api\SlotController');
    Route::post('api-slot-remove/{id}', 'Api\SlotController@destroy');

    //Qc Checklist Master
    Route::resource('api-qc-checklist', 'Api\QCCheckListController');
    Route::post('api-qc-checklist-remove/{id}', 'Api\QCCheckListController@destroy');
    Route::post('api-qc-checklist-remove-multiple', 'Api\QCCheckListController@destroyMany');
    Route::post('api-checklist-point-remove/{id}', 'Api\QCCheckListController@destroyPoints');

    //QC Checklist Points
    Route::post('api-checklist-points-qc', 'Api\QCCheckListController@getChecklistPoints')->name('qc-checklist-points');


    Route::resource('api-booking', 'Api\BookingsController');
    Route::get('api-week-booking', 'Api\BookingsController@weekBooking')->name('booking-weekly');
    Route::get('api-booking-selected-pos', 'Api\BookingsController@getBookingPOs')->name('booking-po');
    Route::post('api-booking-po-delete', 'Api\BookingsController@deletePO')->name('booking-po-delete');

    Route::get('api-booking-day-list', 'Api\BookingsController@bookingDayList');
    Route::post('api-booking-remove-multiple', 'Api\BookingsController@destroyMany');

    // Material Receipt
    Route::get('api-booking-pending-product-count', 'Api\MaterialReceiptController@pendingProductCount');
    Route::post('api-material-receipt-save-product', 'Api\MaterialReceiptController@saveProduct');

    Route::post('api-material-receipt-save-product-case-details', 'Api\MaterialReceiptController@saveProductCaseDetails');

    Route::post('api-material-receipt-supplier-email', 'Api\MaterialReceiptController@SendEmail');

    Route::post('api-material-action-multiple', 'Api\MaterialReceiptController@actionMany');

    Route::post('api-add-descrepancy', 'Api\MaterialReceiptController@addDescrepancy');

    Route::post('api-store-descrepancy', 'Api\MaterialReceiptController@storeDescrepancy');

    Route::post('api-view-descrepancy', 'Api\MaterialReceiptController@viewDescrepancy');

    Route::post('api-desc-image-delete', 'Api\MaterialReceiptController@deletDescrepancyImage');

    Route::post('api-descrepancy-delete', 'Api\MaterialReceiptController@deletDescrepancy');

    Route::post('api-set-booking-arrived-date', 'Api\MaterialReceiptController@setBookingArrivedDate');

    Route::post('api-delete-delivery-note-image', 'Api\MaterialReceiptController@removeDeliveryNoteImg');

    Route::post('api-material-receipt-save-web-product', 'Api\MaterialReceiptController@setProductWeb');

    Route::post('api-material-receipt-save-product-comment', 'Api\MaterialReceiptController@saveProductComment');

    Route::post('api-material-receipt-save-product-variations', 'Api\MaterialReceiptController@saveProductVariations')->name('material-receipt.api-save-variations');

    Route::post('api-material-receipt-save-product-for-return-to-supplier', 'Api\MaterialReceiptController@saveProductForReturnToSupplier');

    Route::post('api-material-receipt-set-parent-product-delivery-note-qty', 'Api\MaterialReceiptController@setParentProductDeliveryNoteQty');

    Route::post('api-booking-details', 'Api\BookingsController@getBookingDetails');

    Route::post('api-set-booking-completed', 'Api\MaterialReceiptController@setBookingCompleted');

    Route::post('api-quarantin-location-products', 'Api\MaterialReceiptController@getQuarantinLocationProductOnSave');
    //booking qc and qc points
    Route::resource('api-booking-qc', 'Api\BookingQcController');
    Route::post('api-delete-booking-qc-point-img', 'Api\BookingQcController@removeBookingQcCHecklistImage');

    Route::get('api-material-receipt-product-list', 'Api\MaterialReceiptController@productList');


    Route::get('api-material-receipt-sidebar-view', 'Api\MaterialReceiptController@sideBarViewData');

    Route::get('api-material-receipt-manage-variations', 'Api\MaterialReceiptController@manageVariations');

    Route::post('api-material-receipt-remove-product-from-booking', 'Api\MaterialReceiptController@removeProduct');

    Route::post('api-material-receipt-check-putaway-start', 'Api\MaterialReceiptController@checkPutawayStart');



    //Buy by product

    Route::resource('api-buyer-enquiry', 'Api\BuyByProductController');
    Route::post('api-existing-draft-pos', 'Api\BuyByProductController@getExistingPoOfSupplier')->name('api-existing-draft-pos');
    Route::post('api-add-product-to-existing-po', 'Api\BuyByProductController@addProductToExistingPo')->name('api-add-product-to-existing-po');
    Route::post('api-create-po-product', 'Api\BuyByProductController@createPO')->name('api-create-po-product');

    Route::get('api-put-away-dashboard', 'Api\PutAwayController@dashboard');


    //Booking Pallet Receive and Return
    Route::resource('api-booking-pallet', 'Api\BookingPalletController');


    //Booking Pallet Receive and Return
    Route::resource('api-booking-pallet', 'Api\BookingPalletController');


    //Booking Pallet Receive and Return
    Route::resource('api-booking-pallet', 'Api\BookingPalletController');

    //location assignment
    Route::resource('api-location-assignment', 'Api\LocationAssignController');
    Route::get('api-empty-locations', 'Api\LocationAssignController@emptyLocationsData');
    Route::get('api-assigned-location', 'Api\LocationAssignController@assignedPickLocations');

    //bulk update qty in fit location
    Route::post('api-update-storage-capacity', 'Api\LocationAssignController@bulkUpdate');

    //get data for pop up on stock hold days
    Route::get('api-inner-outer-barcode-bulk-locations', 'Api\LocationAssignController@detailsOfInnerOuterBarcode');


    //PutAway
    Route::group(['prefix' => 'put-aways'], function() {
        Route::get('products', 'Api\PutAwayController@putAwayProducts')->name('put-aways.products');
        Route::get('products/detail', 'Api\PutAwayController@putAwayProductsDetail')->name('put-aways.products-detail');
        Route::post('products/put-away', 'Api\PutAwayController@storePutAway')->name('put-aways.products-store');
    });

    //Putaway joblist
    Route::group(['prefix' => 'put-away-joblist'], function() {
        Route::get('products', 'Api\PutAwayController@putAwayJobsProducts')->name('put-away-joblist.products');
        //Route::get('products/detail', 'Api\PutAwayController@putAwayProductsDetail')->name('put-aways.products-detail');
        //Route::post('products/put-away', 'Api\PutAwayController@storePutAway')->name('put-aways.products-store');
    });

    Route::resource('api-replen', 'Api\ReplenController');
    Route::get('api-replen-select-pallet', 'Api\ReplenController@replenSelectPallet')->name('api-replen-select-pallet');
    Route::get('api-replen-finish-pallet', 'Api\ReplenController@replenFinishPallet')->name('api-replen-finish-pallet');
    Route::get('api-replen-product-list', 'Api\ReplenController@replenProductList')->name('api-replen-product-list');
    Route::get('api-replen-product', 'Api\ReplenController@replenProduct')->name('api-replen-product');

    //replen
    Route::resource('api-replen-request', 'Api\ReplenRequestController');
    Route::post('api-edit-override', 'Api\ReplenRequestController@editOverride');
    Route::get('api-product-replen-info', 'Api\ReplenRequestController@searchProductReplenInfo');
    Route::post('api-assign-aisle-store', 'Api\ReplenRequestController@storeAssignAisle')->name('store-assign-aisle');
    Route::get('api-assign-aisle-delete/{id}', 'Api\ReplenRequestController@deleteAssignAisle')->name('delete-assign-aisle');


    //products Warehouse tab api
    Route::get('api-product-location-qty', 'Api\ProductsController@locationQty');
    Route::get('api-product-qty-po-not-booked-in', 'Api\ProductsController@onPONOtBookedIn');
    Route::get('api-booked-in-not-arrived', 'Api\ProductsController@bookedInButNotArrivedYet');
    Route::get('api-waiting-to-be-putaway', 'Api\ProductsController@waitingToBePutAway');

    Route::get('api-excess-qty-received-report', 'Api\ReportController@excessQtyReceivedReport');

    //call cron from replen request
    Route::get('api-call-cron-start', 'Api\ReplenRequestController@storeCronCall')->name('call-cron');

    Route::resource('api-report-stock-control', 'Api\ReportStockController')->except('destory');
});

Route::get('api-purchase-order-download-pdf', 'Api\PurchaseOrderController@downloadPO')->name('purchase-order.download-pdf');
Route::get('api-qc-checklist-print-qc', 'Api\QCCheckListController@printQC')->name('print-qc');


Route::get('api-budget-static-data', 'Api\RegionController@getStaticDataForRangeBudget')->name('range-static-data');
