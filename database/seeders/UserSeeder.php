<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_name' => 'Admin',
            'user_email' => 'admin@gmail.com',
            'user_phone' => '1234567890',
            'password' => bcrypt('1234'),
            'user_detail' => 'Admin details here',
            'permit_type' => '2,3,4,5,6,7,8,9,10,11',
            'user_id' => '1',
        ]);
    }
}
