<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin PPIC',
            'email' => 'admin@ppic.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'MR Peroniks',
            'email' => 'mr@peroniks.com',
            'password' => bcrypt('password123'),
        ]);

        $this->call([
            ProductionDummySeeder::class,
        ]);
    }
}
