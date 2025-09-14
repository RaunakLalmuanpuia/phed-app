<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('offices')->insert([
            [
                'name' => 'Aizawl Circle Office',
                'type' => 'Circle',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AWDD North',
                'type' => 'Division',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AWDD South',
                'type' => 'Division',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AWTD',
                'type' => 'Division',
                'location' => 'Lawngtlai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AWTDN',
                'type' => 'Division',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'C.E Z-I',
                'type' => 'Zone',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'C.E Z-II',
                'type' => 'Zone',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Champhai',
                'type' => 'Division',
                'location' => 'Champhai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Champhai Circle Office',
                'type' => 'Circle',
                'location' => 'Champhai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'E-in-C',
                'type' => 'Office',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GW&QC',
                'type' => 'Division',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GWRAC',
                'type' => 'Division',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hnahthial',
                'type' => 'Division',
                'location' => 'Hnahthial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khawzawl',
                'type' => 'Division',
                'location' => 'Khawzawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kolasib',
                'type' => 'Division',
                'location' => 'Kolasib',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lawngtlai',
                'type' => 'Division',
                'location' => 'Lawngtlai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lunglei Circle Office',
                'type' => 'Circle',
                'location' => 'Lunglei',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'LWSM',
                'type' => 'Division',
                'location' => 'Lawngtlai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mamit',
                'type' => 'Division',
                'location' => 'Mamit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rural Circle Office',
                'type' => 'Circle',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'RWD Aizawl',
                'type' => 'Division',
                'location' => 'Lawngtlai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'RWD Lunglei',
                'type' => 'Division',
                'location' => 'Lunglei',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'S & S Circle Office',
                'type' => 'Circle',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'S&D',
                'type' => 'Division',
                'location' => 'Aizawl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Serchhip',
                'type' => 'Division',
                'location' => 'Serchhip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siaha',
                'type' => 'Division',
                'location' => 'Siaha',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
