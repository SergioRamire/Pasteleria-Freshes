<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satclave;
use League\Csv\Reader;
use Illuminate\Support\Str;

class ClaveSatSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/data/ClavesSat.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("❌ CSV no encontrado: $csvPath");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0); // Se espera que la primera fila tenga los encabezados

        $insertados = 0;

        foreach ($csv->getRecords() as $row) {
            // Validar existencia de campos requeridos
            if (empty($row['c_ClaveProdServ']) || empty($row['descripcion'])) {
                continue;
            }

            Satclave::updateOrCreate(
                ['c_ClaveProdServ' => trim($row['c_ClaveProdServ'])],
                [
                    'descripcion' => trim($row['descripcion']),
                    'activo' => isset($row['activo']) ? filter_var($row['activo'], FILTER_VALIDATE_BOOLEAN) : true,
                ]
            );

            $insertados++;
        }

        $this->command->info("✅ Se insertaron o actualizaron $insertados claves SAT.");
    }
}
