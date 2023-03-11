<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'admin']);
        Permission::create(['name' => 'supplier']);
        Permission::create(['name' => 'farmer']);
        Permission::create(['name' => 'professional']);
        Permission::create(['name' => 'customer']);
        Permission::create(['name' => 'guest']);

        Role::create(['name' => 'admin'])->givePermissionTo(['admin', 'supplier', 'farmer', 'professional', 'customer', 'guest']);
        Role::create(['name' => 'supplier'])->givePermissionTo(['supplier', 'farmer', 'professional', 'customer', 'guest']);
        Role::create(['name' => 'farmer'])->givePermissionTo(['farmer', 'professional', 'customer', 'guest']);
        Role::create(['name' => 'professional'])->givePermissionTo(['supplier', 'farmer', 'professional', 'customer', 'guest']);
        Role::create(['name' => 'customer'])->givePermissionTo(['customer', 'guest']);
        Role::create(['name' => 'guest'])->givePermissionTo(['guest']);


    }
}
