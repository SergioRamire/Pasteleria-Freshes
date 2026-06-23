<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use League\Csv\Reader;
use App\Models\Category;
use App\Models\Marca;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/data/Catalogo.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("❌ CSV no encontrado: $csvPath");
            return;
        }

        // Verificar datos necesarios
        $categoryIds = Category::pluck('id')->toArray();
        $marcaIds = Marca::pluck('id')->toArray();

        if (empty($categoryIds) || empty($marcaIds)) {
            $this->command->error("❌ Faltan categorías o marcas. Ejecuta sus seeders primero.");
            return;
        }

        // Leer CSV
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);

        $insertados = 0;

        foreach ($csv->getRecords() as $row) {
            // Validar datos básicos
            if (empty($row['product_name']) || !is_numeric($row['buying_price'])) {
                continue;
            }

            $buyingPrice = (float) $row['buying_price'];

            Product::create([
                'product_name' => trim($row['product_name']),
                'category_id' => Arr::random($categoryIds),
                'product_code' => !empty($row['product_code']) ? $row['product_code'] : null,
                'codigo_barras' => !empty($row['codigo_barras']) ? $row['codigo_barras'] : null,
                // 'clave_sat' => $row['clave_sat'] ?? str_pad(rand(1, 999999999), rand(1, 9), '0', STR_PAD_LEFT),
                'product_garage' => Arr::random(['A1', 'A2', 'B1', 'B2', 'C1']),
                'product_image' => null,
                'product_store' => rand(10, 100),
                'buying_date' => Carbon::now()->subDays(rand(1, 365)),
                'status_product' => true,
                'expire_date' => rand(1, 10) > 7 ? Carbon::now()->addYears(rand(1, 3)) : null,
                'buying_price' => $buyingPrice,
                'selling_price' => round($buyingPrice * 1.5, 2), // 50% margen
                'dealer_price' => round($buyingPrice * 1.2, 2), // 20% margen
                'marca_id' => in_array($row['marca_id'], $marcaIds) ? $row['marca_id'] : Arr::random($marcaIds),
            ]);

            $insertados++;
        }

        $this->command->info("✅ Insertados: $insertados productos");
    }
}
