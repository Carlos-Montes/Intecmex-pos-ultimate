<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB, Hash;
class FirstSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Carlos Montes',
            'email' => 'carlos_montes@jafra.com.mx',
            'password' => Hash::make('password'),
            'gender' => '2'
        ]);
    }
}
