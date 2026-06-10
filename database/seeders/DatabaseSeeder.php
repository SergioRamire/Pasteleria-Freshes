<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\UsoCfdi;
use App\Models\AdvanceSalary;
use App\Models\RegimenFiscal;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Branche;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear sucursales primero
        $this->createBranches();

        // Crear usuarios
        $admin = $this->createUsers();

        // Crear todos los permisos
        $this->createPermissions();

        // Crear roles y asignar permisos
        $this->createRoles();

        // Asignar roles a usuarios
        $admin['admin']->assignRole('SuperAdmin');
        $admin['user']->assignRole('Propietario');

        // Ejecutar otros seeders
        $this->call([
            UsoCfdiSeeder::class,
            RegimenFiscalSeeder::class,
            SupplierSeeder::class,
            CategorySeeder::class,
            MarcaSeeder::class,
            EquivalenciasSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            InventarioSeeder::class,
            ClaveSatSeeder::class,
        ]);
    }

    private function createBranches()
    {
        Branche::insert([
            [
                'nombre' => 'Sucursal Centro',
                'direccion' => 'C. Cuauhtémoc 20, Centro, 70400 Tlacolula de Matamoros, Oax.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sucursal Norte',
                'direccion' => 'Internacional Cristóbal Colón, Segunda Secc, 70400 Tlacolula de Matamoros, Oax.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function createUsers()
    {
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'estado' => 1,
            'cellphone' => '+5219510000000',
        ]);

        $user = \App\Models\User::factory()->create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'estado' => 1,
            'cellphone' => '+5219511000000',
        ]);

        return ['admin' => $admin, 'user' => $user];
    }

    private function createPermissions()
    {
        // Permisos para empleados
        Permission::create(['name' => 'employee.menu', 'group_name' => 'Empleados']);
        Permission::create(['name' => 'employee.ver', 'group_name' => 'Empleados']);
        Permission::create(['name' => 'employee.crear', 'group_name' => 'Empleados']);
        Permission::create(['name' => 'employee.editar', 'group_name' => 'Empleados']);
        Permission::create(['name' => 'employee.eliminar', 'group_name' => 'Empleados']);

        // Permisos para POS
        Permission::create(['name' => 'pos.menu', 'group_name' => 'POS']);
        Permission::create(['name' => 'vender', 'group_name' => 'POS']);

        // Permisos para cotizaciones - AGREGADO
        Permission::create(['name' => 'cotizaciones.menu', 'group_name' => 'Cotizaciones']);
        Permission::create(['name' => 'cotizaciones.crear', 'group_name' => 'Cotizaciones']);
        Permission::create(['name' => 'cotizaciones.ver', 'group_name' => 'Cotizaciones']);
        Permission::create(['name' => 'cotizaciones.editar', 'group_name' => 'Cotizaciones']);
        Permission::create(['name' => 'cotizaciones.eliminar', 'group_name' => 'Cotizaciones']);

        // Permisos para clientes
        Permission::create(['name' => 'clientes.menu', 'group_name' => 'Clientes']);
        Permission::create(['name' => 'clientes.ver', 'group_name' => 'Clientes']);
        Permission::create(['name' => 'clientes.crear', 'group_name' => 'Clientes']);
        Permission::create(['name' => 'clientes.editar', 'group_name' => 'Clientes']);
        Permission::create(['name' => 'clientes.eliminar', 'group_name' => 'Clientes']);

        // Permisos para proveedores
        Permission::create(['name' => 'proveedores.menu', 'group_name' => 'Proveedores']);
        Permission::create(['name' => 'proveedores.ver', 'group_name' => 'Proveedores']);
        Permission::create(['name' => 'proveedores.crear', 'group_name' => 'Proveedores']);
        Permission::create(['name' => 'proveedores.editar', 'group_name' => 'Proveedores']);
        Permission::create(['name' => 'proveedores.eliminar', 'group_name' => 'Proveedores']);

        // Permisos para sucursales
        Permission::create(['name' => 'sucursales.menu', 'group_name' => 'Sucursales']);
        Permission::create(['name' => 'sucursales.ver', 'group_name' => 'Sucursales']);
        Permission::create(['name' => 'sucursales.crear', 'group_name' => 'Sucursales']);
        Permission::create(['name' => 'sucursales.editar', 'group_name' => 'Sucursales']);
        Permission::create(['name' => 'sucursales.eliminar', 'group_name' => 'Sucursales']);

        // Permisos para marcas
        Permission::create(['name' => 'marcas.menu', 'group_name' => 'Marcas']);
        Permission::create(['name' => 'marcas.ver', 'group_name' => 'Marcas']);
        Permission::create(['name' => 'marcas.crear', 'group_name' => 'Marcas']);
        Permission::create(['name' => 'marcas.editar', 'group_name' => 'Marcas']);
        Permission::create(['name' => 'marcas.eliminar', 'group_name' => 'Marcas']);

        // Permisos para productos
        Permission::create(['name' => 'productos.menu', 'group_name' => 'Productos']);
        Permission::create(['name' => 'productos.ver', 'group_name' => 'Productos']);
        Permission::create(['name' => 'productos.crear', 'group_name' => 'Productos']);
        Permission::create(['name' => 'productos.editar', 'group_name' => 'Productos']);
        Permission::create(['name' => 'productos.eliminar', 'group_name' => 'Productos']);

        // Permisos para categorías
        Permission::create(['name' => 'categorias.menu', 'group_name' => 'Categorias']);
        Permission::create(['name' => 'categorias.ver', 'group_name' => 'Categorias']);
        Permission::create(['name' => 'categorias.crear', 'group_name' => 'Categorias']);
        Permission::create(['name' => 'categorias.editar', 'group_name' => 'Categorias']);
        Permission::create(['name' => 'categorias.eliminar', 'group_name' => 'Categorias']);

        // Permisos para inventarios generales
        Permission::create(['name' => 'inventarios.menu', 'group_name' => 'Inventario General']);
        Permission::create(['name' => 'inventarios.ver', 'group_name' => 'Inventario General']);
        Permission::create(['name' => 'inventarios.crear', 'group_name' => 'Inventario General']);
        Permission::create(['name' => 'inventarios.editar', 'group_name' => 'Inventario General']);
        Permission::create(['name' => 'inventarios.eliminar', 'group_name' => 'Inventario General']);

        // Permisos para mis inventarios
        Permission::create(['name' => 'myinventarios.menu', 'group_name' => 'Mis Inventarios']);
        Permission::create(['name' => 'myinventarios.ver', 'group_name' => 'Mis Inventarios']);
        Permission::create(['name' => 'myinventarios.crear', 'group_name' => 'Mis Inventarios']);
        Permission::create(['name' => 'myinventarios.editar', 'group_name' => 'Mis Inventarios']);
        Permission::create(['name' => 'myinventarios.eliminar', 'group_name' => 'Mis Inventarios']);

        // Permisos para traspasos emitidos
        Permission::create(['name' => 'traspasos_emitidos.menu', 'group_name' => 'Traspasos Emitidos']);
        Permission::create(['name' => 'traspasos_emitidos.ver', 'group_name' => 'Traspasos Emitidos']);
        Permission::create(['name' => 'traspasos_emitidos.solicitar', 'group_name' => 'Traspasos Emitidos']);
        Permission::create(['name' => 'traspasos_emitidos.marcar_recibido', 'group_name' => 'Traspasos Emitidos']);
        Permission::create(['name' => 'traspasos_emitidos.cancelar', 'group_name' => 'Traspasos Emitidos']);

        // Permisos para traspasos recibidos
        Permission::create(['name' => 'traspasos_recibidos.menu', 'group_name' => 'Traspasos Recibidos']);
        Permission::create(['name' => 'traspasos_recibidos.ver', 'group_name' => 'Traspasos Recibidos']);
        Permission::create(['name' => 'traspasos_recibidos.despachar', 'group_name' => 'Traspasos Recibidos']);

        // Permisos para cajas generales
        Permission::create(['name' => 'cajas_general.menu', 'group_name' => 'Cajas General']);
        Permission::create(['name' => 'cajas_general.ver', 'group_name' => 'Cajas General']);
        Permission::create(['name' => 'cajas_general.abrir', 'group_name' => 'Cajas General']);
        Permission::create(['name' => 'cajas_general.cerrar', 'group_name' => 'Cajas General']);

        // Permisos para cajas de sucursal
        Permission::create(['name' => 'cajas_sucursal.menu', 'group_name' => 'Cajas Sucursal']);
        Permission::create(['name' => 'cajas_sucursal.ver', 'group_name' => 'Cajas Sucursal']);
        Permission::create(['name' => 'cajas_sucursal.abrir', 'group_name' => 'Cajas Sucursal']);
        Permission::create(['name' => 'cajas_sucursal.cerrar', 'group_name' => 'Cajas Sucursal']);

        // Permisos para mi caja
        Permission::create(['name' => 'mi_caja.menu', 'group_name' => 'Mi Caja']);
        Permission::create(['name' => 'mi_caja.ver', 'group_name' => 'Mi Caja']);
        Permission::create(['name' => 'mi_caja.abrir', 'group_name' => 'Mi Caja']);
        Permission::create(['name' => 'mi_caja.cerrar', 'group_name' => 'Mi Caja']);

        // Permisos para órdenes
        Permission::create(['name' => 'orders.menu', 'group_name' => 'Ordenes']);
        Permission::create(['name' => 'orders.ver', 'group_name' => 'Ordenes']);
        Permission::create(['name' => 'orders.crear', 'group_name' => 'Ordenes']);
        Permission::create(['name' => 'orders.editar', 'group_name' => 'Ordenes']);
        Permission::create(['name' => 'orders.eliminar', 'group_name' => 'Ordenes']);

        // Permisos para stock
        Permission::create(['name' => 'stock.menu', 'group_name' => 'Stock']);
        Permission::create(['name' => 'stock.ver', 'group_name' => 'Stock']);

        // Permisos para roles
        Permission::create(['name' => 'roles.menu', 'group_name' => 'Roles']);
        Permission::create(['name' => 'roles.ver', 'group_name' => 'Roles']);
        Permission::create(['name' => 'roles.crear', 'group_name' => 'Roles']);
        Permission::create(['name' => 'roles.editar', 'group_name' => 'Roles']);
        Permission::create(['name' => 'roles.eliminar', 'group_name' => 'Roles']);

        // Permisos para usuarios
        Permission::create(['name' => 'usuarios.menu', 'group_name' => 'Usuarios']);
        Permission::create(['name' => 'usuarios.ver', 'group_name' => 'Usuarios']);
        Permission::create(['name' => 'usuarios.crear', 'group_name' => 'Usuarios']);
        Permission::create(['name' => 'usuarios.editar', 'group_name' => 'Usuarios']);
        Permission::create(['name' => 'usuarios.eliminar', 'group_name' => 'Usuarios']);

        // Permisos para compras de inventario
        Permission::create(['name' => 'compras.menu', 'group_name' => 'Compras Inventario']);
        Permission::create(['name' => 'compras.ver', 'group_name' => 'Compras Inventario']);
        Permission::create(['name' => 'compras.realizar_compra', 'group_name' => 'Compras Inventario']);

        // Permisos para lista de productos para proveedores
        Permission::create(['name' => 'lista.menu', 'group_name' => 'Lista Productos para Proveedores']);
        Permission::create(['name' => 'lista.ver', 'group_name' => 'Lista Productos para Proveedores']);
        Permission::create(['name' => 'lista.realizar_lista', 'group_name' => 'Lista Productos para Proveedores']);

        // Permisos para base de datos
        Permission::create(['name' => 'database.menu', 'group_name' => 'Database']);
        Permission::create(['name' => 'database.ver', 'group_name' => 'Database']);

        // Permisos para historial
        Permission::create(['name' => 'historial.menu', 'group_name' => 'Historial']);
        Permission::create(['name' => 'historial.ver', 'group_name' => 'Historial']);

        // Permisos para claves SAT
        Permission::create(['name' => 'claves.menu', 'group_name' => 'Clave Sat']);
        Permission::create(['name' => 'claves.ver', 'group_name' => 'Clave Sat']);
        Permission::create(['name' => 'claves.crear', 'group_name' => 'Clave Sat']);
        Permission::create(['name' => 'claves.editar', 'group_name' => 'Clave Sat']);
        Permission::create(['name' => 'claves.eliminar', 'group_name' => 'Clave Sat']);
        Permission::create(['name' => 'claves.agregar_producto', 'group_name' => 'Clave Sat']);

        // Permisos para equivalencias
        Permission::create(['name' => 'equivalencias.menu', 'group_name' => 'Equivalencia']);
        Permission::create(['name' => 'equivalencias.ver', 'group_name' => 'Equivalencia']);
        Permission::create(['name' => 'equivalencias.crear', 'group_name' => 'Equivalencia']);
        Permission::create(['name' => 'equivalencias.editar', 'group_name' => 'Equivalencia']);
        Permission::create(['name' => 'equivalencias.eliminar', 'group_name' => 'Equivalencia']);

        // Permisos para salarios
        Permission::create(['name' => 'salary.menu', 'group_name' => 'Salarios']);
        Permission::create(['name' => 'salary.ver', 'group_name' => 'Salarios']);

        // Permisos para asistencia
        Permission::create(['name' => 'attendence.menu', 'group_name' => 'Asistencia']);
        Permission::create(['name' => 'attendence.ver', 'group_name' => 'Asistencia']);

        // Permisos para empleados de ventas
        Permission::create(['name' => 'salesEmployee.menu', 'group_name' => 'Empleados de Ventas']);
        Permission::create(['name' => 'salesEmployee.ver', 'group_name' => 'Empleados de Ventas']);

        // Permisos para clientes de crédito
        Permission::create(['name' => 'creditClient.menu', 'group_name' => 'Clientes de Crédito']);
        Permission::create(['name' => 'creditClient.ver', 'group_name' => 'Clientes de Crédito']);

        // Permisos para artículos vendidos
        Permission::create(['name' => 'soldItems.menu', 'group_name' => 'Artículos Vendidos']);
        Permission::create(['name' => 'soldItems.ver', 'group_name' => 'Artículos Vendidos']);

        // Permisos para conversiones
        Permission::create(['name' => 'conversiones.menu', 'group_name' => 'Conversiones']);
        Permission::create(['name' => 'conversiones.crear', 'group_name' => 'Conversiones']);

        // Permisos para historial de conversiones
        Permission::create(['name' => 'conversion.historial', 'group_name' => 'Historial Conversiones']);
        Permission::create(['name' => 'conversion.historial.ver', 'group_name' => 'Historial Conversiones']);
        Permission::create(['name' => 'conversion.historial.detalle', 'group_name' => 'Historial Conversiones']);
    }

    private function createRoles()
    {
        // SuperAdmin - Acceso completo a todo
        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Propietario - Acceso casi completo
        $propietarioRole = Role::create(['name' => 'Propietario']);
        $propietarioRole->givePermissionTo([
            // POS y Ventas
            'pos.menu', 'vender',

            // Cotizaciones - AGREGADO
            'cotizaciones.menu', 'cotizaciones.crear', 'cotizaciones.ver', 'cotizaciones.editar', 'cotizaciones.eliminar',

            // Inventarios
            'inventarios.menu', 'inventarios.ver', 'inventarios.crear', 'inventarios.editar', 'inventarios.eliminar',
            'myinventarios.menu', 'myinventarios.ver', 'myinventarios.crear', 'myinventarios.editar', 'myinventarios.eliminar',

            // Traspasos
            'traspasos_emitidos.menu', 'traspasos_emitidos.ver', 'traspasos_emitidos.solicitar', 'traspasos_emitidos.marcar_recibido', 'traspasos_emitidos.cancelar',
            'traspasos_recibidos.menu', 'traspasos_recibidos.ver', 'traspasos_recibidos.despachar',

            // Cajas
            'mi_caja.menu', 'mi_caja.ver', 'mi_caja.abrir', 'mi_caja.cerrar',
            'cajas_general.menu', 'cajas_general.ver', 'cajas_general.abrir', 'cajas_general.cerrar',
            'cajas_sucursal.menu', 'cajas_sucursal.ver', 'cajas_sucursal.abrir', 'cajas_sucursal.cerrar',

            // Productos y categorías
            'productos.menu', 'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
            'categorias.menu', 'categorias.ver', 'categorias.crear', 'categorias.editar', 'categorias.eliminar',

            // Compras y listas
            'compras.menu', 'compras.ver', 'compras.realizar_compra',
            'lista.menu', 'lista.ver', 'lista.realizar_lista',

            // Equivalencias y conversiones
            'equivalencias.menu', 'equivalencias.ver', 'equivalencias.crear', 'equivalencias.editar', 'equivalencias.eliminar',
            'conversiones.menu', 'conversiones.crear',
            'conversion.historial', 'conversion.historial.ver', 'conversion.historial.detalle',

            // Claves SAT
            'claves.menu', 'claves.ver', 'claves.crear', 'claves.editar', 'claves.eliminar', 'claves.agregar_producto',

            // Clientes y proveedores
            'clientes.menu', 'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',
            'proveedores.menu', 'proveedores.ver', 'proveedores.crear', 'proveedores.editar', 'proveedores.eliminar',

            // Marcas y sucursales
            'marcas.menu', 'marcas.ver', 'marcas.crear', 'marcas.editar', 'marcas.eliminar',
            'sucursales.menu', 'sucursales.ver', 'sucursales.crear', 'sucursales.editar', 'sucursales.eliminar',

            // Historial y usuarios
            'historial.menu', 'historial.ver',
            'usuarios.menu', 'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',

            // Stock y órdenes
            'stock.menu', 'stock.ver',
            'orders.menu', 'orders.ver', 'orders.crear', 'orders.editar', 'orders.eliminar',
        ]);

        // Gerente - Gestión operativa sin roles/permisos ni sucursales
        $gerenteRole = Role::create(['name' => 'Gerente']);
        $gerenteRole->givePermissionTo([
            // POS y Ventas
            'pos.menu', 'vender',

            // Cotizaciones - AGREGADO
            'cotizaciones.menu', 'cotizaciones.crear', 'cotizaciones.ver', 'cotizaciones.editar', 'cotizaciones.eliminar',

            // Inventarios (solo mi inventario)
            'myinventarios.menu', 'myinventarios.ver', 'myinventarios.crear', 'myinventarios.editar', 'myinventarios.eliminar',

            // Traspasos
            'traspasos_emitidos.menu', 'traspasos_emitidos.ver', 'traspasos_emitidos.solicitar', 'traspasos_emitidos.marcar_recibido', 'traspasos_emitidos.cancelar',
            'traspasos_recibidos.menu', 'traspasos_recibidos.ver', 'traspasos_recibidos.despachar',

            // Mi caja
            'mi_caja.menu', 'mi_caja.ver', 'mi_caja.abrir', 'mi_caja.cerrar',

            // Productos y categorías
            'productos.menu', 'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
            'categorias.menu', 'categorias.ver', 'categorias.crear', 'categorias.editar', 'categorias.eliminar',

            // Compras y listas
            'compras.menu', 'compras.ver', 'compras.realizar_compra',
            'lista.menu', 'lista.ver', 'lista.realizar_lista',

            // Equivalencias y conversiones
            'equivalencias.menu', 'equivalencias.ver', 'equivalencias.crear', 'equivalencias.editar', 'equivalencias.eliminar',
            'conversiones.menu', 'conversiones.crear',
            'conversion.historial', 'conversion.historial.ver', 'conversion.historial.detalle',

            // Claves SAT
            'claves.menu', 'claves.ver', 'claves.crear', 'claves.editar', 'claves.eliminar', 'claves.agregar_producto',

            // Clientes y proveedores
            'clientes.menu', 'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',
            'proveedores.menu', 'proveedores.ver', 'proveedores.crear', 'proveedores.editar', 'proveedores.eliminar',

            // Marcas
            'marcas.menu', 'marcas.ver', 'marcas.crear', 'marcas.editar', 'marcas.eliminar',

            // Historial
            'historial.menu', 'historial.ver',

            // Stock y órdenes
            'stock.menu', 'stock.ver',
            'orders.menu', 'orders.ver', 'orders.crear', 'orders.editar', 'orders.eliminar',
        ]);

        // Cajero - Operaciones de caja y ventas
        $cajeroRole = Role::create(['name' => 'Cajero']);
        $cajeroRole->givePermissionTo([
            // POS y Ventas
            'pos.menu', 'vender',

            // Cotizaciones básicas - AGREGADO
            'cotizaciones.menu', 'cotizaciones.crear', 'cotizaciones.ver',

            // Mi caja
            'mi_caja.menu', 'mi_caja.ver', 'mi_caja.abrir', 'mi_caja.cerrar',

            // Clientes
            'clientes.menu', 'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',

            // Órdenes
            'orders.menu', 'orders.ver', 'orders.crear', 'orders.editar', 'orders.eliminar',
        ]);

        // Vendedor - Solo ventas y consultas básicas
        $vendedorRole = Role::create(['name' => 'Vendedor']);
        $vendedorRole->givePermissionTo([
            // POS y Ventas
            'pos.menu', 'vender',

            // Cotizaciones básicas - AGREGADO
            'cotizaciones.menu', 'cotizaciones.crear', 'cotizaciones.ver',

            // Clientes
            'clientes.menu', 'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',
        ]);

        // Almacén - Gestión de inventarios y traspasos
        $almacenRole = Role::create(['name' => 'Almacén']);
        $almacenRole->givePermissionTo([
            // Inventarios
            'inventarios.menu', 'inventarios.ver', 'inventarios.crear', 'inventarios.editar', 'inventarios.eliminar',
            'myinventarios.menu', 'myinventarios.ver', 'myinventarios.crear', 'myinventarios.editar', 'myinventarios.eliminar',

            // Traspasos
            'traspasos_emitidos.menu', 'traspasos_emitidos.ver', 'traspasos_emitidos.solicitar', 'traspasos_emitidos.marcar_recibido', 'traspasos_emitidos.cancelar',
            'traspasos_recibidos.menu', 'traspasos_recibidos.ver', 'traspasos_recibidos.despachar',

            // Productos y categorías
            'productos.menu', 'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
            'categorias.menu', 'categorias.ver', 'categorias.crear', 'categorias.editar', 'categorias.eliminar',

            // Compras y listas
            'compras.menu', 'compras.ver', 'compras.realizar_compra',
            'lista.menu', 'lista.ver', 'lista.realizar_lista',

            // Equivalencias y conversiones
            'equivalencias.menu', 'equivalencias.ver', 'equivalencias.crear', 'equivalencias.editar', 'equivalencias.eliminar',
            'conversiones.menu', 'conversiones.crear',
            'conversion.historial', 'conversion.historial.ver', 'conversion.historial.detalle',

            // Claves SAT
            'claves.menu', 'claves.ver', 'claves.crear', 'claves.editar', 'claves.eliminar', 'claves.agregar_producto',

            // Proveedores y marcas
            'proveedores.menu', 'proveedores.ver', 'proveedores.crear', 'proveedores.editar', 'proveedores.eliminar',
            'marcas.menu', 'marcas.ver', 'marcas.crear', 'marcas.editar', 'marcas.eliminar',

            // Historial y stock
            'historial.menu', 'historial.ver',
            'stock.menu', 'stock.ver',
        ]);
    }
}
