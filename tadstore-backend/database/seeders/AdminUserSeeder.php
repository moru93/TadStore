<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@tadstore.com',
            'password' => Hash::make('4321tadstore'),
            'role' => 'admin'
        ]);
    }
}
