<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class pos_payment_store_desktop_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pos_payment_store_desktops')->insert(
            [
                [
                    'payment_id' => 1,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 2,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 3,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 4,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 5,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 6,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 7,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 8,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 9,
                    'store_id' => 1,
                ],
                [
                    'payment_id' => 10,
                    'store_id' => 1,
                ],
            ]
        );
    }
}
