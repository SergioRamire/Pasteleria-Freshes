<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    public function run()
    {
        $marcas = [
            ['nombre' => 'Truper', 'suppliers_id' => 1],
            ['nombre' => 'Expert', 'suppliers_id' => 1],
            ['nombre' => 'Hermex', 'suppliers_id' => 1],
            ['nombre' => 'Pretul', 'suppliers_id' => 1],
            ['nombre' => 'Volteck', 'suppliers_id' => 1],
            ['nombre' => 'Klintek', 'suppliers_id' => 1],
            ['nombre' => 'Foset', 'suppliers_id' => 1],
            ['nombre' => 'Fiero', 'suppliers_id' => 1],
            ['nombre' => 'Ultracraft', 'suppliers_id' => 1],
            ['nombre' => 'Surtek', 'suppliers_id' => 2],
        ];

        foreach ($marcas as $marca) {
            DB::table('marcas')->insert([
                'nombre' => $marca['nombre'],
                'suppliers_id' => $marca['suppliers_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
