<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//         \App\Models\User::factory(10)->create();

        // create with password increment
//         $base_password = 'password';
//            for ($i = 0; $i < 10; $i++) {
//                $user = new \App\Models\User();
//                $user->name = 'user' . $i;
//                $user->email = 'user' . $i . '@example.com';
//                $user->password = Hash::make($base_password . $i);
//                $user->save();
//            }

        // $admin
        $user = new \App\Models\User();
        $user->name = 'admin';
        $user->email = 'monja.sesame@gmail.com';
        $user->password = Hash::make('2o24WebCup');
        $user->role = 'admin';
        $user->status = 1;
        $user->save();
    }
}
