<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Storage;

class User extends Authenticatable
{

    use SoftDeletes; 
    use HasApiTokens,Notifiable,HasRoles;
    public
            function getProfilePicAttribute() {
        if (!empty($this->attributes['profile_pic']))
            {
                if(file_exists(Storage::path($this->attributes['profile_pic'])))
                return url('/storage/uploads') . '/' . $this->attributes['profile_pic'];
                else
                return url('/img/userplaceholder.png');
            }
        else
            return url('/img/userplaceholder.png');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'email', 'password','first_name','last_name','gender','address_line1','address_line2','country_id','state_id','city_id','zipcode','profile_pic_org_name','profile_pic','phone_no','date_pass_change','date_enroll','created_by','modified_by','last_login_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
//    protected $casts = [
//        'email_verified_at' => 'datetime',
//    ];

   

   
    public static function getAllUsers($perPage = '',
                                $params = array()){
        
        $object=self::select('users.*','warehouse_master.name as site_name');
        $object->orderBy($params['order_column'],$params['order_dir']);
        if (!empty($params['search'])) {
           $totesOb->orWhere('first_name','like',"%".$params['search']."%");
           $totesOb->orWhere('last_name','like',"%".$params['search']."%");
           $totesOb->orWhere('email','like',"%".$params['search']."%");
           $totesOb->orWhere('phone_no','like',"%".$params['search']."%");
           
          //$totesOb->orWhere('date_enroll','like',"%".$params['search']."%");
        }
        $object->leftJoin('warehouse_master',function($join){
            $join->on('warehouse_master.id','users.site_id');
        });
        return $object->paginate($perPage);
    }
}
