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

Route::post('forgot-password', 'Api\ForgotpasswordController@sendResetLinkEmail')->name('user.forgot-password');

//Magento Apis Url

Route::resource('api-magento-category','Api\MagentoCateController');

Route::resource('api-magento-product','Api\MagentoProController');

Route::resource('api-magento-product-detail','Api\MagentoProDetailController');

Route::resource('api-magento-product-detail-upd','Api\MagentoProDetailUpdController');

Route::resource('api-magento-product-qty-update','Api\MagentoQtyUpdController');

Route::resource('api-magento-product-price-update','Api\MagentoPriceUpdController');

Route::resource('api-magento-product-delete','Api\MagentoProductDelController');

Route::resource('api-magento-product-post','Api\MagentoProPostController');

Route::resource('api-magento-product-revise','Api\MagentoProReviseController');

Route::resource('api-magento-product-merge','Api\ProductMergeController');

Route::resource('api-magento-product-enable','Api\MagentoEnabledUpdController');

Route::group(['middleware' => ['auth:api']], function() {
	// User Role & permissions
    Route::get('permissions-list', 'Api\RoleController@permissionList');
	Route::delete('delete-user-role', 'Api\RoleController@deleteUserRole');

	// Cartons    
    Route::resource('api-cartons','Api\CartonsController');
    Route::post('api-cartons-remove-multiple','Api\CartonsController@destroyMany');
    
    // totes
    Route::resource('api-totes','Api\TotesController');
    Route::post('api-totes-remove/{id}','Api\TotesController@destroy');
    Route::post('api-totes-remove-multiple','Api\TotesController@destroyMany');

    // Suppliers
	Route::resource('api-supplier','Api\SupplierController')->except('destory');
    Route::post('api-supplier-remove','Api\SupplierController@destroy');
    Route::post('api-supplier-email','Api\SupplierController@SendEmail');
    Route::get('api-supplier-contacts','Api\SupplierController@supplierContacts');

    Route::post('api-supplier-save-general-info','Api\SupplierController@save_general_info');
    Route::post('api-supplier-set-default-contact','Api\SupplierController@save_default_contacts');
    Route::post('api-supplier-save-contacts','Api\SupplierController@save_contacts');
    Route::post('api-supplier-destory-contacts','Api\SupplierController@destory_contacts');
    Route::post('api-supplier-save-payment-info','Api\SupplierController@save_payment_info');
    Route::post('api-supplier-save-terms-condition','Api\SupplierController@save_terms_and_condition');

    // Pallets    
    Route::resource('api-pallets','Api\PalletsController');
    Route::post('api-pallets-remove/{id}','Api\PalletsController@destroy');
    Route::post('api-pallets-remove-multiple','Api\PalletsController@destroyMany');

    // Users
    Route::resource('api-users','Api\UsersController');
    Route::post('api-users-remove/{id}','Api\UsersController@destroy');
    Route::post('api-users-remove-multiple','Api\UsersController@destroyMany');
    Route::post('api-users-remove-image','Api\UsersController@removeImage');
    Route::post('api-attachment-delete/{id}','Api\UsersController@deleteAttachments');

    //Purchase Orders
    Route::resource('api-purchase-orders','Api\PurchaseOrderController');
    Route::post('api-purchase-orders/update-terms','Api\PurchaseOrderController@updateTerms')->name('api-purchase-orders.update-terms');
    Route::post('api-purchase-orders-remove-multiple','Api\PurchaseOrderController@destroyMany');
    Route::post('api-purchase-orders-item-save','Api\PurchaseOrderController@poItemSave');
    Route::post('api-purchase-orders-item-remove','Api\PurchaseOrderController@destroyItem')->name('purchase-order.item-delet');
    Route::post('api-purchase-orders-item-remove-multiple','Api\PurchaseOrderController@destroyItemMany')->name('purchase-order.item-deletemany');
    Route::get('api-purchase-order-download-pdf','Api\PurchaseOrderController@downloadPO')->name('purchase-order.download-pdf');
    Route::post('api-purchase-order-send-email-pdf','Api\PurchaseOrderController@sendPO')->name('purchase-order.email-pdf');
    Route::post('api-purchase-order-recalculate-items','Api\PurchaseOrderController@reCalculateItems')->name('purchase-order.re-calculate-items');
    Route::resource('api-purchase-orders-revise','Api\PurchaseOrderRevisesController');
    
    
    
    
    
    //Region For Country State and City
    Route::get('api-countries','Api\RegionController@getAllCountry');
    Route::get('api-states/{id}','Api\RegionController@getAllState');
    Route::get('api-cities/{id}','Api\RegionController@getAllCity');
    Route::post('api-cities-list/{id}','Api\RegionController@getCityList');
   


    // Range 
    Route::resource('api-range','Api\RangeController');
    Route::post('api-range-remove/{id}','Api\RangeController@destroy');
    Route::post('api-range-search','Api\RangeController@searchByKeyword');
    
    // Pallets    
    Route::resource('api-warehouse','Api\WarehouseController');
    Route::post('api-warehouse-remove/{id}','Api\WarehouseController@destroy');
    Route::post('api-warehouse-remove-multiple','Api\WarehouseController@destroyMany'); 
    
    // Products
    Route::resource('api-product','Api\ProductsController')->except('destory');
    Route::post('api-product-remove','Api\ProductsController@destroy');  
    Route::post('api-product-save-buying-range','Api\ProductsController@saveBuyingRange');  
    Route::post('api-product-save-stock-file','Api\ProductsController@saveStockFile');  
    Route::post('api-product-save-images','Api\ProductsController@saveImages'); 
    Route::post('api-product-delete-image','Api\ProductsController@deleteImage')->name('delete-image'); 
    Route::post('api-product-save-supplier','Api\ProductsController@addSupplier');  
    Route::post('api-product-update-suppliers','Api\ProductsController@saveSuppliers');  
    Route::post('api-product-supplier-remove','Api\ProductsController@productSupplierDestroy');
    Route::post('api-product-save-barcodes','Api\ProductsController@saveBarcodes');  
    Route::post('api-product-barcode-remove','Api\ProductsController@productBarcodeDestroy'); 
    Route::post('api-product-save-warehouse','Api\ProductsController@saveWarehouse');  
    Route::post('api-product-save-variation','Api\ProductsController@saveVariations');  
    Route::get('api-product-get-sku','Api\ProductsController@getSku');  
    Route::get('api-product-search','Api\ProductsController@searchProducts')->name('products.search');
    
    // Locations
    Route::resource('api-locations','Api\LocationsController')->except('destory');

    Route::post('api-locations-remove/{id}','Api\LocationsController@destroy');  
    Route::post('api-locations-remove-multiple','Api\LocationsController@destroyMany');
    Route::post('api-locations-active-multiple','Api\LocationsController@activeMany');
    Route::post('api-locations-inactive-multiple','Api\LocationsController@inactiveMany'); 
    Route::post('api-locations-setting-save','Api\LocationsController@locationSetting')->name('api-locations-setting-save');
    Route::post('api-locations-inline-update','Api\LocationsController@inlineUpdate');
    Route::post('api-locations-row-update','Api\LocationsController@rowUpdate');
    Route::post('api-locations-row-copy','Api\LocationsController@rowCopy');
    //Route::post('api-setting-location-save','Api\LocationsController@locationSetting')->name('api-setting-location-save');

    // Commodity Codes
    Route::resource('api-commodity-codes','Api\CommodityCodesController')->except('destory');
    Route::post('api-commodity-codes-remove/{id}','Api\CommodityCodesController@destroy');  
    Route::post('api-commodity-codes-remove-multiple','Api\CommodityCodesController@destroyMany');


    // Import duty 
    Route::resource('api-import-duty','Api\ImportDutyController')->except('destory');
    Route::post('api-import-duty-remove/{id}','Api\ImportDutyController@destroy');  
    Route::post('api-import-duty-remove-multiple','Api\ImportDutyController@destroyMany');
    Route::post('api-import-duty-desc-code','Api\ImportDutyController@getDescCode');

    // Setting 
    Route::resource('api-setting','Api\SettingController');
    Route::get('api-setting-terms','Api\SettingController@getTerms');
    Route::post('api-setting-store-terms','Api\SettingController@storeTerms');
    Route::post('api-setting-update-terms','Api\SettingController@updateTerms');

    // References
    Route::resource('api-reference','Api\ReferenceController');    

    //Tax Payment Report On PO
    Route::any('api-tax-payment-report-po','Api\PurchaseOrderController@taxPaymentReport');


    //Category Mapping
    Route::resource('api-category-mapping','Api\CategoryMappingController');
    Route::post('api-category-mapping-remove/{id}','Api\CategoryMappingController@destroy');  
    Route::post('api-category-mapping-remove-multiple','Api\CategoryMappingController@destroyMany');

    //Magento Listing Manager
    Route::resource('api-listing-manager-magento','Api\ListingManagerController')->except('update');
    Route::post('api-listing-manager-magento/{id}','Api\ListingManagerController@update')->name('api-listing-manager-magento.update');
    Route::resource('api-listing-manager','Api\ListingManagerController');
    Route::get('api-listing-manager-already-listed','Api\ListingManagerController@alreadyListedRecords');
    Route::get('api-listing-manager-to-be-listed','Api\ListingManagerController@index');
    Route::get('api-listing-manager-inprogress','Api\ListingManagerController@inProgressRecords');
    Route::post('api-listing-manager-product-delist-many','Api\ListingManagerController@delistMagentoProduct');
    Route::post('api-listing-manager-product-list-many','Api\ListingManagerController@addToList');
    Route::post('api-magento-qty-log-store','Api\ListingManagerController@storeMagentoQtyLog');
    Route::post('api-magento-price-log-store','Api\ListingManagerController@storeMagentoSellPriceLog');
    Route::post('api-magento-product-enable-disabled','Api\ListingManagerController@makeMagentoProductEnableDisabled');



    
});

Route::get('api-purchase-order-download-pdf','Api\PurchaseOrderController@downloadPO')->name('purchase-order.download-pdf');