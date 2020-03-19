<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Lang;
use App\Country;
use App\Products;
use App\ProductImage;
use App\CommodityCodes;
use App\SupplierMaster;
use App\VariationThemes;
use App\Tags;
use App\Range;

class ProductsController extends Controller
{
     /**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:product-list', ['only' => ['index']]);

        $this->middleware('permission:product-create', ['only' => ['form']]);

        $this->middleware('permission:product-edit', ['only' => ['form']]);
    }


    /**
     * Display a listing of the resource.
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = $prefix_title = Lang::get('messages.inventory.inventory_list');

        return view('product.index', compact('page_title', 'prefix_title'));
    }

    /**
     * Show the form for creating a new resource.
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    public function form($id = "", Request $request)
    {   
        $page_title = $prefix_title = Lang::get('messages.inventory.product_add');

        $active_tab = !empty($request->active_tab) ? $request->active_tab : 'buying-range';

        $result = [];

        $already_assigned_suppliers = [];

        $product_suppliers = [];

        $countries = [];
        
        $tags = [];
        
        $commodity_codes = [];
        
        $variation_themes = [];

        $suppliers = [];

        $sel_buying_range_parent_ids = "";

        $allRanges = [];

        $allRanges = Range::get()->makeHidden(['parent', 'children'])->toArray();
        
        $allRanges = $this->buildCategoryTree($allRanges);

        if(!empty($id))
        {    
            $result = Products::find($id);
            
            if(!empty($result))
            {
                $countries = Country::get();

                $tags = Tags::get();

                $commodity_codes = CommodityCodes::get();

                $suppliers = SupplierMaster::get();

                $variation_themes = VariationThemes::get();

                $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');
           

                $product_suppliers = $result->suppliers()->get();
                    
                if(!empty($product_suppliers))
                {
                    foreach($product_suppliers as $product_supplier)
                    {
                        $already_assigned_suppliers[] = $product_supplier->supplier_id;
                    }   
                }     

                $sel_buying_range_parent_ids = !empty($result->buying_range) ? $result->buying_range->getParentsNames() : '';
            }
            else
            {
                $active_tab = 'buying-range';

                $id = "";
            }      
        }
        else
        {
            $active_tab = 'buying-range';
        }
        
        return view('product.form',compact(
                                            'id', 
                                            'page_title', 
                                            'prefix_title',
                                            'result', 
                                            'countries',
                                            'commodity_codes', 
                                            'active_tab',
                                            'suppliers',
                                            'product_suppliers',
                                            'already_assigned_suppliers',
                                            'variation_themes',
                                            'tags',
                                            'allRanges',
                                            'sel_buying_range_parent_ids',
                                        )
                    );
    }

    function buildCategoryTree($elements = array(), $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {

            if ($element['parent_id'] == $parentId) 
            {

                $children = $this->buildCategoryTree($elements, $element['id']);

                if ($children) {

                    $element['children'] = $children;

                }

                $branch[] = $element;

            }

        }

        return $branch;
    }

    public function magentoRangeContent(Request $request)
    {

        if(!empty($request->buying_category_id))
        {
            $result = Range::with('magentoCategories')->find($request->buying_category_id);

            echo view('product.magento_range_content', compact('result'));
        }    
        else
        {
            echo '';
        }
        
    }

    public function formStockFile($id = "")
    {
        if(!empty($id))
        {    
            $result = Products::find($id);

            $countries = Country::get();

            $tags = Tags::get();

            $commodity_codes = CommodityCodes::get();
            
            if(!empty($result))
            {   
                echo view('product.form_stock_file',compact('result', 'countries', 'tags', 'commodity_codes'));
            }
        }    
    }

    public function formBarcodes($id = "")
    {
        if(!empty($id))
        {    
            $result = Products::find($id);
            
            if(!empty($result))
            {   
                echo view('product.form_barcode',compact('result'));
            }
        }    
    }

    function formVariations($id = "")
    {
        if(!empty($id))
        {
            $result = Products::find($id);
            
            if(!empty($result) && $result->product_type == 'parent')
            {    
                $variation_themes = VariationThemes::get();

                return view('product.form_variations',compact('result', 'id', 'variation_themes'));
            }
            
        }    
    }
    
    public function GetProductVariations($id = "")
    {
        $html = "";

        if(!empty($id))
        {
            $result = Products::where(['parent_id' => $id, 'product_type' => 'variation' ])->get();

            if(!empty($result))
            {    
                $html = view('product.list_variations', compact('result', 'id'));
            }
        }   

        return $html; 
    }
        
    public function formImages($id = "")
    {
        if(!empty($id))
        {    
            $result = Products::find($id);
            
            if(!empty($result))
            {   
                echo view('product.form_images',compact('result'));
            }
        }    

    }

    public function formBuyingRange(Request $request, $id = "")
    {
        if(!empty($id))
        {       
            $result = Products::find($id);

            $allRanges = Range::get()->makeHidden(['parent', 'children'])->toArray();
            
            $allRanges = $this->buildCategoryTree($allRanges);

            $sel_buying_range_parent_ids = !empty($result->buying_range) ? $result->buying_range->getParentsNames() : ''; 
            $sub_active_tab = !empty($request->active_tab) ? $request->active_tab : "";
            
            if(!empty($result))
            {   
                echo view('product.form_buying_range',compact('result', 'allRanges', 'sel_buying_range_parent_ids', 'sub_active_tab'));
            }
        }   
    }

    // public function formBuyingRange($id = "")
    // {   
    //     $page_title = $prefix_title = Lang::get('messages.inventory.product_add');

    //     $active_tab = 'buying-range';

    //     $commodity_code = array();
        
    //     $result = array();

    //     if(!empty($id))
    //     {    
    //         $result = Products::select('id','buying_category_id','sku')->where('id', $id)->get()->first();

    //         if(!empty($result))
    //         {
    //             $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');
    //         }    
    //     }

    //     return view('product.form_buying_range',compact('page_title', 'prefix_title','result', 'commodity_code', 'id', 'active_tab'));
    // }
    
    // public function formStockfile($id = "")
    // {
    //     if(!empty($id))
    //     {   
    //         $select_array = [
    //                             'id',
    //                             'sku',
    //                             'buying_category_id',
    //                             'product_identifier_type',
    //                             'product_identifier',
    //                             'product_type',
    //                             'title',
    //                             'short_title',
    //                             'sku',
    //                             'country_of_origin',
    //                             'commodity_code_id',
    //                             'is_essential',
    //                             'on_hold',
    //                             'brand',
    //                             'threshold_quantity',
    //                             'single_selling_price',
    //                             'vat_type',
    //                             'bulk_selling_price',
    //                             'bulk_selling_quantity',
    //                             'recom_retail_price',
    //                             'comment',
    //                             'long_description',
    //                             'short_description',
    //                             'product_length',
    //                             'product_width',
    //                             'product_height',
    //                             'product_weight',
    //                             'is_seasonal',
    //                             'seasonal_from_date',
    //                             'seasonal_to_date',
    //                             'created_at',
    //                             'created_by',
    //                         ];
                            
    //         $result = Products::select($select_array)->where('id', $id)->get()->first();

    //         if(!empty($result))
    //         { 
    //             $active_tab = 'stock-file';

    //             $commodity_code = array();

    //             $countries = Country::get();

    //             $commodity_codes = CommodityCodes::get();

    //             $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');

    //             return view('product.form_stock_file',compact('page_title', 'prefix_title','result', 'commodity_code', 'countries', 'id', 'active_tab', 'commodity_codes'));
    //         }
    //         else
    //         {
    //             return back()->withInput();    
    //         }    
    //     }
    //     else
    //     {
    //         return back()->withInput();
    //     }    
    // }

    // public function formBarcodes($id = "")
    // {
    //     if(!empty($id))
    //     {    
    //         $active_tab = 'barcodes';

    //         $result = Products::select('id', 'product_type')->where('id', $id)->get()->first();
            
    //         if(!empty($result))
    //         {   
    //             $product_bardcodes = $result->barCodes()->get();
                
    //             $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');

    //             return view('product.form_barcode',compact('page_title', 'prefix_title','id', 'active_tab', 'product_bardcodes', 'result'));
    //         }
    //         else
    //         {
    //             return back()->withInput();    
    //         }
    //     }
    //     else
    //     {
    //         return back()->withInput();
    //     }    
    // }

    // public function formImages($id = "")
    // {
    //     if(!empty($id))
    //     {    

    //         $active_tab = 'images';

    //         $result=Products::find($id);
            
    //         $productImagesCount=ProductImage::where('product_id',$id)->count();
            
    //         $productImages=ProductImage::where('product_id',$id)->get();


    //         $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');

    //         return view('product.form_images',compact('page_title', 'prefix_title','result', 'id', 'active_tab','productImagesCount','productImages'));
    //     }
    //     else
    //     {
    //         return back()->withInput();
    //     }    
    // }

    // public function formSuppliers($id = "")
    // {
    //     if(!empty($id))
    //     {    
    //         $already_assigned_suppliers = [];

    //         $active_tab = 'suppliers';
            
    //         $result = Products::select('id', 'product_type')->where('id', $id)->get()->first();
            
    //         if(!empty($result))
    //         {
    //             $product_suppliers = $result->suppliers()->get();
                
    //             if(!empty($product_suppliers))
    //             {
    //                 foreach($product_suppliers as $product_supplier)
    //                 {
    //                     $already_assigned_suppliers[] = $product_supplier->supplier_id;
    //                 }   
    //             }    

    //             $suppliers = SupplierMaster::get();

    //             $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');

    //             return view('product.form_suppliers',compact('page_title', 'prefix_title','result','id', 'active_tab', 'suppliers', 'product_suppliers', 'already_assigned_suppliers'));
    //         }
    //         else
    //         {
    //             return back()->withInput();    
    //         }    
    //     }
    //     else
    //     {
    //         return back()->withInput();
    //     }    
    // }

    // public function formWarehouse($id = "")
    // {   
    //     // temp set ID
    //     $id = '1';

    //     if(!empty($id))
    //     {    
    //         $result = Products::select('id', 'product_type')->where('id', $id)->get()->first();

    //         if(!empty($result))
    //         {    
    //             $active_tab = 'warehouse';

    //             $locations = $result->locations()->get();
                
    //             $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');

    //             return view('product.form_warehouse',compact('page_title', 'prefix_title','result', 'id', 'active_tab','locations'));
    //         }
    //         else
    //         {
    //             return back()->withInput();    
    //         }    
    //     }
    //     else
    //     {
    //         return back()->withInput();
    //     }    
    // }

    // public function addMoreImage(Request $request)
    // {
    //     //print_r($request->all());exit;
    //     $nextPreviewId="";
    //     $nextImageClass="";
    //     $nextAddMoreBtnClass="";
    //     $nextCount=$request->nextNum;
    //     return response()->json(['view' => view('product.add-more-images',compact('nextCount'))->render()]); 
    // }

    // function formVariations($id = "")
    // {
    //     if(!empty($id))
    //     {
    //         $result = Products::select('id', 'product_type', 'variation_theme_id')->where('id', $id)->get()->first();
            
    //         if(!empty($result) && $result->product_type == 'parent')
    //         {    
    //             $active_tab = 'variation';

    //             $variation_themes = VariationThemes::get();

    //             $page_title = $prefix_title = Lang::get('messages.inventory.product_edit');

    //             return view('product.form_variations',compact('page_title', 'prefix_title','result', 'id', 'active_tab','variation_themes'));
    //         }
    //         else
    //         {
    //             return back()->withInput();
    //         }    
    //     }
    //     else
    //     {
    //         return back()->withInput();
    //     }    
    // }

}
