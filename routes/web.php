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
    Route::resource('locations','LocationsController');
    Route::resource('commodity-codes','CommodityCodeController');
    Route::resource('import-duty','ImportDutyController');
    Route::resource('setting','SettingController');
    Route::resource('category-mapping','CategoryMappingController');
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
});



