<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create admin user for login purpose
        User::firstOrCreate(['email' => 'admin@merodiscount.com'], [
            'name' => 'admin',
            'email' => 'admin@merodiscount.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        //create normal user for login purpose
        User::firstOrCreate(['email' => 'user@merodiscount.com'], [
            'name' => 'user',
            'email' => 'user@merodiscount.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }
}
