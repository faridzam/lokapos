<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class pos_cashier_desktop_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('pos_cashier_desktop')->insert([
            'name' => 'Zam',
            'username' => 'faridzam',
            'email' => 'zamtechcorp@gmail.com',
            'password' => Hash::make('zamganteng'),
        ]);
    }
}
