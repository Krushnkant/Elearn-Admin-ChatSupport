<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed a ready-to-use, active student account for the frontend app.
     *
     * Login (frontend http://127.0.0.1:8000/Login) with either the email
     * or the mobile number as "username", password: password123
     *
     * @return void
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Student / frontend app user -> log in at http://127.0.0.1:8000/Login
        DB::table('users')->updateOrInsert(
            ['email' => 'student@test.com'],
            [
                'name'              => 'Test Student',
                'mobile_number'     => '9999999999',
                'password'          => Hash::make('password123'),
                'role_id'           => 2, // 2 = student/app user
                'status'            => 1, // 1 = active (required to log in)
                'email_verified_at' => $now,
                'otp_verified_at'   => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );

        // Admin panel user -> log in at http://127.0.0.1:9000/admin
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@test.com'],
            [
                'name'              => 'Admin',
                'mobile_number'     => '8888888888',
                'password'          => Hash::make('admin123'),
                'role_id'           => 1, // 1 = admin
                'status'            => 1, // 1 = active
                'email_verified_at' => $now,
                'otp_verified_at'   => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );
    }
}
