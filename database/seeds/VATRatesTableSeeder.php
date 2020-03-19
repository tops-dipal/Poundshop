<?php

use Illuminate\Database\Seeder;

class VATRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('settings')->insert([[
            'module_name' => 'vat_rates',
            'column_name' => "Standard VAT Rate(%)",
            'column_key' => 'standard_vat_rate',
            'column_val'=>'20',
        ],[
            'module_name' => 'vat_rates',
            'column_name' => "Zero Rated(%)",
            'column_key' => 'zero_rated',
            'column_val'=>'0',
        ]]);
    }
}
