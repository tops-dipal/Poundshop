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
     
        $insertDate=date('Y-m-d H:i:s');
        $insertupdateArr=array( [
                'module_name' => 'billing_address',
                'column_name' => "Address1",
                'column_key' => 'address1',
                'column_val'=>'Ellisbridge',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "Address2",
                'column_key' => 'address2',
                'column_val'=>'Ellisbridge',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "Country",
                'column_key' => 'country',
                'column_val'=>'India',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "State/County",
                'column_key' => 'state',
                'column_val'=>'Gujarat',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "City",
                'column_key' => 'city',
                'column_val'=>'Ahmedabad',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'billing_address',
                'column_name' => "Postcode",
                'column_key' => 'postcode',
                'column_val'=>'380006',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'po_bookings',
                'column_name' => "Days Remaining Before Short Dated",
                'column_key' => 'short_date',
                'column_val'=>'5',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'others',
                'column_name' => "Days Remaining To Start Season",
                'column_key' => 'default_season_start',
                'column_val'=>'5',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ],
                [
                'module_name' => 'others',
                'column_name' => "Default Day Promotion About To Start",
                'column_key' => 'default_promotion_start',
                'column_val'=>'5',
                'created_at' =>$insertDate,
                'updated_at'    =>$insertDate
                ]);
        $insertArr=array();

        foreach ($insertupdateArr as $key => $value) {
            $fetchRecord=Setting::where('column_key',$value['column_key'])->first();
            if(!empty($fetchRecord))
            {
                $fetchRecord->column_val=$value['column_val'];
                $fetchRecord->updated_at=date('Y-m-d H:i:s');
                $fetchRecord->save();
            }
            else
            {
                array_push($insertArr,$value);
            }
        }
        if(count($insertArr)>0)
        {
            Setting::insert($insertArr);
        }
    }
}
