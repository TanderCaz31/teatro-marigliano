<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'role' => RoleEnum::ADMIN,
        ]);
        User::factory()->create([
            'name' => 'member',
            'email' => 'member@gmail.com',
            'role' => RoleEnum::MEMBER,
        ]);
        User::factory()->count(10)->create();
    }
}
