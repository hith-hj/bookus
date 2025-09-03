<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Helpers\FakerHelper;
use App\Models\Admin;
use App\Models\Center;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $f = new FakerHelper();
        $height = rand(600, 900);
        $width = intval($height * 2 / 3);


        $admin = Admin::where('email', 'admin@luxury.com')->first();
        if (!$admin)
            DB::table('admins')->insert([
                'phone_number'      => '036963555',
                'status'            =>1,
                'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'first_name'        =>'Admin',
                'last_name'         =>'Admin',
                'email'             =>'Admin@luxury.com',
                "is_admin"          =>true,
                "center_id"         =>null,
                'center_position'   =>null,
                'address'           =>Str::random(10),
            ]);

        $user = Admin::where('email','Admin@luxury.com')->first();

        $Admin =   Role::create(['name' => 'Admin']);
        \Log::debug("bundle: " . 1);
        foreach ( config('permission.permissions') as $permission) {
            Permission::findOrCreate($permission);
            $Admin->givePermissionTo($permission);
        }
        $user->assignRole('Admin');
    // add center 
        $center=Center::where('email','center@LuxuryServe.com')->first();
        if(!$center)
        DB::table('centers')->insert([
            'name'=>'center',
            'email'=>'center@LuxuryServe.com',
            'status'=>true,
            'about'=>'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Recusandae modi nemo odio veritatis mollitia vero, esse maiores magni! Quod harum quidem iusto aliquid laboriosam quae debitis placeat necessitatibus corrupti rem.',
            'latitude'=>'00000000',
            'longitude'=>'1111111',
            'currency'=>'AAD'
        ]);
        $center=Center::where('email','center@LuxuryServe.com')->first();
    //add logo to center
        $image=new Image();
        $image->logo= $f->imageUrl(1920, 725, 'NOT USED', false);
        $image->center_id =$center->id;
        $image->save();



    // Add center Owner
        $centerOwner = Admin::where('email', 'centerOwner@luxury.com')->first();
        if (!$centerOwner)
            DB::table('admins')->insert([
                'phone_number'      => '09000000',
                'status'            =>1,
                'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'first_name'        =>'center owner',
                'last_name'         =>'center owner',
                'email'             =>'centerOwner@luxury.com',
                "is_admin"          =>false,
                "center_id"         =>$center->id,
                'center_position'   =>'owner',
                'address'           =>Str::random(10),
            ]);

        $centerOwner = Admin::where('email','centerOwner@luxury.com')->first();

        // $Admin =   Role::create(['name' => 'Admin']);
        $Center =   Role::create(['name' => 'Center']);
        
        foreach ( config('permission.center_permission') as $permission) {
            Permission::findOrCreate($permission);
            $Center->givePermissionTo($permission);
        }
        $centerOwner->assignRole('Center');




    }
}
