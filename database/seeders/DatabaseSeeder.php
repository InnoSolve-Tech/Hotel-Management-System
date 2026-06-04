<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $superAdminRole = Role::query()->firstWhere('name', 'SuperAdmin');

        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@hot-l.test')],
            [
                'name' => 'Hot-L Admin',
                'email_verified_at' => now(),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'Status' => 1,
                'Role' => 'SuperAdmin',
                'role_id' => $superAdminRole?->id,
            ]
        );

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
