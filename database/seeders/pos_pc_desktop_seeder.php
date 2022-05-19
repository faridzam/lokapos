<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class pos_pc_desktop_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('pos_pc_desktops')->insert([
            'store_id' => 1,
            'name' => 'Zam',
            'ip_address' => '192.168.0.139',
            'cashier_printer' => 'IT-01',
            'kitchen_printer' => '10.154.30.xxx',
            'bar_printer' => '10.154.30.xxx',
        ]);
    }
}
