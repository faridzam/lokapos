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
            pos_cashier_desktop_seeder::class,
            pos_pc_desktop_seeder::class,
            pos_store_desktop_seeder::class,
        ]);
    }
}
