<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RegimenFiscal;

class RegimenFiscalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regimenes = [
            ['code' => '601', 'description' => 'General de Ley Personas Morales'],
            ['code' => '603', 'description' => 'Personas Morales con Fines no Lucrativos'],
            ['code' => '605', 'description' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios'],
            ['code' => '606', 'description' => 'Arrendamiento'],
            ['code' => '608', 'description' => 'Demás ingresos'],
            ['code' => '609', 'description' => 'Consolidación'],
            ['code' => '610', 'description' => 'Residentes en el Extranjero sin Establecimiento Permanente en México'],
            ['code' => '611', 'description' => 'Ingresos por Dividendos (socios y accionistas)'],
            ['code' => '612', 'description' => 'Personas Físicas con Actividades Empresariales y Profesionales'],
            ['code' => '614', 'description' => 'Ingresos por intereses'],
            ['code' => '615', 'description' => 'Régimen de los ingresos por obtención de premios'],
            ['code' => '616', 'description' => 'Sin obligaciones fiscales'],
            ['code' => '620', 'description' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos'],
            ['code' => '621', 'description' => 'Incorporación Fiscal'],
            ['code' => '622', 'description' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras'],
            ['code' => '623', 'description' => 'Opcional para Grupos de Sociedades'],
            ['code' => '624', 'description' => 'Coordinados'],
            ['code' => '625', 'description' => 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas'],
            ['code' => '626', 'description' => 'Régimen Simplificado de Confianza'],
        ];

        foreach ($regimenes as $regimen) {
            RegimenFiscal::updateOrCreate(
                ['code' => $regimen['code']],
                ['description' => $regimen['description']]
            );
        }
    }
}
