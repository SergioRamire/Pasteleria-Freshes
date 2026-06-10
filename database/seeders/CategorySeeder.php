<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Herramientas manuales',
            'Herramientas eléctricas',
            'Pinturas y solventes',
            'Materiales de construcción',
            'Plomería',
            'Electricidad',
            'Ferretería general',
            'Tornillería',
            'Seguridad industrial',
            'Adhesivos y selladores',
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category,
                'slug' => Str::slug($category),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
