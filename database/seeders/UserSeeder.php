<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@localhost';
        $admin->password = Hash::make('password');
        $admin->phone = '08012345674';
        $admin->location = 'Bamenda';
        $admin->save();
        $admin->assignRole('admin');

        $supplier = new User();
        $supplier->name = 'Supplier';
        $supplier->email = 'supplier@localhost';
        $supplier->password = Hash::make('password');
        $supplier->phone = '08012345675';
        $supplier->location = 'Bambili';
        $supplier->save();
        $supplier->assignRole('supplier');

        $farmer = new User();
        $farmer->name = 'Farmer';
        $farmer->email = 'farmer@localhost';
        $farmer->password = Hash::make('password');
        $farmer->phone = '08012345676';
        $farmer->location = 'Bali';
        $farmer->save();
        $farmer->assignRole('farmer');

        $professional = new User();
        $professional->name = 'Professional';
        $professional->email = 'professional@localhost';
        $professional->password = Hash::make('password');
        $professional->phone = '08012345677';
        $professional->location = 'Bali';
        $professional->save();
        $professional->assignRole('professional');

        $customer = new User();
        $customer->name = 'Customer';
        $customer->email = 'customer@localhost';
        $customer->password = Hash::make('password');
        $customer->phone = '08012345678';
        $customer->location = 'Bali';
        $customer->save();
        $customer->assignRole('customer');


    }
}
