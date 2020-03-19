<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\User;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
       
        $inserted_ids = [];

        // User Roles
        $permissions[] = [
                            'name' => 'role-list',
                            'parent_caption' => 'User Roles',
                         ];

        $permissions[] = [
                            'name' => 'role-create',
                            'parent_id' => 'role-list',
                         ];   

        $permissions[] = [
                            'name' => 'role-edit',
                            'parent_id' => 'role-list',
                         ];

        $permissions[] = [
                            'name' => 'role-delete',
                            'parent_id' => 'role-list',
                         ];                                                   
        
        // Supplier Module
        $permissions[] = [
                            'name' => 'supplier-list',
                            'parent_caption' => 'Suppliers',
                         ];

        $permissions[] = [
                            'name' => 'supplier-create',
                            'parent_id' => 'supplier-list',
                         ];   

        $permissions[] = [
                            'name' => 'supplier-edit',
                            'parent_id' => 'supplier-list',
                         ];

        $permissions[] = [
                            'name' => 'supplier-delete',
                            'parent_id' => 'supplier-list',
                          ];


        // Pallets Module
        $permissions[] = [
                            'name' => 'pallet-list',
                            'parent_caption' => 'Pallet Management',
                         ];

        $permissions[] = [
                            'name' => 'pallet-create',
                            'parent_id' => 'pallet-list',
                         ];   

        $permissions[] = [
                            'name' => 'pallet-edit',
                            'parent_id' => 'pallet-list',
                         ];

        $permissions[] = [
                            'name' => 'pallet-delete',
                            'parent_id' => 'pallet-list',
                          ];
        
        
        //Cartons
         $permissions[] = [
                            'name' => 'cartons-list',
                            'parent_caption' => 'Cartons Management',
                         ];

        $permissions[] = [
                            'name' => 'cartons-create',
                            'parent_id' => 'cartons-list',
                         ];   

        $permissions[] = [
                            'name' => 'cartons-edit',
                            'parent_id' => 'cartons-list',
                         ];

        $permissions[] = [
                            'name' => 'cartons-delete',
                            'parent_id' => 'cartons-list',
                          ];

        //Totes
         $permissions[] =[
                            'name'=>'totes-list',
                            'parent_caption'=>'Totes Management',
                        ];
         $permissions[] = [
                            'name' => 'totes-create',
                            'parent_id' => 'totes-list',
                         ];   

        $permissions[] = [
                            'name' => 'totes-edit',
                            'parent_id' => 'totes-list',
                         ];

        $permissions[] = [
                            'name' => 'totes-delete',
                            'parent_id' => 'totes-list',
                          ];

        //user managenet

        $permissions[] =[
                            'name'=>'users-list',
                            'parent_caption'=>'User Management',
                        ];
         $permissions[] = [
                            'name' => 'users-create',
                            'parent_id' => 'users-list',
                         ];   

        $permissions[] = [
                            'name' => 'users-edit',
                            'parent_id' => 'users-list',
                         ];

        $permissions[] = [
                            'name' => 'users-delete',
                            'parent_id' => 'users-list',
                          ];

         //Range managenet

        $permissions[] =[
                            'name'=>'range-list',
                            'parent_caption'=>'Range Management',
                        ];
         $permissions[] = [
                            'name' => 'range-create',
                            'parent_id' => 'range-list',
                         ];   

        $permissions[] = [
                            'name' => 'range-edit',
                            'parent_id' => 'range-list',
                         ];

        $permissions[] = [
                            'name' => 'range-delete',
                            'parent_id' => 'range-list',
                          ];
        
        //Purchase Order
        $permissions[] =[
                            'name'=>'po-list',
                            'parent_caption'=>'Purchase Order Management',
                        ];
         $permissions[] = [
                            'name' => 'po-create',
                            'parent_id' => 'po-list',
                         ];   

        $permissions[] = [
                            'name' => 'po-edit',
                            'parent_id' => 'po-list',
                         ];

        $permissions[] = [
                            'name' => 'po-delete',
                            'parent_id' => 'po-list',
                          ];

        //Purchase Order
        $permissions[] =[
                            'name'=>'warehouse-list',
                            'parent_caption'=>'Warehouse Management',
                        ];
         $permissions[] = [
                            'name' => 'warehouse-create',
                            'parent_id' => 'warehouse-list',
                         ];   

        $permissions[] = [
                            'name' => 'warehouse-edit',
                            'parent_id' => 'warehouse-list',
                         ];

        $permissions[] = [
                            'name' => 'warehouse-delete',
                            'parent_id' => 'warehouse-list',
                          ];


        //locations Order
        $permissions[] =[
                            'name'=>'locations-list',
                            'parent_caption'=>'Locations Management',
                        ];
         $permissions[] = [
                            'name' => 'locations-create',
                            'parent_id' => 'locations-list',
                         ];   

        $permissions[] = [
                            'name' => 'locations-edit',
                            'parent_id' => 'locations-list',
                         ];

        $permissions[] = [
                            'name' => 'locations-delete',
                            'parent_id' => 'locations-list',
                          ];

        //commodity codes
        $permissions[] =[
                            'name'=>'commoditycode-list',
                            'parent_caption'=>'Commodity Codes',
                        ];
         $permissions[] = [
                            'name' => 'commoditycode-create',
                            'parent_id' => 'commoditycode-list',
                         ];   

        $permissions[] = [
                            'name' => 'commoditycode-edit',
                            'parent_id' => 'commoditycode-list',
                         ];

        $permissions[] = [
                            'name' => 'commoditycode-delete',
                            'parent_id' => 'commoditycode-list',
                          ];

        //Import duty
        $permissions[] =[
                            'name'=>'importduty-list',
                            'parent_caption'=>'Import Duty',
                        ];
         $permissions[] = [
                            'name' => 'importduty-create',
                            'parent_id' => 'commoditycode-list',
                         ];   

        $permissions[] = [
                            'name' => 'importduty-edit',
                            'parent_id' => 'commoditycode-list',
                         ];

        $permissions[] = [
                            'name' => 'importduty-delete',
                            'parent_id' => 'commoditycode-list',
                          ];


         //Inventory Management
        $permissions[] =[
                            'name'=>'product-list',
                            'parent_caption'=>'Inventory Management',
                        ];
         $permissions[] = [
                            'name' => 'product-create',
                            'parent_id' => 'commoditycode-list',
                         ];   

        $permissions[] = [
                            'name' => 'product-edit',
                            'parent_id' => 'commoditycode-list',
                         ];

        $permissions[] = [
                            'name' => 'product-delete',
                            'parent_id' => 'commoditycode-list',
                          ];  


        //Category  Mapping
        $permissions[] =[
                            'name'=>'categorymapping-list',
                            'parent_caption'=>'Category Mapping',
                        ];
         $permissions[] = [
                            'name' => 'categorymapping-create',
                            'parent_id' => 'categorymapping-list',
                         ];   

        $permissions[] = [
                            'name' => 'categorymapping-edit',
                            'parent_id' => 'categorymapping-list',
                         ];

        $permissions[] = [
                            'name' => 'categorymapping-delete',
                            'parent_id' => 'categorymapping-list',
                          ];  

        //Category  Mapping
        $permissions[] =[
                            'name'=>'listingmanager-list',
                            'parent_caption'=>'Listing Manager',
                        ];
         $permissions[] = [
                            'name' => 'listingmanager-create',
                            'parent_id' => 'listingmanager-list',
                         ];   

        $permissions[] = [
                            'name' => 'listingmanager-edit',
                            'parent_id' => 'listingmanager-list',
                         ];

        $permissions[] = [
                            'name' => 'listingmanager-delete',
                            'parent_id' => 'listingmanager-list',
                          ];                  

        
        foreach ($permissions as $permission) 
        {
          if(isset($permission['parent_id']))
          {
            $permission['parent_id'] = !empty($inserted_ids[$permission['parent_id']]) ? $inserted_ids[$permission['parent_id']] : NULL;
          } 

          $inserted_ids[$permission['name']] = Permission::create($permission)->id;
        }
        
        $admin_role = Role::where('name', 'Admin')->first();

        if(!empty($admin_role))
        {  
          $admin_role->syncPermissions($inserted_ids);
        }
    }
}
