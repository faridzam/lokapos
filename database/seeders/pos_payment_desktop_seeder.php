<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class pos_payment_desktop_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('pos_payment_desktops')->insert([
                [
                    'name' => 'TUNAI',
                ],
                [
                    'name' => 'FINTECH',
                ],
                [
                    'name' => 'BCA DEBIT',
                ],
                [
                    'name' => 'BCA KREDIT',
                ],
                [
                    'name' => 'BNI DEBIT',
                ],
                [
                    'name' => 'BNI KREDIT',
                ],
                [
                    'name' => 'BRI DEBIT',
                ],
                [
                    'name' => 'BRI KREDIT',
                ],
                [
                    'name' => 'MANDIRI DEBIT',
                ],
                [
                    'name' => 'MANDIRI KREDIT',
                ],
            ]
        );

    }
}
