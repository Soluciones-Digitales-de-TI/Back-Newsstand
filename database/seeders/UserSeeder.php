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
        User::factory(10)->create();
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'admin' => 1
        ]);
        User::create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('12345678'),
            'admin' => 1
        ]);
        User::create([
            'name' => 'client',
            'email' => 'client@gmail.com',
            'password' => Hash::make('12345678'),
            'admin' => 0
        ]);
    }
}
