<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            user_seeder::class,
            pos_kasir_desktop_seeder::class,
            pos_store_desktop_seeder::class,
        ]);
    }
}
