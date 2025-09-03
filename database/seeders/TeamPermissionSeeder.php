<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Helpers\FakerHelper;
use App\Models\Admin;
use App\Models\TeamPermission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TeamPermissionSeeder extends Seeder
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

        for($i=0;$i<20;$i++)
        {
            $newPerm =new TeamPermission();
            $newPerm->name=str::random(5);
            $newPerm->save();
        }


    }
}
