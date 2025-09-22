<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::query()->upsert([
            ['id'=>1,'name'=>'admin','email'=>'admin@mail.com','mobile'=>'0000000001','password'=>Hash::make('password')],
            ['id'=>2,'name'=>'manager','email'=>'manager@mail.com','mobile'=>'0000000002','password'=>Hash::make('password')],
            ['id'=>3,'name'=>'reviewer','email'=>'reviewer@mail.com','mobile'=>'0000000003','password'=>Hash::make('password')],
        ], ['id']);


        $admin=User::query()->find(1);
        $admin->assignRole('Admin');

        $manager=User::query()->find(2);
        $manager->assignRole('Manager');

        $manager=User::query()->find(3);
        $manager->assignRole('Reviewer');

    }
}
