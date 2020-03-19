<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VariationThemes extends Model
{
	protected $fillable = ['variation_theme_name','variation_theme_1','variation_theme_2','combination_type','created_at'];
    protected $table = 'variation_themes';
}
