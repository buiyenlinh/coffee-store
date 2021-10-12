<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role->name = 'Administrator';
        $role->level = 1;
        $role->save();

        $role1 = new Role();
        $role1->name = 'Manager';
        $role1->level = 2;
        $role1->save();

        $role2 = new Role();
        $role2->name = 'Employee';
        $role2->level = 3;
        $role2->save();
    }
}
