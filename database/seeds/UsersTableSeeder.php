<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\User;
use Spatie\Permission\Models\Permission;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $mytime = Carbon\Carbon::now();
       
        $user=User::create([
            'first_name' => 'Tops',
            'last_name' => 'Test',
            'email' => 'admin@poundshop.com',
            'password' => bcrypt('123456'),
            'country_id' => '1',
            'state_id' => '1',
            'city_id' => '1',
            'created_at'=>$mytime->toDateTimeString(),
           'updated_at'=>$mytime->toDateTimeString(),
        ]);
        
        $role = Role::create(['name' => 'Admin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
