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
            'name' => 'leonardo',
            'email' => 'leonardo@intecmex.com',
            'password' => Hash::make('password'),
            'gender' => '1'
        ]);
    }
}
