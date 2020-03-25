<?php

namespace App\Http\Controllers;

use App\CategoryMapping;
use App\Range;
use App\MagentoCategories;
use Illuminate\Http\Request;

class CategoryMappingController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function buildCategoryTree($elements = array(), $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {

            if ($element['parent_id'] == $parentId) {

                $children = $this->buildCategoryTree($elements, $element['id']);

                if ($children) {

                    $element['children'] = $children;
                }
                else
                {
                    $element['children']='';
                }


                $branch[] = $element;
            }
        }

        return $branch;
    }
    public function getForm($type)
    {
        $allRanges     =Range::getAllRange()->toArray();
        $allRanges     = $this->buildCategoryTree($allRanges);

        $allMagentoCat = MagentoCategories::getAllCategory()->toArray();
        $allMagentoCat = $this->buildCategoryTree($allMagentoCat);
        return response()->json(['view' => view('category-mapping.create',compact('allRanges', 'allMagentoCat'))->render()]); 
    }
    public
            function index(Request $request) {

        $allRanges     = Range::getAllRange()->toArray();

        $allRanges     = $this->buildCategoryTree($allRanges);

        $allMagentoCat = MagentoCategories::getAllCategory()->toArray();

        $allMagentoCat = $this->buildCategoryTree($allMagentoCat);

        return view('category-mapping.index', compact('allRanges', 'allMagentoCat'));
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public
            function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CategoryMapping  $categoryMapping
     * @return \Illuminate\Http\Response
     */
    public
            function show(CategoryMapping $categoryMapping) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CategoryMapping  $categoryMapping
     * @return \Illuminate\Http\Response
     */
    public
            function edit(CategoryMapping $categoryMapping,Request $request) {
        $editData=$categoryMapping;
        $range=Range::find($editData->range_id);
         $parentIds=array();
        $parentIds=explode(">", $range->getParentsNames());  
        $selectedParent=$parentIds[0]; 

        $allRanges     =  Range::getAllRange()->toArray();
        $allRanges     = $this->buildCategoryTree($allRanges);

        $allMagentoCat = MagentoCategories::getAllCategory()->toArray();
        $allMagentoCat = $this->buildCategoryTree($allMagentoCat);
        $status='mapped';

        
        return view('category-mapping.mapped-notmapped', compact('allRanges', 'allMagentoCat','editData','parentIds','status','selectedParent'));
    }

    public
            function getChildCategories(Request $request) {
        if($request->type=='range')
        {
            $parentOfSelectedId = Range::find($request->id)->parent_id;

            $child              = Range::getAllRange()->toArray();

            $child=$this->buildCategoryTree($child,$request->id);
            
            $childStatus        = "Yes";
            $parent_id          = $request->id;
            $process            = $request->process;
            if (count($child) == 0) {
                $childStatus = "No";
            }
            $parentIds = [];
            $range     = Range::find($request->editId);
            if ($request->process == 'edit') {
                $parentId      = explode(">", $range->getParentsNames());
                //$parentIdCycle = array_pop($parentId);
                $parentIds     = $parentId;
            }
            $selected_parent=$request->selected_parent;
            $editId=$request->editId;
            return response()->json(['view' => view('category-mapping.range-child-category', compact('child', 'parent_id', 'parentIds', 'process','selected_parent','editId'))->render(), 'childStatus' => $childStatus, 'parent_id' => $parent_id, 'parentOfSelectedId' => $parentOfSelectedId, 'parentIds' => $parentIds]);
        }
        else
        {
            if(!empty($request->id))
            {
                  $parentOfSelectedId = MagentoCategories::where('category_id',$request->id)->first()->parent_id;
            }
              else{
                  $parentOfSelectedId = '';
              }
            $child              = MagentoCategories::getAllCategory()->toArray();;
            $child=$this->buildCategoryTree($child,$request->id);
            
            $childStatus        = "Yes";
            $parent_id          = $request->id;
            $process            = $request->process;
            if (count($child) == 0) {
                $childStatus = "No";
            }
            $parentIds = [];
            $range     = MagentoCategories::where('category_id',$request->editId)->first();
            
            if ($request->process == 'edit') {
                $parentId      = explode("/", $range->path);
                //$parentIdCycle = array_pop($parentId);
                $parentIds     = $parentId;
            }
            $selected_parent=$request->selected_parent;
            $editId=$request->editId;
            return response()->json(['view' => view('category-mapping.magento-child-category', compact('child', 'parent_id', 'parentIds', 'process','selected_parent','editId'))->render(), 'childStatus' => $childStatus, 'parent_id' => $parent_id, 'parentOfSelectedId' => $parentOfSelectedId, 'parentIds' => $parentIds]);
        }
       
    }


  

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CategoryMapping  $categoryMapping
     * @return \Illuminate\Http\Response
     */
    public
            function update(Request $request, CategoryMapping $categoryMapping) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryMapping  $categoryMapping
     * @return \Illuminate\Http\Response
     */
    public
            function destroy(CategoryMapping $categoryMapping) {
        //
    }

    public function mappedNotMappedRelation(Request $request,$range_id)
    {
      // dd(Request::route()->getName())
        $editData=CategoryMapping::leftJoin('magento_categories','magento_categories.id','category_mappings.magento_category_id')->select('category_mappings.*','category_mappings.id as cat_map_id','magento_categories.*')->selectRaw('magento_categories.category_id as magento_cat_id')->where('range_id',$range_id)->havingRaw('magento_cat_id != ?',[NULL])->first();
       // dd($editData);
        $parentIdsMagento=array();
         $selectedParentMagento='';
         $rangeData=Range::find($range_id);
        if(empty($editData))
        {
            $status='not-mapped';
        }
        else
        {

            $status='mapped';
          
                $parentIdsMagento=explode("/", $editData->path); 

                if (($key = array_search(1, $parentIdsMagento)) !== false) {
                    unset($parentIdsMagento[$key]);
                } 
               
                
                $selectedParentMagento=$parentIdsMagento[1];
                
           

        }
        
        $range=Range::find($range_id);
        $parentIds=array();
        $parentIds=explode(">", $range->getParentsNames()); 
        $selectedParent=$parentIds[0];  

        $allRanges     = Range::orderBy('category_name','ASC')->get()->makeHidden(['parent', 'children'])->toArray();
        $allRanges     = $this->buildCategoryTree($allRanges);


        $allMagentoCat = MagentoCategories::select('category_id as id','id as table_id', 'name', 'parent_id')->orderBy('name','ASC')->get()->toArray();
        $allMagentoCat = $this->buildCategoryTree($allMagentoCat);

        //dd($editData->magento_category_id);
       // $magentoCat=MagentoCategories::find($editData->magento_category_id);
     
        //$status='mapped';

         
        return view('category-mapping.mapped-notmapped', compact('allRanges', 'allMagentoCat','parentIds','status','selectedParent','range_id','selectedParentMagento','parentIdsMagento','editData','rangeData'));
    }

}
