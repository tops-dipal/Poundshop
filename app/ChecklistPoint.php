<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistPoint extends Model
{
    use SoftDeletes; 
	public $table="checklist_points";
	protected $fillable = ['title'];
	 public function qcchecklist()
    {
        return $this->belongsTo('App\QCChecklist','qc_id');
    }
}
