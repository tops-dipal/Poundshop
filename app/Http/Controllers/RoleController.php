<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
// use GuzzleHttp\Client;
use DB;

class RoleController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);

        $this->middleware('permission:role-create', ['only' => ['create','store']]);

        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:role-delete', ['only' => ['destroy']]);

    }


    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $page_title = $prefix_title = 'User Roles';
        
        if ($request->ajax()) 
        {
            $data = Role::latest()->get();
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row)
                    {
                        $action_html = $btn1 =  $btn2 = "";
                        
                        if(auth()->user()->can('role-edit'))
                        {    
                            $btn1 = '<a href="'.route('roles.edit',$row->id).'" title="Edit" class="btn-edit"><span class="icon-moon icon-Edit"></span></a>';
                        }
                        
                        if(auth()->user()->can('role-delete'))
                        {    
                            $btn2 = '<form action="'.route('roles.destroy',$row->id).'" method="post">
                                        '.csrf_field().'
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <a href="javascript:void(0)" title="Delete" class="btn-delete" onclick="delete_record(this)"><span class="icon-moon icon-Delete"></span></a>
                                    </form>
                                    ';
                        }
                        
                       $action_html =   '<ul class="action-btns">
                                            <li>
                                                '.$btn1.'
                                            </li>
                                            <li>
                                                '.$btn2.'
                                            </li>
                                        </ul>';

                        return $action_html;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('roles.index',compact('page_title','prefix_title'))->with('i', ($request->input('page', 1) - 1) * 5);
    }


    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

    */
    public function create()
    {
        $page_title = $prefix_title = 'Create New Role';
        
        $temp_permissions = Permission::get();
        
        $permissions = buildTree($temp_permissions);
       
        return view('roles.create',compact('permissions', 'page_title', 'prefix_title'));
    }


    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

    */
    public function store(Request $request)
    {
        $validationMessage = [
                    'name.required' => 'The Group Name field is required.',
                    'name.unique' => 'The Group Name field must be different from all present group.',
                    'permission.required' => 'The Permission field is required.',
                ];

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ], $validationMessage);


        $role = Role::create(['name' => $request->input('name')]);

        $role->syncPermissions($request->input('permission'));


        return redirect()->route('roles.index')
                         ->with('success','Role created successfully');
    }

    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

    */
    public function edit($id)
    {
        $page_title = $prefix_title = 'Edit Role';

        $role = Role::find($id);

        $temp_permissions = Permission::get();

        $permissions = buildTree($temp_permissions);

        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)

            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')

            ->all();


        return view('roles.edit',compact('page_title', 'prefix_title', 'role','permissions','rolePermissions'));
    }


    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

    */
    public function update(Request $request, $id)
    {

         $validationMessage = [
                    'name.required' => 'The Group Name field is required.',
                    'name.unique' => 'The Group Name field must be different from all present group.',
                    'permission.required' => 'The Permission field is required.',
                ];

        $this->validate($request, [
            'name' => 'required|unique:roles,name,'.$id,
            'permission' => 'required',
        ], $validationMessage);

        $role = Role::find($id);

        $role->name = $request->input('name');

        $role->save();


        $role->syncPermissions($request->input('permission'));


        return redirect()->route('roles.index')

                        ->with('success','Role updated successfully');
    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

    */
    public function destroy($id)
    {

        DB::table("roles")->where('id',$id)->delete();

        return redirect()->route('roles.index')

                        ->with('success','Role deleted successfully');
    }
}
