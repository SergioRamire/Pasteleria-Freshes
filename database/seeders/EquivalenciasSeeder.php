<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquivalenciasSeeder extends Seeder
{
    public function run()
    {
        $equivalencias = [
            [
                'nombre' => 'Pieza',
                'abreviatura' => 'PZA',
                'descripcion' => 'Producto individual (ej. herramienta, válvula, brocha).',
                'clave_sat' => 'H87',
                'tipo' => 'Múltiplos/Fracciones/Decimales'
            ],
            [
                'nombre' => 'Paquete',
                'abreviatura' => 'PAQ',
                'descripcion' => 'Conjunto de piezas (ej. paquete de tornillos, taquetes).',
                'clave_sat' => 'XPK',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Caja',
                'abreviatura' => 'CJ',
                'descripcion' => 'Productos empacados en caja (ej. loseta, focos).',
                'clave_sat' => 'XBX',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Metro',
                'abreviatura' => 'ML',
                'descripcion' => 'Longitud lineal (ej. cable, tubo, manguera).',
                'clave_sat' => 'MTR',
                'tipo' => 'Tiempo y Espacio'
            ],
            [
                'nombre' => 'Metro cuadrado',
                'abreviatura' => 'M²',
                'descripcion' => 'Superficie (ej. piso, pintura, malla).',
                'clave_sat' => 'MTK',
                'tipo' => 'Tiempo y Espacio'
            ],
            [
                'nombre' => 'Metro cúbico',
                'abreviatura' => 'M³',
                'descripcion' => 'Volumen (ej. arena, grava, concreto).',
                'clave_sat' => 'MTQ',
                'tipo' => 'Tiempo y Espacio'
            ],
            [
                'nombre' => 'Litro',
                'abreviatura' => 'LT',
                'descripcion' => 'Capacidad estándar para líquidos.',
                'clave_sat' => 'LTR',
                'tipo' => 'Tiempo y Espacio'
            ],
            [
                'nombre' => '0.50 Litro',
                'abreviatura' => '0.50 L',
                'descripcion' => 'Medio litro; envases pequeños (ej. esmaltes, aceites pequeños).',
                'clave_sat' => 'HLT',
                'tipo' => 'Múltiplos/Fracciones/Decimales'
            ],
            [
                'nombre' => '0.25 Litro',
                'abreviatura' => '0.25 L',
                'descripcion' => 'Un cuarto de litro; envases muy pequeños (ej. pegamentos, barnices).',
                'clave_sat' => 'D42',
                'tipo' => 'Múltiplos/Fracciones/Decimales'
            ],
            [
                'nombre' => 'Kilogramo',
                'abreviatura' => 'KG',
                'descripcion' => 'Peso medio (ej. cemento, varilla, productos químicos).',
                'clave_sat' => 'KGM',
                'tipo' => 'Mecánica'
            ],
            [
                'nombre' => 'Tonelada',
                'abreviatura' => 'TON',
                'descripcion' => 'Peso elevado (ej. acero, materiales a granel).',
                'clave_sat' => 'TNE',
                'tipo' => 'Mecánica'
            ],
            [
                'nombre' => 'Rollo',
                'abreviatura' => 'XRO',
                'descripcion' => 'Producto enrollado (ej. malla, cable, alambre).',
                'clave_sat' => 'XRO',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Bulto',
                'abreviatura' => 'BLT',
                'descripcion' => 'Saco de cemento, mortero, cal (usualmente de 50 kg).',
                'clave_sat' => 'XSA',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Cubeta',
                'abreviatura' => 'CBT',
                'descripcion' => 'Recipiente de pintura o impermeabilizante (20 litros aprox.).',
                'clave_sat' => 'XBJ',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Galón',
                'abreviatura' => 'GAL',
                'descripcion' => 'Unidad de volumen, típica en productos importados.',
                'clave_sat' => 'GLL',
                'tipo' => 'Tiempo y Espacio'
            ],
            [
                'nombre' => 'Tarima',
                'abreviatura' => 'TAR',
                'descripcion' => 'Carga paletizada para mayoreo (ej. loseta, bultos).',
                'clave_sat' => 'XPF',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Juego',
                'abreviatura' => 'JGO',
                'descripcion' => 'Conjunto de piezas (ej. herramientas, brocas, llaves).',
                'clave_sat' => 'SET',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Kit',
                'abreviatura' => 'KIT',
                'descripcion' => 'Paquete que incluye varios elementos relacionados (ej. kit de plomería).',
                'clave_sat' => 'XKI',
                'tipo' => 'Unidades de empaque'
            ],
            [
                'nombre' => 'Par',
                'abreviatura' => 'PAR',
                'descripcion' => 'Para artículos en pares (ej. bisagras, guantes).',
                'clave_sat' => 'PR',
                'tipo' => 'Números enteros/Números/Ratios'
            ],
            [
                'nombre' => 'Viaje',
                'abreviatura' => 'VJE',
                'descripcion' => 'Unidades específicas de la industria (servicios de transporte).',
                'clave_sat' => 'E54',
                'tipo' => 'Unidades específicas de la industria'
            ]
        ];

        foreach ($equivalencias as $item) {
            DB::table('equivalencias')->insert([
                'nombre' => $item['nombre'],
                'abreviatura' => $item['abreviatura'],
                'descripcion' => $item['descripcion'],
                'clave_sat' => $item['clave_sat'],
                'tipo' => $item['tipo'],
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
