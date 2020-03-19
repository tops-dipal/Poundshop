<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Country;
use App\State;
use App\City;
use Spatie\Permission\Models\Role;
use DB;
use Hash;

class UserController extends Controller
{
        /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

     function __construct()
    {
        $this->middleware('permission:users-list|users-create|users-edit|users-delete', ['only' => ['index','store']]);

        $this->middleware('permission:users-create', ['only' => ['create','store']]);

        $this->middleware('permission:users-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:users-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('users.index');

    }


    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $roles = Role::all();
        $countries = Country::orderBy('name','ASC')->get();
        $sites=\App\Warehouse::get();
        return view('users.create',compact('roles','countries','sites')); 
    }


    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $this->validate($request, [

            'name' => 'required',

            'email' => 'required|email|unique:users,email',

            'password' => 'required|same:confirm-password',

            'roles' => 'required'

        ]);
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')->with('success','User created successfully');

    }


    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

         try{
            $user=User::find($id);
            if($user){
                $roles = Role::all();
                 $country_array = array();
            
            $state_array = array();

            if($user->country_id)
            {
                $country_array[] = $user->country_id;    
            }

           
            if($user->state_id)
            {
                $state_array[] = $user->state_id;    
            }

          
            $countries = Country::get();

            // get states
            $country_states = State::whereIn('country_id',$country_array)->orderBy('name','ASC')->get();
            
            $country_states = object_to_array($country_states);

            // get states array with array key country_id
            $country_states = helper_array_column_multiple_key($country_states, array('country_id'), TRUE);

            // get cities
            $state_cities = City::whereIn('state_id',$state_array)->orderBy('name','ASC')->get();
            
            $state_cities = object_to_array($state_cities);
            
            // get cities array with array key state_id
            $state_cities = helper_array_column_multiple_key($state_cities, array('state_id'), TRUE);
            
           
            $attachments=\App\Attachment::where('user_id',$user->id)->get();
             $state_name=($user->state_id!=0)?\App\State::find($user->state_id)->name:'';
            $city_name=($user->city_id!=0)?\App\City::find($user->city_id)->name:'';
            
            
            return view('users.edit-profile', compact(['user','roles','countries','country_states','state_cities','attachments','state_name','city_name']));
            }else{
               abort(404);    
            }
        } catch (Exception $ex) {
            abort(404);
        }

    }


    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
     

         $user =User::find($id);
        
        if(!empty($user))
        {   
            $roles = Role::all();
            // create country id , state id array to excute single query to retrive states, cities
            $country_array = array();
            
            $state_array = array();

            if($user->country_id)
            {
                $country_array[] = $user->country_id;    
            }

           
            if($user->state_id)
            {
                $state_array[] = $user->state_id;    
            }

          
            $countries = Country::get();

            // get states
            $country_states = State::whereIn('country_id',$country_array)->orderBy('name','ASC')->get();
            
            $country_states = object_to_array($country_states);
            
            // get states array with array key country_id
            $country_states = helper_array_column_multiple_key($country_states, array('country_id'), TRUE);

            // get cities
            $state_cities = City::whereIn('state_id',$state_array)->orderBy('name','ASC')->get();
            
            $state_cities = object_to_array($state_cities);
            
            // get cities array with array key state_id
            $state_cities = helper_array_column_multiple_key($state_cities, array('state_id'), TRUE);
            
            /*if(!empty($user->profile_pic))
            {
                $img=explode("/", $user->profile_pic);
                $user->profile_pic='users/thumbnail/'.$img[1];
                
            }*/
            $page="edit";
             $sites=\App\Warehouse::get();
              $attachments=\App\Attachment::where('user_id',$user->id)->get();
            $state_name=($user->state_id!=0)?\App\State::find($user->state_id)->name:'';
            $city_name=($user->city_id!=0)?\App\City::find($user->city_id)->name:'';
            
            
            return view('users.edit', compact(['user','roles','countries','country_states','state_cities','page','sites','attachments','state_name','city_name']));
            //return view('supplier.form',compact('page_title', 'prefix_title', 'countries', 'result', 'country_states', 'state_cities'));
        }
        else
        {
            return back()->withInput();
        }    
       
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

        $this->validate($request, [

            'name' => 'required',

            'email' => 'required|email|unique:users,email,'.$id,

            'password' => 'same:confirm-password',

            'roles' => 'required'

        ]);


        $input = $request->all();

        if(!empty($input['password'])){ 

            $input['password'] = Hash::make($input['password']);

        }else{

            $input = array_except($input,array('password'));    

        }


        $user = User::find($id);

        $user->update($input);

        DB::table('model_has_roles')->where('model_id',$id)->delete();


        $user->assignRole($request->input('roles'));


        return redirect()->route('users.index')

                        ->with('success','User updated successfully');

    }


    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        User::find($id)->delete();

        return redirect()->route('users.index')

                        ->with('success','User deleted successfully');

    }


}
