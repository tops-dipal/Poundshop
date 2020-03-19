<?php

use Illuminate\Database\Seeder;
use App\VariationThemes;

class VariationThemeMaster extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$insert_array[] = array( 'variation_theme_name' => 'Color', 'variation_theme_1' => 'Color', 'variation_theme_2' => NULL, 'combination_type'=> '1');
    	
    	$insert_array[] = array( 'variation_theme_name' => 'Size', 'variation_theme_1' => 'Size', 'variation_theme_2' => NULL, 'combination_type'=> '1');


		$insert_array[] = array( 'variation_theme_name' => 'Color - Size', 'variation_theme_1' => 'Color', 'variation_theme_2' =>'Size', 'combination_type'=> '2');

		VariationThemes::insert($insert_array);
    }
}
