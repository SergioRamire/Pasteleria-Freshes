<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $suppliers = [
            [
                'name' => 'Grupo Truper',
                'email' => 'alcharlygl@outlook.com',
                'phone' => '9514997432',
                'address' => 'Av. Periférico 2-A, San Lorenzo Almecatla, Puebla, 72710',
                'shopname' => 'Luis Felipe',
                'type' => 'Ventas Mostrador',
                'account_holder' => 'Grupo Truper',
                'account_number' => '1234567890',
                'bank_name' => 'BBVA',
                'bank_branch' => 'Sucursal Centro',
                'city' => 'Ciudad de México',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name' => 'Grupo Surtek',
                'email' => 'alcharlygl@surtek.com',
                'phone' => '0000000000',
                'address' => 'Av. Periférico 2-A, San Lorenzo Almecatla, Puebla, 72710',
                'shopname' => 'David Juearez',
                'type' => 'Mayorista',
                'account_holder' => 'Grupo Surtek',
                'account_number' => '1234567890',
                'bank_name' => 'Santander',
                'bank_branch' => 'Sucursal Norte',
                'city' => 'Ciudad de México',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
    }
}
