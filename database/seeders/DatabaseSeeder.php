<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\User\Persistence\Models\UserModel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserModel::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
