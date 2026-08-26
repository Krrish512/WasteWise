<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@wastewise.in';

        // Check if an existing normal user uses this email address before creating/updating admin
        $existingUser = User::where('email', $email)->first();
        if ($existingUser && !$existingUser->is_admin) {
            throw new RuntimeException("Cannot seed admin account: Email {$email} is already in use by a normal non-admin user.");
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'WasteWise Admin',
                'password' => Hash::make('WasteWise@123'),
                'is_admin' => true,
            ]
        );
    }
}
