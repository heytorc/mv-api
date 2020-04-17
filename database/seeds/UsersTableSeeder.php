<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'              => 'Administrador',
            'email'             => 'admin@tascominformatica.com.br',
            'email_verified_at' => now(),
            'username'          => 'tascom',
            'password'          => Hash::make('tascom'),
            'created_at'        => now(),
            'updated_at'        => now()
        ]);
    }
}