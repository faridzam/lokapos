<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class pos_store_desktop_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('pos_store_desktops')->insert([
            'name' => 'Zam',
            'type' => 3,
            'area' => 3,
        ]);
    }
}
