<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create-admin', 'guard_name' => 'api']);
        Permission::create(['name' => 'create-role', 'guard_name' => 'api']);
        Permission::create(['name' => 'add-user', 'guard_name' => 'api']);
        Permission::create(['name' => 'create-task', 'guard_name' => 'api']);
        Permission::create(['name' => 'edit-task', 'guard_name' => 'api']);
        Permission::create(['name' => 'delete-task', 'guard_name' => 'api']);
        Permission::create(['name' => 'assign-task', 'guard_name' => 'api']);

        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'api'])
            ->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'api'])
            ->givePermissionTo(Permission::whereNot('name','create-admin')->get());

        $member = Role::create(['name' => 'Member', 'guard_name' => 'api'])
            ->givePermissionTo('edit-task');

        
        
        User::where('name', 'Super Admin')->first()->assignRole($superAdmin);
        User::where('name', 'Admin')->first()->assignRole($admin);

    }
}
