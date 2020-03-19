<?php

use Illuminate\Database\Seeder;

class TotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$array = [1.4, 2.5, 3.6, 4.7, 5.8];
    	$length=Arr::random($array);
    	$width=Arr::random($array);
    	$height=Arr::random($array);
    	$max_volume=$length*$width*$height;
    	$weightArr=[10,20,30,40,50];
    	$quantityArr=[50,100,150,120,200];
    	$catArr=[1,2,3];
         DB::table('totes_master')->insert([
            'name' => Str::random(10),
            'length' => $length,
            'width' => $width,
            'height'=>$height,
            'max_volume'=>$max_volume,
            'max_weight'=>Arr::random($weightArr),
            'quantity'=>Arr::random($quantityArr),
            'category'=>Arr::random($catArr),
            'created_by'=>1,
            'modified_by'=>1
        ]);
    }
}
