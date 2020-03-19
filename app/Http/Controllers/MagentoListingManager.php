<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Lang;
use App\MagentoProductPosting;
use App\Country;
use App\MagentoCategories;
use App\MagentoProduct;

class MagentoListingManager extends Controller
{
     /**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
       
    }

    public function index()
    {
        $active_tab="already-listed";
        $storeList=\App\StoreMaster::get();
        return view('listing-manager.already_listed_tab',compact('storeList','active_tab'));
    }
    public function toBeListed()
    {
        $active_tab="to-be-listed";
        $storeList=\App\StoreMaster::get();
        echo view('listing-manager.to_be_listed_tab',compact('storeList','active_tab'));
    }
    public function inProgressRecords()
    {
        $active_tab="inprogress";
        $storeList=\App\StoreMaster::get();
        echo view('listing-manager.in_progress_tab',compact('storeList','active_tab'));
    }

    public function add($product_master_id, $store_id = "")
    {
        if(!empty($product_master_id) && $store_id)
        {
            $product_master_details = MagentoProductPosting::posting_valid_product($product_master_id, $store_id);
            
            $countries = Country::get();

            $magentoCategories = MagentoCategories::where('parent_id', '!=', 0)->get()->pluck('name', 'id')->toArray();
            
            if(!empty($product_master_details))
            {
                $page_title = $prefix_title = Lang::get('messages.magento_listing.edit_product');

                $magento_posting_details = MagentoProductPosting::get_posting_details($product_master_id, $store_id);

                $select_data = function($magento_data,$product_data,$magento_key,$product_key)
                {
                    return (isset($magento_data) && !empty($magento_data) ? ($magento_data->$magento_key) : $product_data->$product_key);
                };

                return view('listing-manager.magento.add', compact(
                                                    'page_title',
                                                    'prefix_title',
                                                    'product_master_details',
                                                    'magento_posting_details',
                                                    'select_data',
                                                    'product_master_id',
                                                    'store_id',
                                                    'countries',
                                                    'magentoCategories',
                                                )
                            );
            }
            else
            {
                return back()->withInput();
            }    
        }    
    }

    public function edit($magento_product_id)
    {

        if(!empty($magento_product_id))
        {
            $where_array = array(
                            );

            $magento_products_details = MagentoProduct::revise_valid_product($magento_product_id);
            
            $countries = Country::get();

            $magentoCategories = MagentoCategories::where('parent_id', '!=', 0)->get()->pluck('name', 'id')->toArray();
            
            if(!empty($magento_products_details))
            {
                if(!empty($magento_products_details->store_id))
                {    
                    $page_title = $prefix_title = Lang::get('messages.magento_listing.edit_product');
                    
                    $magento_posting_details = MagentoProductPosting::get_revise_details($magento_product_id, $magento_products_details->store_id);
                    
                   
                        $magentoQtyLog=\App\MagentoQtyLog::where('magento_id',$magento_products_details->id)->where('is_quantity_posted',0)->orderBy('id','desc')->first();
                        if(!empty($magentoQtyLog))
                        {
                            $magento_products_details->quantity=$magentoQtyLog->quantity;
                        }
                       
                        $magentoPriceLog=\App\MagentoPriceLog::where('magento_id',$magento_products_details->id)->where('is_selling_price_posted',0)->orderBy('id','desc')->first();
                        if(!empty($magentoPriceLog))
                        {
                            $magento_products_details->selling_price=$magentoPriceLog->selling_price;
                        }
                        
                    
                    $select_data = function($magento_posting_data,$magento_prod_data,$magento_posting_key,$magento_prod_key)
                    {
                        return (isset($magento_posting_data) && !empty($magento_posting_data) ? ($magento_posting_data->$magento_posting_key) : $magento_prod_data->$magento_prod_key);
                    };

                    return view('listing-manager.magento.edit', compact(
                                                        'page_title',
                                                        'prefix_title',
                                                        'magento_products_details',
                                                        'magento_posting_details',
                                                        'select_data',
                                                        'countries',
                                                        'magentoCategories',
                                                    )
                                );
                }    
            }
            else
            {
                return back()->withInput();
            }    
        }     
    }   
}    