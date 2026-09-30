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
        // User::factory(10)->create();


        // ============ 经络穴道数据（renti） ============
        $this->call([
            MeridianSeeder::class,
            MeridianLineSeeder::class,
            AcupointSeeder::class,
            AcupointDescSeeder::class,
            ModelSeeder::class,
        ]);
    }
}
