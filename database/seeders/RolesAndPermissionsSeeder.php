<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'assign-role',
            'role',
            'roles',
            'create-role',
            'update-role',
            'delete-role',
            // hotels
            'create-hotel',
            'update-hotel',
            'delete-hotel',
            // reserves
            'reserves',
            'reserve',
            // rooms
            'create-room',
            'update-room',
            'delete-room',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        // Assign the admin role to the first user if exists
        $user = User::first();
        if ($user) {
            $user->assignRole(Role::first());
        } else {
            $this->command->info('No user found to assign the admin role.');
        }
    }
}
