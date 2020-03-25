<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/' ,'Auth\LoginController@showLoginForm');
Auth::routes();
Route::get('logout','Auth\LoginController@logout');

Route::get('supplier-material-receipt/{booking_id}','SupplierMaterialReceiptController@index')->name('supplier_material_receipt.index');

Route::group(['middleware' => ['web', 'auth', 'preventBackHistory', 'checkUserSessionToken']], function() 
{
    Route::get('dashboard','DashboardController@index')->name('user-dashboard');
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
    Route::resource('cartons','CartonController');
    Route::resource('totes','TotesController');
    Route::resource('supplier','SupplierController')->except(['edit', 'create', 'show']);
    Route::get('supplier/form{id?}','SupplierController@form')->name('supplier.form');
    Route::get('supplier/supplier-contacts{SupplierId?}','SupplierController@supplier_contacts');
    Route::resource('pallets','PalletsController');
    Route::resource('range','RangeController');
    Route::resource('warehouse','WarehouseController');
    Route::get('locations/update-all-location','LocationsController@updateAllLocationData');
    Route::resource('locations','LocationsController');
    Route::resource('commodity-codes','CommodityCodeController');
    Route::resource('import-duty','ImportDutyController');
    Route::resource('setting','SettingController');
    Route::resource('category-mapping','CategoryMappingController');
    Route::resource('slot','SlotController');
    Route::resource('qc-checklist','QCCheckListController');
    Route::resource('buy-by-product','BuyByProductController');
    Route::resource('location-assign','LocationAssignController');
    //Route::get('locations/setting','LocationsController@setting')->name('locations-setting');
    Route::get('setting-location','LocationsController@setting')->name('locations-setting');

    // Inventory Mangement
    Route::resource('product','ProductsController')->except(['edit', 'create', 'show']);     
    Route::get('product/form/{id?}','ProductsController@form');
    Route::get('product/magento-range-content','ProductsController@magentoRangeContent');
    Route::get('product/form-buying-range/{id}','ProductsController@formBuyingRange');
    Route::get('product/form-stock-file/{id}','ProductsController@formStockFile');
    Route::get('product/form-barcodes/{id}','ProductsController@formBarcodes');
    Route::get('product/form-variations/{id}','ProductsController@formVariations');
    Route::get('product/get-variations-list/{id}','ProductsController@GetProductVariations');
    Route::get('product/form-images/{id}','ProductsController@formImages');
    Route::get('product/product-outer-barcodes','ProductsController@productOuterBarcode');   

    // Route::get('product/form-buying-range/{id?}','ProductsController@formBuyingRange');
    // Route::get('product/form-stock-file/{id}','ProductsController@formStockfile');
    // Route::get('product/form-images/{id}','ProductsController@formImages');
    // Route::get('product/form-suppliers/{id}','ProductsController@formSuppliers');
    // Route::get('product/form-warehouse/{id}','ProductsController@formWarehouse');
    Route::post('product/add-more-images','ProductsController@addMoreImage')->name('add-more-image');
    Route::get('settings/{module_name}','SettingController@show')->name('module-setting');

    //Purchase Order
    Route::resource('purchase-orders','PurchaseOrderController');
    Route::get('purchase-orders/revision/view/{id}','PurchaseOrderController@viewRevision')
            ->where('id', '[0-9]+')->middleware('signed')->name('purchase-order.revision-view');
    //Range Management
    Route::post('get-child-category','RangeController@getChildCategories')->name('get-child-category');
    Route::post('get-child-list','RangeController@getChildCategoryList')->name('get-child-list');
    Route::post('add-more-cat','RangeController@addMoreCategory')->name('add-more-cat');    
    Route::resource('reference','ReferenceController');

    Route::any('tax-payment-report-po','PurchaseOrderController@taxPaymentReport')->name('tax-paymnet-report-po');
    Route::get('range-form-type/{type}','RangeController@getForm')->name('get-form');


    Route::resource('utility','UtilityController');

    Route::get('setting/terms','SettingController@terms')->name('setting-terms');

    // Magento Listing manager
    Route::get('listing-manager/magento','MagentoListingManager@index')->name('magento.index');
    Route::get('listing-manager/magento/already-listed','MagentoListingManager@index')->name('magento-already-listed');
    Route::get('listing-manager/magento/to-be-listed','MagentoListingManager@toBeListed')->name('magento-to-be-listed');
    Route::get('listing-manager/magento/in-progress','MagentoListingManager@inProgressRecords')->name('magento-in-progress');
    Route::get('listing-manager/magento/add/{id}/{store_id}','MagentoListingManager@add')->name('magento.add');
    Route::get('listing-manager/magento/edit/{id}','MagentoListingManager@edit')->name('magento.edit');

    Route::resource('booking-in','BookingsController');
    Route::get('booking-in/booking_day_list/{date}','BookingsController@bookingDayList')->name('booking-in.bookingDayList');


    // Material Receipt
    Route::get('material-receipt/{booking_id}','MaterialReceiptController@index')->name('material_receipt.index');
    Route::post('material-receipt/list-ajax-table','MaterialReceiptController@listAjaxTable')->name('material_receipt.list_ajax');
    Route::post('material-receipt/ajax-manage-variations','MaterialReceiptController@manageVariations');
    Route::get('material-receipt-html-version','MaterialReceiptController@htmlVersion');

    Route::post('material-receipt/side-bar-data','MaterialReceiptController@getSideBarView')->name('material_receipt.sidebar-view');

    Route::post('material-receipt/pallet-return-receive-data','MaterialReceiptController@htmlBookingPallet')->name('material_receipt.booking-pallets');

    //Category Mapping
    Route::get('mapping-form-type/{type}','CategoryMappingController@getForm')->name('get-mapping-form');
    Route::get('mapping-relation/{range_id}','CategoryMappingController@mappedNotMappedRelation')->name('mapping-relation');
    Route::post('mapping-relation-range-list','CategoryMappingController@getChildCategories')->name('mapping-relation-range-list');
     //
    Route::post('get-qc-list-products','QCCheckListController@getQcList')->name('get-qc-list');

    Route::get('print-product-barcodes','PrintViewController@barcode');
    
    Route::get('set-barcode-img','BarcodeImageController@index');

    //Buy By Product Routes
    Route::get('search-barcode/{barcode}','BuyByProductController@serachByBarcode')->name('search-barcode');

    Route::get('put-away-dashboard','PutAwayController@getDashboard')->name('put-away-dashboard');

    Route::get('put-away','PutAwayController@getPutAway')->name('put-away');

    Route::get('put-away-job-list','PutAwayController@getPutAwayJobList')->name('put-away-job-list');

    // Print Qc Checklist for products
    Route::get('print-product-qcchecklist-bookingwise','PrintViewController@productQCChecklistPDF');

    Route::get('qc-checklist-print-qc', 'QCCheckListController@printQC')->name('print-qc');

    //Replen Request
    Route::resource('replen-request','ReplenRequestController');
    Route::resource('replen','ReplenController');
    Route::get('assign-aisle','ReplenRequestController@assignAisle')->name('assign-aisle');

    Route::get('excess-qty-received-report','ReportController@excessQtyReceivedReport')->name('excess-qty-received-report'); 


    //count of warehouse tab base on warehouse selection - Inventory warehouse Tab
    Route::post('count-based-on-site','ProductsController@getSiteWiseWarehouseCount')->name('count-based-on-site');

    
    //Move Product
    Route::get('move-products','ProductsController@moveProducts')->name('move-products');


});



