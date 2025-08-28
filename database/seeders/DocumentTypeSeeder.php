<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $types = [
            'Aadhar',
            'EPIC',
            'Birth Certificate',
            'Educational Certificate',
            'Technical Certificate',
        ];

        foreach ($types as $type) {
            DocumentType::create([
                'name' => $type,
                'description' => "$type document",
            ]);
        }
    }
}
