<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt('password'),  // You should use hashed passwords
                'Tanggal Lahir' => '1990-01-01',
                'Kelamin' => 'Male',
                'Alamat' => 'Jl. Admin No.1',
                'Acces' => 'admin',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member User',
                'email' => 'member@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt('password'),
                'Tanggal Lahir' => '1995-05-05',
                'Kelamin' => 'Female',
                'Alamat' => 'Jl. Member No.5',
                'Acces' => 'member',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
    }
