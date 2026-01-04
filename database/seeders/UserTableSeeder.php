<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Surya#2801'),
            'api_token' => sha1('digitalMeter@123'),
            'phone' => '8506842145',
            'company' => 'developer',
            'user_role' => 'superadmin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
