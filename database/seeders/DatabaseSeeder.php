<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'name' => 'spv',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        User::create([
            'username' => 'superadmin',
            'password' => bcrypt('superadmin'),
            'role' => 'superadmin',
            'name' => 'Kepala Lab ICT',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Seed sample activity logs
        $this->call(ActivityLogSeeder::class);

    }
}
