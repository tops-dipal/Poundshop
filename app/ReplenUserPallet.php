<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplenUserPallet extends Model
{
    protected $table = 'replen_user_pallet';

    public static function getPalletPickLocation($warehouse_id='',$user_id='')
    {
        $query = ReplenUserPallet::select("replen_user_pallet.id","replen_user_pallet.location_id");
        
        $query->selectRaw("locations_master.type_of_location,locations_master.location");

        $query->leftJoin('locations_master', function($q) {
            $q->on('replen_user_pallet.location_id', 'locations_master.id');            
        });       
        
        $query->where('replen_user_pallet.warehouse_id', $warehouse_id);

        if(!empty($user_id))
        {
            $query->where('replen_user_pallet.user_id', $user_id);        
        }
        
        return $query->get();
    }
}
