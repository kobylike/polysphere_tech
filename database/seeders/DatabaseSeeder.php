<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'email' => 'samuel.oatuahene@gmail.com',
            'name'              => 'Samuel Atuahene',
            'username'          => 'kobylike',
            'password'          => Hash::make('0251700gJ@#*'),
            'phone'             => '+233592991453',
            'email_verified_at' => now(),
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class
        ]);
    }
}
