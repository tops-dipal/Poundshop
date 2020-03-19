<?php

namespace App\Http\Controllers;

use App\CategoryMapping;
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

                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function index() {
        $allRanges = \App\Range::get()->makeHidden(['parent', 'children'])->toArray();
        $allRanges = $this->buildCategoryTree($allRanges);

        $allMagentoCat=\App\MagentoCategories::select('category_id as id','name','parent_id')->get()->toArray();

        $allMagentoCat = $this->buildCategoryTree($allMagentoCat);
      
        return view('category-mapping.index',compact('allRanges','allMagentoCat'));
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
            function edit(CategoryMapping $categoryMapping) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CategoryMapping  $categoryMapping
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

}
