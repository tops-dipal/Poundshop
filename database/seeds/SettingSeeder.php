<?php

use Illuminate\Database\Seeder;
use App\Setting;
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
                [
                'module_name' => 'billing_address',
                'column_name' => "Address1",
                'column_key' => 'address1',
                'column_val'=>'Ellisbridge',
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "Address2",
                'column_key' => 'address2',
                'column_val'=>'Ellisbridge',
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "Country",
                'column_key' => 'country',
                'column_val'=>'India',
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "State/County",
                'column_key' => 'state',
                'column_val'=>'Gujarat',
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "City",
                'column_key' => 'city',
                'column_val'=>'Ahmedabad',
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "Postcode",
                'column_key' => 'postcode',
                'column_val'=>'380006',
                ],
            
                
        ]);
    }
}
