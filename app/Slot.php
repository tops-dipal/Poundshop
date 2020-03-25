<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slot extends Model {

    use SoftDeletes;

    protected
            $fillable = ['deleted_at'];
    public
            $timestamps  = true;

    /**
     *
     */
    public static
            function getSlots() {
        return self::orderBy('from')->get();
    }

}
