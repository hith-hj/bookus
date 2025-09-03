<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CenterCategory;
use App\Models\CenterService;
use App\Models\Contact;
use App\Models\OpenDay;
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

class CenterSeeder extends Seeder
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
//
//
//        $admin = Admin::where('email', 'admin@luxury.com')->first();
//        if (!$admin)
//            DB::table('admins')->insert([
//                'phone_number'      => '036963555',
//                'status'            =>1,
//                'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
//                'first_name'        =>'Admin',
//                'last_name'         =>'Admin',
//                'email'             =>'Admin@luxury.com',
//                "is_admin"          =>true,
//                "center_id"         =>null,
//                'center_position'   =>null,
//                'address'           =>Str::random(10),
//            ]);

//        $user = Admin::where('email','Admin@luxury.com')->first();
//
//        $Admin =   Role::create(['name' => 'Admin']);
        \Log::debug("bundle: " . 1);
//        foreach ( config('permission.permissions') as $permission) {
//            Permission::findOrCreate($permission);
//            $Admin->givePermissionTo($permission);
//        }
//        $user->assignRole('Admin');
    // add center
        for ($i=20; $i<30;$i++)
        {
            $category=new Category();
            $category->name='cat ' . $i;
            $category->status=1;
            $category->image=$f->imageUrl(1920, 725, 'NOT USED', false);;
            $category->save();
        }
        for ($i=20;$i<30;$i++)
        {
            //create center
            DB::table('centers')->insert([
                'id'=>$i,
                'name'=>'center',
                'email'=>'center'. $i .'@LuxuryServe.com',
                'status'=>true,
                'about'=>'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Recusandae modi nemo odio veritatis mollitia vero, esse maiores magni! Quod harum quidem iusto aliquid laboriosam quae debitis placeat necessitatibus corrupti rem.',
                'latitude'=>rand(1000000,9000000),
                'longitude'=>rand(1000000,9000000),
                'currency'=>'AAD'
            ]);
            //create owner
            DB::table('admins')->insert([
                'phone_number'      => '09000000' . $i,
                'status'            =>1,
                'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'first_name'        =>'center'.$i. ' owner',
                'last_name'         =>'center'. $i .' owner',
                'email'             =>'centerOwner'.$i.'@luxury.com',
                "is_admin"          =>false,
                "center_id"         =>$i,
                'center_position'   =>'owner',
                'address'           =>Str::random(10),
            ]);

            $centerOwner = Admin::where('email','centerOwner'.$i.'@luxury.com')->first();

            // $Admin =   Role::create(['name' => 'Admin']);
            $Center =   Role::whereName( 'Center')->first();

//            foreach ( config('permission.center_permission') as $permission) {
//                Permission::findOrCreate($permission);
//                $Center->givePermissionTo($permission);
//            }
            $centerOwner->assignRole('Center');

            //image
            $image=new Image();
            $image->logo= $f->imageUrl(1920, 725, 'NOT USED', false);
            $image->center_id =$i;
            $image->save();
            //open day
            $day = new OpenDay();
            $day->day="Wednesday";
            $day->open='01:58:00';
            $day->close='05:58:00';
            $day->center()->associate($i);
            $day->save();

            $contact = new Contact();
            $contact->key="TELEGRAM";
            $contact->value='/telegram/'. $i;
            $contact->center()->associate($i);
            $contact->save();
                for($j=20;$j<26;$j++){


            $category=new CenterCategory();
            $category->name='cat ' . $j;
            $category->status=1;
//            $category->id=$i;
            $category->center_id=$i;
            $category->image=$f->imageUrl(1920, 725, 'NOT USED', false);;
            $category->save();

            for ($x=0;$x <4;$x++)
            {
                $service =new CenterService();
                $service->Treatment_type='';
        $service->Aftercare_description='Lorem ipsum, dolor sit amet consectetur adipisicing elit. Recusandae modi nemo odio veritatis mollitia vero, esse maiores magni! Quod harum quidem iusto aliquid laboriosam quae debitis placeat necessitatibus corrupti rem.';
        $service->service_gender=$x<3?'Females':'Males';
        $service->online_booking=1;
        $service->Duration=$x<3?'30min':'45min';
        $service->price_type='Fixed';
        $service->retail_price=$x<3?100000:200000;
        $service->extra_time=0;
        $service->name='service'.$j.'-'.$x;
        $service->description='Lorem ipsum, dolor sit amet consectetur adipisicing elit. Recusandae modi nemo odio veritatis mollitia vero, esse maiores magni! Quod harum quidem iusto aliquid laboriosam quae debitis placeat necessitatibus corrupti rem.';
        $service->center_category_id=$category->id;
        $service->status=1;
        $service->center_id=$i;
        $service->save();
            }
                }

        }




    }
}
