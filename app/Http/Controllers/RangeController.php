<?php

namespace App\Http\Controllers;

use App\Range;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     function __construct()
    {
        $this->middleware('permission:range-list|range-create|range-edit|range-delete', ['only' => ['index','store']]);

        $this->middleware('permission:range-create', ['only' => ['create','store']]);

        $this->middleware('permission:range-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:range-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $allRanges = \App\Range::orderBy('category_name','ASC')->get()->makeHidden(['parent', 'children'])->toArray();
        $parent = $this->buildCategoryTree($allRanges);
        $process='add';
      
        return view('range.index',compact('parent','process'));
    }
    public function getForm($type)
    {
        $allRanges = \App\Range::orderBy('id','desc')->get()->makeHidden(['parent', 'children'])->toArray();
        $parent = $this->buildCategoryTree($allRanges);
        $process='add';
      
        return response()->json(['view' => view('range.create',compact('parent','process'))->render()]); 
    }
    /*public function getChildCategoryList(Request $request)
    {
        $childCategoryList=Range::where('parent_id',$request->id)->get();
         foreach ($childCategoryList as $key => $value) {
            $child_status=Range::where('parent_id',$value->id)->get()->count();
            if($child_status==0)
            {
                $childCategoryList[$key]->child_status=0;
            }
            else
            {
                $childCategoryList[$key]->child_status=1;
            }
        }
        return response()->json(['view' => view('range.child_category_list',compact('childCategoryList'))->render()]); 
    }*/

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function show(Range $range)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Range $range)
    {
       $editRange=$range;
       
      // $parentchildTree=$this->buildTree($range);
       $fromDate=explode("-",$editRange->seasonal_from);
       $toDate=explode("-",$editRange->seasonal_to);
       if($editRange->seasonal_status==1)
        {
           $editRange->seasonal_range_fromdate=$fromDate[2];
           $editRange->seasonal_range_frommonth=$fromDate[1];
           $editRange->seasonal_range_todate=$toDate[2];
           $editRange->seasonal_range_tomonth=$toDate[1];
        }
        $parentIds=array();
       $parentIds=explode(">", $range->getParentsNames());   
      //print_r($parentIds);exit();
      $ranges = Range::where('parent_id',Null)->orderBy('id','DESC')->cursor();
        
       $allRanges = \App\Range::orderBy('id','desc')->get()->makeHidden(['parent', 'children'])->toArray();
        $parent = $this->buildCategoryTree($allRanges);
       /* foreach ($parent as $key => $value) {
            $child_status=Range::where('parent_id',$value->id)->get()->count();
            if($child_status==0)
            {
                $parent[$key]->child_status=0;
            }
            else
            {
                $parent[$key]->child_status=1;
            }
        }*/
        $process='edit';
        if($request->ajax())
        {

        return response()->json(['view' => view('range.edit',compact('ranges','parent','process','editRange','parentIds'))->render()]);
        }
        else
        {
            return view('range.index',compact('ranges','parent','process','editRange','parentIds'));    
        }
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Range $range)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Range  $range
     * @return \Illuminate\Http\Response
     */
    public function destroy(Range $range)
    {
        //
    }

    public function getChildCategories(Request $request)
    {
        $parentOfSelectedId=Range::find($request->id)->parent_id;
        $child=Range::where('parent_id',$request->id)->cursor();
        $childStatus="Yes";
        $parent_id=$request->id;
        $process=$request->process;
        if(count($child)==0)
        {
            $childStatus="No";
        }
        $parentIds=[];
        $range=Range::find($request->editId);
        if($request->process=='edit')
        {
            $parentId=explode(">",$range->getParentsNames());
            $parentIdCycle=array_pop($parentId);
            $parentIds=$parentId;   
        }
        return response()->json(['view' => view('range.child_categories',compact('child','parent_id','parentIds','process'))->render(),'childStatus'=>$childStatus,'parent_id'=>$parent_id,'parentOfSelectedId'=>$parentOfSelectedId,'parentIds'=>$parentIds]);  
    }

    public function addMoreCategory(Request $request)
    {
        $nextCount=$request->nextaddMoreCat;
        return response()->json(['view' => view('range.add-more-category',compact('nextCount'))->render()]);
    }
    function buildCategoryTree($elements = array(), $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {

            if ($element['parent_id'] == $parentId) 
            {

                $children = $this->buildCategoryTree($elements, $element['id']);
                $element['edit_url']=route('range.edit',$element['id']);
                if ($children) {
                    $element['child_status']=1;
                    
                    $element['children'] = $children;

                }
                else
                {
                    $element['child_status']=0;
                }

                $branch[] = $element;

            }

        }
        return $branch;
    }

    /* function buildChildCategoryTree($elements = array(), $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {

            if ($element['parent_id'] == $parentId) 
            {

                $children = $this->buildCategoryTree($elements, $element['id']);
                $element['edit_url']=route('range.edit',$element['id']);
                if ($children) {
                    $element['child_status']=1;
                    
                    $element['children'] = $children;

                }
                else
                {
                    $element['child_status']=0;
                }

                $branch[] = $element;

            }

        }
        return $branch;
    }
*/
}
