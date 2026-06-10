<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $buyingPrice = $this->faker->randomFloat(2, 10, 500);
        $sellingPrice = round($buyingPrice * 1.35, 2); // 35% de ganancia
        $dealerPrice = round($sellingPrice * 0.90, 2); // 10% de descuento

        return [
            'product_name' => $this->faker->words(2, true),
            'category_id' => $this->faker->numberBetween(1, 10),
            'marca_id' => $this->faker->numberBetween(1, 10),
            'product_code' => strtoupper(Str::random(8)),
            'product_garage' => $this->faker->randomElement(['Bodega 1', 'Bodega 2', 'Estante A', 'Estante B']),
            'codigo_barras' => $this->faker->regexify('[0-9]{13}'),
            'product_image' => null,
            'product_store' => $this->faker->numberBetween(10, 200),
            'buying_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'expire_date' => '2025-12-04',
            'status_product' => 1,

            'buying_price' => $buyingPrice,
            'selling_price' => $sellingPrice,
            'dealer_price' => $dealerPrice,
        ];
    }

}
