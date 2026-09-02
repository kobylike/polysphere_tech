<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ─── 1. Seed roles & permissions ────────────────────────────────
        $this->call(RolesAndPermissionsSeeder::class);

        // ─── 2. Create the CEO / Super Admin user ───────────────────────
        $user = User::updateOrCreate(
            ['email' => 'samuel.oatuahene@gmail.com'],
            [
                'name'              => 'Samuel Atuahene',
                'username'          => 'kobylike',
                'password'          => Hash::make('0251700gJ@#*'), // CHANGE THIS!
                'phone'             => '+233592991453',
                'email_verified_at' => now(),
                'status'            => 'active',
            ]
        );

        // ─── 3. Assign the Super Admin role ─────────────────────────────
        $user->assignRole('Super Admin');

        // ─── 4. Create or update the user's profile ─────────────────────
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'about_me' => 'CEO & Founder of Polysphere Tech. '
                    . 'Passionate about technology, innovation, and building '
                    . 'products that make a difference in the world.',
                'position'          => 'CEO / Founder',
                'is_featured_team'  => true,
                'is_spotlight'      => true,
                'display_order'     => 1,
                'skills' => [
                    ['name' => 'Leadership & Strategy', 'level' => 100],
                    ['name' => 'Software Architecture',  'level' => 90],
                    ['name' => 'Product Management',     'level' => 85],
                    ['name' => 'Cloud Infrastructure',   'level' => 80],
                    ['name' => 'Team Building',          'level' => 95],
                ],
                'education' => [
                    [
                        'institution' => 'Massachusetts Institute of Technology',
                        'degree'      => 'PhD in Computer Science',
                        'start_year'  => '2012',
                        'end_year'    => '2018',
                        'currently_studying' => false,
                    ],
                    [
                        'institution' => 'Stanford University',
                        'degree'      => 'MBA in Technology Management',
                        'start_year'  => '2010',
                        'end_year'    => '2012',
                        'currently_studying' => false,
                    ],
                ],
                'social_links' => [
                    'linkedin' => 'https://linkedin.com/in/samuel-atuahene',
                    'github'   => 'https://github.com/kobylike',
                    'twitter'  => 'https://twitter.com/kobylike',
                    'youtube'  => 'https://youtube.com/@polyspheretech',
                ],
            ]
        );

        // ─── 5. (Optional) Seed additional users ─────────────────────────
        // User::factory(10)->create();
    }
}
