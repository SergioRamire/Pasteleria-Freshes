<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('customers')->insert([
            [
                'name' => 'Cliente General',
                'email' => 'publico@general.com',
                'phone' => '0000000000',
                'shopname' => null,
                'photo' => null,
                'regimen_fiscal' => '616',
                'uso_cfdi' => 'P01',
                'city' => 'Ciudad Genérica',
                'type_customer' => 'general',
                'rfc' => 'XAXX010101000',
                'tipo_persona' => 'Moral',
                'num_interior' => null,
                'num_exterior' => null,
                'calle' => 'Sin dirección',
                'colonia' => 'Sin colonia',
                'municipio' => 'Genérico',
                'estado' => 'Sin estado',
                'pais' => 'México',
                'referencia' => null,
                'cp' => 00000,
                'rul_maps' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'phone' => '5512345678',
                'shopname' => 'Ferretería JP',
                'photo' => null,
                'regimen_fiscal' => '601',
                'uso_cfdi' => 'G03',
                'city' => 'Ciudad de México',
                'type_customer' => 'cliente',
                'rfc' => 'PEPJ8001019Q8',
                'tipo_persona' => 'Física',
                'num_interior' => '2B',
                'num_exterior' => '123',
                'calle' => 'Av. Reforma',
                'colonia' => 'Centro',
                'municipio' => 'Cuauhtémoc',
                'estado' => 'CDMX',
                'pais' => 'México',
                'referencia' => 'Frente al banco',
                'cp' => 06000,
                'rul_maps' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Laura Gómez',
                'email' => 'laura.gomez@example.com',
                'phone' => '5611122233',
                'shopname' => 'Papelería La Goma',
                'photo' => null,
                'regimen_fiscal' => '612',
                'uso_cfdi' => 'D01',
                'city' => 'Guadalajara',
                'type_customer' => 'cliente',
                'rfc' => 'GOML9305053D2',
                'tipo_persona' => 'Física',
                'num_interior' => '',
                'num_exterior' => '56',
                'calle' => 'Calle Hidalgo',
                'colonia' => 'Santa Teresita',
                'municipio' => 'Guadalajara',
                'estado' => 'Jalisco',
                'pais' => 'México',
                'referencia' => 'A un lado del OXXO',
                'cp' => 44100,
                'rul_maps' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Empresa XYZ',
                'email' => 'contacto@xyz.com.mx',
                'phone' => '5522334455',
                'shopname' => 'XYZ S.A. de C.V.',
                'photo' => null,
                'regimen_fiscal' => '603',
                'uso_cfdi' => 'I04',
                'city' => 'Monterrey',
                'type_customer' => 'cliente',
                'rfc' => 'XYZ910101AA1',
                'tipo_persona' => 'Moral',
                'num_interior' => '',
                'num_exterior' => '789',
                'calle' => 'Av. Industriales',
                'colonia' => 'Obrera',
                'municipio' => 'Monterrey',
                'estado' => 'Nuevo León',
                'pais' => 'México',
                'referencia' => null,
                'cp' => 64000,
                'rul_maps' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
