<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\CreateRequest;
use App\Http\Requests\Api\Users\UpdateRequest;
use Illuminate\Support\Facades\View;
use App\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Imagine;
use Intervention\Image\ImageManagerStatic as Image;
use App\Events\SendMail;
use Gumlet\ImageResize;


class UsersController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:users-list|users-create|users-edit|users-delete', ['only' => ['index','store']]);

        $this->middleware('permission:users-create', ['only' => ['create','store']]);

        $this->middleware('permission:users-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:users-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
    	try
        {
        
           $columns=[
                    0 => 'id',
                    1 => 'profile_pic',
                    2 => 'first_name',
                    3 => 'email',
                    4 => 'first_name',
                    5 => 'phone_no',
                    6 => 'date_enroll',
            ];
            $params  = array(
                 'order_column'    => $columns[$request->order[0]['column']],
                 'order_dir'       => $request->order[0]['dir'],
                 'search'          => $request->search['value']
            );

            $users=User::getAllUsers($request->length, $params);
                
            $data = [];
            
            $logUser=Auth::user();
            if (!empty($users)) {
                    $data = $users->getCollection()->transform(function ($result) use ($data,$logUser)  {
                        $tempArray   = array();

                        $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                        /*if(!empty($result->profile_pic))
                        {
                            $profilePic=explode("/",$result->profile_pic);
                            $result->profile_pic=$profilePic[0]."/thumbnail/".$profilePic[1];
                        }*/
                        $tempArray[]=View::make('components.list-image',['object'=>$result])->render();
                        $tempArray[] = View::make('components.list-title',['title'=>ucwords($result->first_name." ".$result->last_name),'edit_url'=>route('users.edit',$result->id),'btn_title'=>trans('messages.user_management.user_edit')])->render();
                        $tempArray[] = $result->email;
                        $tempArray[] = ucwords($result->getRoleNames());
                        $tempArray[] = $result->phone_no;
                        $tempArray[] =($result->date_enroll!=NULL) ? Carbon::createFromFormat('Y-m-d', $result->date_enroll)->format('d-M-Y') : '-';
                        
                        $viewActionButton = View::make('users.action-buttons', ['object' => $result,'logUser'=>$logUser]);
                        $tempArray[]      =$viewActionButton->render();
                        //$viewActionButton->render();
                        return $tempArray;
                    });
                }
                
                $jsonData = [
                    "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                    "recordsTotal"    => $users->total(), // Total number of records
                    "recordsFiltered" => $users->total(),
                    "data"            => $data // Total data array
                ];
                return response()->json($jsonData);
        } 
        catch (Exception $ex) {
            return 'error';
        }
    }

   public function store(CreateRequest $request)
    {
      
        try{
               $user_model=new User;
               $user_model->first_name = $request->first_name;
               $user_model->last_name = $request->last_name;
               $user_model->email = $request->email;
               $user_model->phone_no = $request->phone_no;
               $user_model->mobile_no = $request->mobile_no;
               $user_model->comment = $request->comment;
               $user_model->emergency_contact_num = $request->emergency_contact_num;
               $user_model->emergency_contact_name = $request->emergency_contact_name;
               
               $user_model->address_line1 = $request->address_line1;
               $user_model->address_line2 = $request->address_line2;
               $user_model->country_id =  isset($request->country_id) ? $request->country_id : '0';
               if(isset($request->state_id))
               {
                    $state=\App\State::where('name',$request->state_id)->where('country_id',$request->country_id)->first();
                    if(empty($state))
                    {
                        $stateObj=new \App\State;
                        $stateObj->name=$request->state_id;
                        $stateObj->country_id=$request->country_id;
                        $stateObj->save();
                        $user_model->state_id=$stateObj->id;
                    }
                    else
                    {
                        $user_model->state_id = $state->id;
                    }
               }
               if(isset($request->city_id))
               {
                    $city=\App\City::where('name',$request->city_id)->where('state_id',$user_model->state_id)->first();
                  
                    if(empty($city))
                    {

                        $cityObj=new \App\City;
                        $cityObj->name=$request->city_id;
                        $cityObj->state_id=$user_model->state_id;
                        $cityObj->save();
                        $user_model->city_id=$cityObj->id;
                    }
                    else
                    {
                        $user_model->city_id = $city->id;
                    }
               }
               
               $user_model->zipcode = $request->zipcode;
               $dt = Carbon::now();
               $user_model->date_pass_change = $dt->toDateString();
               $user_model->date_enroll = Carbon::parse($request->date_enroll)->format('Y-m-d');
               $user_model->created_by = $request->user->id;
               $user_model->modified_by = $request->user->id;
               $user_model->site_id = $request->site_id;
               $user_model->password=bcrypt($request->password);
           
           if ($request->file('profile_pic')) {
                $folder='users';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }
                $uploadedFile =$request->file('profile_pic');
                $user_model->profile_pic_org_name=$request->file('profile_pic')->getClientOriginalName();
                $extension = strtolower($request->file('profile_pic')->getClientOriginalExtension());
                if ($extension=='jpg' || $extension=='jpeg' || $extension=='png') {
                    $orientation = exif_read_data($request->file('profile_pic'));
                }
                $name=time().'.'.$extension;
                $path = Storage::putFileAs(('users'), $uploadedFile,$name);
                 if (!empty($path)) {
                    $folder = 'users';
                    Storage::makeDirectory($folder, 0777, true);
                    $folder = 'users/thumbnail/';
                    if (!Storage::exists($folder)) {
                        Storage::makeDirectory($folder, 0777, true);
                    }
                     
                    $thumbName1 = explode('/', $path);
                    $thumbName=$thumbName1[1];
                    $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'users/' . $thumbName;

                    $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'users/thumbnail/' . $thumbName;
                    //   echo $thumbPath;exit;
                   /* $image = new ImageResize($originalPath);
                    $image->resize(100, 100, true);
                    $image->save($thumbPath);*/
                    Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                        $constraint->upsize();
                        $constraint->aspectRatio();
                    })->save($thumbPath, 100);
                    
                    Storage::delete($user_model->profile_pic);
                    if (isset($user_model->profile_pic) && !empty($user_model->profile_pic)) {
                        $thumbName = explode('/', $user_model->profile_pic)[1];
                        Storage::delete('users/thumbnail/'.$thumbName);
                    }
                    $user_model->profile_pic = $path;
                }
               /* $path = Storage::putFile(('users'), $uploadedFile);
                $user_model->profile_pic = $path;*/
            }
           if($user_model->save()){
            $request->id=$user_model->id;
            $ans=$this->saveAttachments($request);
              $emailData=array('toName'=>$request->first_name.' '.$request->last_name,'toEmail'=>$request->email,'subject'=>'Welcome To Poundshop','template'=>'emails.welcome_user','username'=>$request->email,'password'=>$request->password);
              event(new SendMail($emailData)); // send mail to user for welcome
              $role=Role::find($request->user_role);
              $user_model->assignRole($role);
               return $this->sendResponse(trans('messages.api_responses.user_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.user_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function destroy(Request $request)
    {
        $user=User::find($request->id);
        if($user->delete()){
            return $this->sendResponse(trans('messages.api_responses.user_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.user_delete_error'), 422);
        }
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->ids;
        
         if(User::whereIn('id',explode(",",$ids))->delete()){
            return $this->sendResponse(trans('messages.api_responses.users_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.users_delete_error'), 422);
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        try
       {
           $user_model= User::find($id);
           $files = $request->file('attachments');
          //dd($request->all());
           $user_model->first_name = $request->first_name;
           $user_model->last_name = $request->last_name;
           $user_model->email = $request->email;
           $user_model->phone_no = $request->phone_no;
           $user_model->address_line1 = $request->address_line1;
           $user_model->address_line2 = $request->address_line2;
           $user_model->country_id =  isset($request->country_id) ? $request->country_id : '0';
           $user_model->country_id =  isset($request->country_id) ? $request->country_id : '0';
               if(isset($request->state_id))
               {
                    $state=\App\State::where('name',$request->state_id)->where('country_id',$request->country_id)->first();
                    if(empty($state))
                    {
                        $stateObj=new \App\State;
                        $stateObj->name=$request->state_id;
                        $stateObj->country_id=$request->country_id;
                        $stateObj->save();
                        $user_model->state_id=$stateObj->id;
                    }
                    else
                    {
                        $user_model->state_id = $state->id;
                    }
               }
               if(isset($request->city_id))
               {
                    $city=\App\City::where('name',$request->city_id)->where('state_id',$user_model->state_id)->first();
                    if(empty($city))
                    {
                        $cityObj=new \App\City;
                        $cityObj->name=$request->city_id;
                        $cityObj->state_id=$user_model->state_id;
                        $cityObj->save();
                        $user_model->city_id=$cityObj->id;
                    }
                    else
                    {
                        $user_model->city_id = $city->id;
                    }
               }
           $user_model->zipcode = $request->zipcode;
           $user_model->date_enroll = Carbon::parse($request->date_enroll)->format('Y-m-d');
           $user_model->modified_by = $request->user->id;
           $user_model->mobile_no = $request->mobile_no;
           $user_model->comment = $request->comment;
           $user_model->site_id = $request->site_id;
           $user_model->emergency_contact_num = $request->emergency_contact_num;
           $user_model->emergency_contact_name = $request->emergency_contact_name;
           $ans=$this->saveAttachments($request);
           if(isset($request->password))
           {
                $user_model->password=bcrypt($request->password);
                $dt = Carbon::now();
                $user_model->date_pass_change = $dt->toDateString();
           }
            if ($request->file('profile_pic')) {
                $folder='users';
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }
                $uploadedFile =  $request->file('profile_pic');
                $user_model->profile_pic_org_name=$request->file('profile_pic')->getClientOriginalName();
                $extension = strtolower($request->file('profile_pic')->getClientOriginalExtension());
                if ($extension=='jpg' || $extension=='jpeg' || $extension=='png') {
                    $orientation = exif_read_data($request->file('profile_pic'));
                }
                $name=time().'.'.$extension;
                $path = Storage::putFileAs(('users'), $uploadedFile,$name);
                 if (!empty($path)) {
                    $folder = 'users';
                    Storage::makeDirectory($folder, 0777, true);
                    $folder = 'users/thumbnail/';
                    if (!Storage::exists($folder)) {
                        Storage::makeDirectory($folder, 0777, true);
                    }
                     
                    $thumbName1 = explode('/', $path);
                    $thumbName=$thumbName1[1];
                    $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'users/' . $thumbName;

                    $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'users/thumbnail/' . $thumbName;
                    //   echo $thumbPath;exit;
                    /*$image = new ImageResize($originalPath);
                    $image->resize(100, 100, true);
                    $image->save($thumbPath);*/
                    Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                        $constraint->upsize();
                        $constraint->aspectRatio();
                    })->save($thumbPath, 100);
                    
                    Storage::delete($user_model->profile_pic);
                    if (isset($user_model->profile_pic) && !empty($user_model->profile_pic)) {
                        $thumbName = explode('/', $user_model->profile_pic)[1];
                        Storage::delete('users/thumbnail/'.$thumbName);
                    }
                    $user_model->profile_pic = $path;
                }
               /* $path = Storage::putFile(('users'), $uploadedFile);
                $user_model->profile_pic = $path;*/
            }
           // dd($user_model);
           if($user_model->save()){
               return $this->sendResponse(trans('messages.api_responses.user_edit_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.user_edit_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function storeImage($product)
    {
        if (request()->has('profile_pic')) {
            $product->update([
                'profile_pic' => request()->profile_pic->store('users',
                    'public'),
            ]);
            $img_path = public_path('storage/uploads/users') . $product->profile_pic;
            $image = Image::make($img_path)->fit('200');
            $image->save();
        }
    }

    function removeImage(Request $request)
    {
        $user_model=User::find($request->id);
        Storage::delete($user_model->profile_pic);
        if (isset($user_model->profile_pic) && !empty($user_model->profile_pic)) {
            $thumbName = explode('/', $user_model->profile_pic)[1];
            Storage::delete('users/thumbnail/'.$thumbName);
        }
        if($user_model->update(['profile_pic'=>null]))
        {
           return $this->sendResponse(trans('messages.api_responses.user_img_delete_success'), 200);
        }
        else
        {
            return $this->sendResponse(trans('messages.api_responses.user_img_delete_error'), 422);
        }
    }

    public function saveAttachments(Request $request)
    {
         $i=0;
        $files=$request->file('attachments');
        if(!empty($files))
        {
            foreach($files as $file) {
                $storeImagearray[$i]['user_id']=$request->id;
               
                $uploadedFile =$file;
                 $folder="attachments/".$request->id;
                 $imageExtension=['jpg','JPG','JPEG','jpeg','png',"PNG"];
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder, 0777, true);
                }
                 Storage::makeDirectory($folder, 0777, true);
                $extension = strtolower($file->getClientOriginalExtension());
                
                $name=time().$i.'.'.$file->getClientOriginalExtension();
                $path = Storage::putFileAs(($folder), $uploadedFile,$name);
                
                if (!empty($path) && in_array($extension, $imageExtension)) {
            
                    $folder = "attachments/".$request->id.'/thumbnail/';
                    if (!Storage::exists($folder)) {
                        Storage::makeDirectory($folder, 0777, true);
                    }
                     
                    $thumbName1 = explode('/', $path);

                    $thumbName=$thumbName1[2];
                    
                    $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . "attachments/".$request->id.'/'.$thumbName;

                    $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() .$folder. $thumbName;
                    $image = new ImageResize($originalPath);
                    $image->resize(50, 50, true);
                    $image->save($thumbPath);
                    
                    $storeImagearray[$i]['attachment'] = $path;
                }
                else
                {
                    $storeImagearray[$i]['attachment'] = $path;
                }
               $i++;
            }
            if(count($storeImagearray)>0)
            {
               if(\App\Attachment::insert($storeImagearray))
                {
                   return 1;
                } 
            }
            else{
                return 0;
            }   
        }
        else
        {
            return 0;
        }
    }

    function deleteAttachments(Request $request)
    {
        if($request)
        {
            
            try{
                    $attachments=\App\Attachment::find($request->id);
                    if (isset($attachments->attachment) && !empty($attachments->attachment)) {
                        Storage::delete($attachments->attachment);
                        $thumbName = explode('/', $attachments->attachment)[2];
                        Storage::delete('attachments/'.$request->removeId.'/thumbnail/'.$thumbName);
                    }
                    if($attachments->delete())
                    {
                        return $this->sendResponse(trans('messages.user_management.attachment_delete_success'), 200);
                    }
                    else
                    {
                         return $this->sendError(trans('messages.common.something_wrong'), 422);
                    }
                    
            }
            catch (Exception $ex) {
                return $this->sendError($ex->getMessage(), 400);
            }
        }
        else
        {
             return $this->sendError($ex->getMessage(), 400);
        }
    }

}
