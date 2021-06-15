<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'arvian',
            'email' => 'arvianfy@gmail.com',
            'password' => bcrypt('arvian123'),
            'role' => 'admin'
        ]);
    }
}
