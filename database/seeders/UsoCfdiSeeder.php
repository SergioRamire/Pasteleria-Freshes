<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UsoCfdi;

class UsoCfdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definición estructurada de los catálogos del SAT
        $usos = [
            ['code' => 'G01', 'description' => 'Adquisición de mercancías'],
            ['code' => 'G02', 'description' => 'Devoluciones, descuentos o bonificaciones'],
            ['code' => 'G03', 'description' => 'Gastos en general'],
            ['code' => 'I01', 'description' => 'Construcciones'],
            ['code' => 'I02', 'description' => 'Mobiliario y equipo de oficina por inversiones'],
            ['code' => 'I03', 'description' => 'Equipo de transporte'],
            ['code' => 'I04', 'description' => 'Equipo de cómputo y accesorios'],
            ['code' => 'I05', 'description' => 'Dados, troqueles, moldes, matrices y herramental'],
            ['code' => 'I06', 'description' => 'Comunicaciones telefónicas'],
            ['code' => 'I07', 'description' => 'Comunicaciones satelitales'],
            ['code' => 'I08', 'description' => 'Otra maquinaria y equipo'],
            ['code' => 'D01', 'description' => 'Honorarios médicos, dentales y gastos hospitalarios'],
            ['code' => 'D02', 'description' => 'Gastos médicos por incapacidad o discapacidad'],
            ['code' => 'D03', 'description' => 'Gastos funerales'],
            ['code' => 'D04', 'description' => 'Donativos'],
            ['code' => 'D05', 'description' => 'Intereses reales efectivamente pagados por créditos hipotecarios'],
            ['code' => 'D06', 'description' => 'Aportaciones voluntarias al SAR'],
            ['code' => 'D07', 'description' => 'Primas por seguros de gastos médicos'],
            ['code' => 'D08', 'description' => 'Gastos de transportación escolar obligatoria'],
            ['code' => 'D09', 'description' => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones'],
            ['code' => 'D10', 'description' => 'Pagos por servicios educativos (colegiaturas)'],
            ['code' => 'P01', 'description' => 'Por definir'],
        ];

        // 2. Procesamiento limpio e inserción uno por uno resguardando duplicados
        foreach ($usos as $uso) {
            UsoCfdi::updateOrCreate(
                ['code' => $uso['code']],
                ['description' => $uso['description']]
            );
        }
    }
}