<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventario;
use App\Models\Product;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener 100 productos aleatorios
        $products = Product::inRandomOrder()->limit(30000)->get();

        // Primeros 50 productos: Sucursal 1
        foreach ($products->take(10000) as $product) {
            Inventario::create([
                'stock_minimo' => rand(5, 15),
                'stock' => rand(20, 100),
                'estado'=>1,
                'disponibilidad'=>1,
                'product_id' => $product->id,
                'branche_id' => 1,
            ]);
        }

        // Siguientes 50 productos: Sucursal 2
        foreach ($products->skip(10000) as $product) {
            Inventario::create([
                'stock_minimo' => rand(5, 15),
                'stock' => rand(20, 100),
                'estado'=>1,
                'disponibilidad'=>1,
                'product_id' => $product->id,
                'branche_id' => 2,
            ]);
        }
    }
}
