<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Phones, laptops, chargers, and other electronic items.'],
            ['name' => 'Documents', 'slug' => 'documents', 'description' => 'Student cards, certificates, books, and papers.'],
            ['name' => 'Bags', 'slug' => 'bags', 'description' => 'Backpacks, tote bags, wallets, and cases.'],
            ['name' => 'Keys', 'slug' => 'keys', 'description' => 'Vehicle, room, locker, and keychain items.'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Jackets, uniforms, hats, and accessories.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'status' => 'active']
            );
        }

        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password123'),
            'role' => UserRole::Admin->value,
        ]);

        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password123'),
            'role' => UserRole::User->value,
        ]);
    }
}
