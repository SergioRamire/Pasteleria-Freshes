<?php

/*
|--------------------------------------------------------------------------
| Importación de Controladores
|--------------------------------------------------------------------------
|
| Todos los controladores necesarios para el funcionamiento del sistema
| están organizados por categorías para facilitar su mantenimiento.
|
*/

use Illuminate\Support\Facades\Route;

// Controladores Principales del Dashboard
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProfileController;

// Controladores de Catálogos Básicos
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\SupplierController;
use App\Http\Controllers\Dashboard\BranchesController;
use App\Http\Controllers\Dashboard\MarcaController;

// Controladores de Gestión de Personal
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\PaySalaryController;
use App\Http\Controllers\Dashboard\AttendenceController;
use App\Http\Controllers\Dashboard\AdvanceSalaryController;

// Controladores de Ventas y POS
use App\Http\Controllers\Dashboard\PosController;
use App\Http\Controllers\Dashboard\VentaController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\CotizacionController;

// Controladores de Inventario y Stock
use App\Http\Controllers\Dashboard\InventarioController;
use App\Http\Controllers\Dashboard\MyInventarioController;
use App\Http\Controllers\Dashboard\TraspasosController;
use App\Http\Controllers\Dashboard\TraspasoSucursalController;
use App\Http\Controllers\Dashboard\TraspasosRecibidosController;

// Controladores de Compras
use App\Http\Controllers\Dashboard\ComprasController;
use App\Http\Controllers\Dashboard\ComprasSucursalController;
use App\Http\Controllers\Dashboard\CompraListProveedorController;
use App\Http\Controllers\Dashboard\ListProveedorController;

// Controladores de Caja y Transacciones
use App\Http\Controllers\Dashboard\CajaController;
use App\Http\Controllers\Dashboard\CajaSucursalController;
use App\Http\Controllers\Dashboard\MiCajaController;
use App\Http\Controllers\Dashboard\TransaccionesController;

// Controladores de Administración y Configuración
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\DatabaseBackupController;

// Controladores de Reportes y Configuraciones Especiales
use App\Http\Controllers\Dashboard\HistorialesController;
use App\Http\Controllers\Dashboard\EquivalenciasController;
use App\Http\Controllers\Dashboard\ClaveSatController;
use App\Http\Controllers\Dashboard\ConversionController;

use App\Http\Controllers\Dashboard\ConfiguracionNegocioController;

use App\Http\Controllers\Dashboard\EditOrderController;

use App\Http\Controllers\Dashboard\AbonosController;

/*
|--------------------------------------------------------------------------
| Rutas Web Principales
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web para la aplicación. Estas rutas son
| cargadas por el RouteServiceProvider y todas pertenecen al grupo de
| middleware "web".
|
*/

/**
 * Ruta raíz - Redirección al login
 * Redirige automáticamente a los usuarios no autenticados al formulario de login
 */
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rutas del Dashboard y Perfil de Usuario
|--------------------------------------------------------------------------
|
| Rutas básicas para el dashboard principal y gestión de perfil de usuario.
| Requieren autenticación para acceder.
|
*/
Route::middleware('auth')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth'])
        ->name('dashboard');

    // Gestión de perfil de usuario
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
});

/*
|--------------------------------------------------------------------------
| Gestión de Usuarios y Roles
|--------------------------------------------------------------------------
|
| Sistema completo de administración de usuarios, roles y permisos.
| Solo accesible para usuarios con permisos específicos.
|
*/

/**
 * Gestión de Usuarios
 * CRUD completo para administrar usuarios del sistema
 */
Route::middleware(['permission:usuarios.menu'])->group(function () {
    Route::resource('/users', UserController::class)->except(['show']);
});

/**
 * Gestión de Roles y Permisos
 * Sistema completo para administrar roles, permisos y asignaciones
 */
Route::middleware(['permission:roles.menu'])->group(function () {
    // Gestión de Permisos
    Route::prefix('permission')->name('permission.')->group(function () {
        Route::get('/', [RoleController::class, 'permissionIndex'])->name('index');
        Route::get('/create', [RoleController::class, 'permissionCreate'])->name('create');
        Route::post('/', [RoleController::class, 'permissionStore'])->name('store');
        Route::get('/edit/{id}', [RoleController::class, 'permissionEdit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'permissionUpdate'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'permissionDestroy'])->name('destroy');
    });

    // Gestión de Roles
    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/', [RoleController::class, 'roleIndex'])->name('index');
        Route::get('/create', [RoleController::class, 'roleCreate'])->name('create');
        Route::post('/', [RoleController::class, 'roleStore'])->name('store');
        Route::get('/edit/{id}', [RoleController::class, 'roleEdit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'roleUpdate'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'roleDestroy'])->name('destroy');
    });

    // Asignación de Permisos a Roles
    Route::prefix('role/permission')->name('rolePermission.')->group(function () {
        Route::get('/', [RoleController::class, 'rolePermissionIndex'])->name('index');
        Route::get('/create', [RoleController::class, 'rolePermissionCreate'])->name('create');
        Route::post('/', [RoleController::class, 'rolePermissionStore'])->name('store');
        Route::get('/{id}', [RoleController::class, 'rolePermissionEdit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'rolePermissionUpdate'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'rolePermissionDestroy'])->name('destroy');
    });

    // API para obtener roles disponibles por sucursal
    Route::get('/roles-disponibles/{brancheId}', [UserController::class, 'obtenerRolesDisponibles']);
});

/*
|--------------------------------------------------------------------------
| Catálogos Principales
|--------------------------------------------------------------------------
|
| Gestión de los catálogos básicos del sistema: clientes, proveedores,
| sucursales, productos, categorías y marcas.
|
*/

/**
 * Gestión de Clientes
 * CRUD completo para el manejo de clientes del sistema
 */
Route::middleware(['permission:clientes.menu'])->group(function () {
    Route::resource('/customers', CustomerController::class);
});

/**
 * Gestión de Proveedores
 * CRUD completo para el manejo de proveedores del sistema
 */
Route::middleware(['permission:proveedores.menu'])->group(function () {
    Route::resource('/suppliers', SupplierController::class);
});

/**
 * Gestión de Sucursales
 * CRUD completo para el manejo de sucursales/tiendas
 */
Route::middleware(['permission:sucursales.menu'])->group(function () {
    Route::resource('/sucursales', BranchesController::class);
});

/**
 * Gestión de Marcas
 * CRUD completo para el manejo de marcas de productos
 */
Route::middleware(['permission:marcas.menu'])->group(function () {
    Route::resource('/marcas', MarcaController::class);
});

/**
 * Gestión de Categorías de Productos
 * CRUD completo para categorizar productos
 */
Route::middleware(['permission:categorias.menu'])->group(function () {
    Route::resource('/categories', CategoryController::class);
});

/**
 * Gestión de Productos
 * CRUD completo con funciones de importación y exportación
 */
Route::middleware(['permission:productos.menu'])->group(function () {
    // Funciones especiales de productos
    Route::get('/products/import', [ProductController::class, 'importView'])->name('products.importView');
    Route::post('/products/import', [ProductController::class, 'importStore'])->name('products.importStore');
    Route::get('/products/export', [ProductController::class, 'exportData'])->name('products.exportData');

    // CRUD estándar
    Route::resource('/products', ProductController::class);
});

/*
|--------------------------------------------------------------------------
| Gestión de Personal
|--------------------------------------------------------------------------
|
| Sistema completo para administrar empleados, asistencias y nómina.
|
*/

/**
 * Gestión de Empleados
 * CRUD completo para administrar empleados del sistema
 */
Route::middleware(['permission:employee.menu'])->group(function () {
    Route::resource('/employees', EmployeeController::class);
});

/**
 * Control de Asistencias
 * Sistema para registrar y controlar asistencias de empleados
 */
Route::middleware(['permission:attendence.menu'])->group(function () {
    Route::resource('/employee/attendence', AttendenceController::class)
        ->except(['show', 'update', 'destroy']);
});

/**
 * Sistema de Nómina y Salarios
 * Gestión de pagos de salarios y adelantos salariales
 */
Route::middleware(['permission:salary.menu'])->group(function () {
    // Pago de Salarios
    Route::resource('/pay-salary', PaySalaryController::class)
        ->except(['show', 'create', 'edit', 'update']);

    // Historial y detalles de pagos
    Route::get('/pay-salary/history', [PaySalaryController::class, 'payHistory'])->name('pay-salary.payHistory');
    Route::get('/pay-salary/history/{id}', [PaySalaryController::class, 'payHistoryDetail'])->name('pay-salary.payHistoryDetail');
    Route::get('/pay-salary/{id}', [PaySalaryController::class, 'paySalary'])->name('pay-salary.paySalary');

    // Adelantos Salariales
    Route::resource('/advance-salary', AdvanceSalaryController::class)->except(['show']);
});

/*
|--------------------------------------------------------------------------
| Sistema de Inventarios
|--------------------------------------------------------------------------
|
| Gestión completa de inventarios a nivel general y por sucursal.
|
*/

/**
 * Inventarios Generales
 * Vista y gestión de inventarios desde administración central
 */
Route::middleware(['permission:inventarios.menu'])->group(function () {
    Route::resource('/inventarios', InventarioController::class);
});

/**
 * Mi Inventario (Por Sucursal)
 * Gestión de inventario específico de cada sucursal
 */
Route::middleware(['permission:myinventarios.menu'])->group(function () {
    Route::resource('/myinventarios', MyInventarioController::class);

    // Reportes de inventario
    Route::get('/myinventarios/imprimir/{id}', [MyInventarioController::class, 'imprimir_stock'])
        ->name('myinventarios.imprimir_stock');
});

/*
|--------------------------------------------------------------------------
| Sistema de Punto de Venta (POS)
|--------------------------------------------------------------------------
|
| Rutas para el sistema de punto de venta, incluyendo manejo de carrito,
| facturación y procesamiento de órdenes.
|
*/

/**
 * POS Principal
 * Sistema completo de punto de venta con carrito y facturación
 */
Route::middleware(['permission:pos.menu'])->group(function () {
    // Vista principal del POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

    // Gestión del carrito de compras
    Route::post('/pos/add', [PosController::class, 'addCart'])->name('pos.addCart');
    Route::post('/pos/add-by-barcode', [PosController::class, 'addByBarcode'])->name('pos.add-by-barcode');
    Route::post('/pos/bulk-add', [PosController::class, 'bulkAddCart'])->name('pos.bulkAddCart');
    Route::post('/pos/update/{rowId}', [PosController::class, 'updateCart'])->name('pos.updateCart');
    Route::get('/pos/delete/{rowId}', [PosController::class, 'deleteCart'])->name('pos.deleteCart');
    Route::get('/pos/clear-cart', [PosController::class, 'clearCart'])->name('pos.clearCart');
    Route::post('/pos/Vaciar-Carrito', [PosController::class, 'VaciarCarrito'])->name('pos.VaciarCarrito');

    // Facturación y órdenes
    Route::post('/pos/invoice/create', [PosController::class, 'createInvoice'])->name('pos.createInvoice');
    Route::post('/pos/invoice/print', [PosController::class, 'printInvoice'])->name('pos.printInvoice');
    Route::post('/pos/order', [OrderController::class, 'storeOrder'])->name('pos.storeOrder');
});

/**
 * Sistema de Ventas (Interfaz alternativa de POS)
 * Funcionalidad similar al POS pero con interfaz específica para ventas
 */
Route::middleware(['permission:vender'])->group(function () {
    // Vista principal de ventas
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');

    // Gestión del carrito de ventas
    Route::post('/ventas/add', [VentaController::class, 'addCart'])->name('ventas.addCart');
    Route::post('/ventas/add-by-barcode', [VentaController::class, 'addByBarcode'])->name('ventas.add-by-barcode');
    Route::post('/ventas/bulk-add', [VentaController::class, 'bulkAddCart'])->name('ventas.bulkAddCart');
    Route::post('/ventas/update/{rowId}', [VentaController::class, 'updateCart'])->name('ventas.updateCart');
    Route::get('/ventas/delete/{rowId}', [VentaController::class, 'deleteCart'])->name('ventas.deleteCart');
    Route::get('/ventas/clear-cart', [VentaController::class, 'clearCart'])->name('ventas.clearCart');
    Route::post('/ventas/Vaciar-Carrito', [VentaController::class, 'VaciarCarrito'])->name('ventas.VaciarCarrito');

    // Facturación y procesamiento
    Route::post('/ventas/invoice/create', [VentaController::class, 'createInvoice'])->name('ventas.createInvoice');
    Route::post('/ventas/invoice/print', [VentaController::class, 'printInvoice'])->name('ventas.printInvoice');
    Route::post('/ventas/order', [OrderController::class, 'storeOrder'])->name('ventas.storeOrder');

    // Funciones especiales
    Route::get('/ventas/regresar', [VentaController::class, 'regresar_ventas'])->name('ventas.regresar');
    Route::post('/cajas/cancelar-venta/{id}', [VentaController::class, 'cancelarVenta'])->name('ventas.cancelar-venta');
});

/**
 * Sistema de Cotizaciones
 * Generación y gestión de cotizaciones para clientes
 */
Route::middleware(['permission:vender'])->group(function () {
    // Vista principal de cotizaciones
    Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('cotizaciones.index');

    // Gestión del carrito de cotización
    Route::post('/cotizaciones/add', [CotizacionController::class, 'addCart'])->name('cotizaciones.addCart');
    Route::post('/cotizaciones/add-by-barcode', [CotizacionController::class, 'addByBarcode'])->name('cotizaciones.add-by-barcode');
    Route::post('/cotizaciones/bulk-add', [CotizacionController::class, 'bulkAddCart'])->name('cotizaciones.bulkAddCart');
    Route::post('/cotizaciones/update/{rowId}', [CotizacionController::class, 'updateCart'])->name('cotizaciones.updateCart');
    Route::get('/cotizaciones/delete/{rowId}', [CotizacionController::class, 'deleteCart'])->name('cotizaciones.deleteCart');
    Route::get('/cotizaciones/clear-cart', [CotizacionController::class, 'clearCart'])->name('cotizaciones.clearCart');
    Route::post('/cotizaciones/Vaciar-Carrito', [CotizacionController::class, 'VaciarCarrito'])->name('cotizaciones.VaciarCarrito');

    // Generación y procesamiento de cotizaciones
    Route::post('/cotizaciones/invoice/create', [CotizacionController::class, 'createInvoice'])->name('cotizaciones.createInvoice');
    Route::post('/cotizaciones/invoice/print', [CotizacionController::class, 'printInvoice'])->name('cotizaciones.printInvoice');
    Route::post('/cotizaciones/order', [OrderController::class, 'storeOrder'])->name('cotizaciones.storeOrder');
    Route::get('/cotizaciones/regresar', [CotizacionController::class, 'regresar_ventas'])->name('cotizaciones.regresar');
});

/*
|--------------------------------------------------------------------------
| Gestión de Órdenes y Tickets
|--------------------------------------------------------------------------
|
| Sistema completo para administrar órdenes de venta, estados,
| facturación y generación de tickets.
|
*/
Route::middleware(['permission:orders.menu'])->group(function () {
    // Gestión de órdenes por estado
    Route::get('/orders/pending', [OrderController::class, 'pendingOrders'])->name('order.pendingOrders');
    Route::get('/orders/complete', [OrderController::class, 'completeOrders'])->name('order.completeOrders');
    Route::get('/orders/cancelled', [OrderController::class, 'cancelledOrders'])->name('order.cancelledOrders');

    // Detalles de órdenes
    Route::get('/orders/details/{order_id}', [OrderController::class, 'orderDetails'])->name('order.orderDetails');
    Route::get('/orders/detailsComplete/{order_id}', [OrderController::class, 'orderDetailsComplete'])->name('order.DetailsComplete');
    Route::get('/orders/detailsDue/{order_id}', [OrderController::class, 'orderDetailsDue'])->name('order.DetailsDue');
    Route::get('/orders/detailsCancel/{order_id}', [OrderController::class, 'orderDetailsCancel'])->name('order.DetailsCancel');

    // Funciones especiales de órdenes
    Route::get('/orders/cambioenvio/{order_id}', [OrderController::class, 'cambiarenvio'])->name('order.cambiarenvio');
    Route::put('/orders/update/status', [OrderController::class, 'updateStatus'])->name('order.updateStatus');

    // Facturación y descargas
    Route::get('/orders/invoice/download/{order_id}', [OrderController::class, 'invoiceDownload'])->name('order.invoiceDownload');

    // Gestión de pendientes y pagos
    Route::get('/pending/due', [OrderController::class, 'pendingDue'])->name('order.pendingDue');
    Route::get('/order/due/{id}', [OrderController::class, 'orderDueAjax'])->name('order.orderDueAjax');
    Route::post('/update/due', [OrderController::class, 'updateDue'])->name('order.updateDue');

    // Gestión de stock
    Route::get('/stock', [OrderController::class, 'stockManage'])->name('order.stockManage');

    // Sistema de tickets de venta
    Route::get('/ventas/ticket/{id}', [OrderController::class, 'showTicket'])->name('order.ticket');
    Route::get('/ticket/raw/{id}', [OrderController::class, 'rawTicket'])->name('ticket.raw');
    Route::get('/ventas/imprimir/{id}', [OrderController::class, 'printView'])->name('ventas.print');
});

/*
|--------------------------------------------------------------------------
| Sistema de Cajas y Transacciones
|--------------------------------------------------------------------------
|
| Gestión completa del sistema de cajas por sucursal, transacciones
| y control de flujo de efectivo.
|
*/

/**
 * Gestión General de Cajas (Todas las Sucursales)
 * Vista administrativa de todas las cajas del sistema
 */
Route::middleware(['permission:cajas_general.menu'])->group(function () {
    Route::resource('/cajas_sucursales', CajaController::class);

    // APIs para obtener datos por sucursal
    Route::get('/sucursal/{id}/empleados', [CajaController::class, 'getBySucursal']);

    // Reportes de cajas
    Route::get('/cajas_sucursales/imprimir/{id}', [CajaController::class, 'imprimir'])
        ->name('cajas_sucursales.imprimir_cerrar_caja');
});

/**
 * Gestión de Caja por Sucursal
 * Control de cajas específico para cada sucursal
 */
Route::middleware(['permission:cajas_sucursal.menu'])->group(function () {
    Route::resource('/caja_sucursal', CajaSucursalController::class);

    // Reportes específicos de sucursal
    Route::post('/cajassucursal/invoice/print', [CajaSucursalController::class, 'imprimir_reporte_show'])
        ->name('cajas.imprimir_reporte');
    Route::get('/cajassucursal/imprimir/{id}', [CajaSucursalController::class, 'imprimir'])
        ->name('cajas.imprimir_cerrar_caja');
});

/**
 * Mi Caja Asignada
 * Gestión de caja individual para cada empleado
 */
Route::middleware(['permission:mi_caja.menu'])->group(function () {
    Route::resource('/mis_cajas', MiCajaController::class);

    // Reportes de mi caja
    Route::post('/cajas/invoice/print', [MiCajaController::class, 'imprimir_reporte_show'])
        ->name('mis_cajas.imprimir_reporte');
    Route::get('/cajas/imprimir/{id}', [MiCajaController::class, 'imprimir'])
        ->name('mis_cajas.imprimir_cerrar_caja');

    // Gestión de transacciones en caja abierta
    Route::get('/transacciones/crear/{id}', [MiCajaController::class, 'create_transaccion'])
        ->name('cajas_transacciones.create');
    Route::post('/cajas_transacciones', [MiCajaController::class, 'store_transaccion'])
        ->name('cajas_transacciones.store');
});

/**
 * Gestión de Transacciones
 * CRUD para todas las transacciones del sistema
 */
Route::middleware(['permission:mi_caja.menu'])->group(function () {
    Route::resource('/transacciones', TransaccionesController::class);
});

/*
|--------------------------------------------------------------------------
| Sistema de Traspasos entre Sucursales
|--------------------------------------------------------------------------
|
| Gestión completa de traspasos de mercancía entre sucursales,
| incluyendo emisión y recepción de traspasos.
|
*/

/**
 * Traspasos Emitidos
 * Lista y gestión de traspasos enviados por la sucursal
 */
Route::middleware(['permission:traspasos_emitidos.menu'])->group(function () {
    Route::resource('/listTraspasos', TraspasosController::class);
});

/**
 * Traspasos Recibidos
 * Gestión de traspasos recibidos de otras sucursales
 */
Route::middleware(['permission:traspasos_recibidos.menu'])->group(function () {
    Route::resource('/listTraspasosRecibidos', TraspasosRecibidosController::class);

    // Marcar traspaso como despachado
    Route::patch('/traspasosRecibido/{traspaso}/despachado', [TraspasosRecibidosController::class, 'markAsDespachado'])
        ->name('traspasos.markAsDespachado');
});

/**
 * Solicitud de Traspasos (POS de Traspasos)
 * Interfaz para solicitar traspasos entre sucursales
 */
Route::middleware(['permission:traspasos_emitidos.solicitar'])->group(function () {
    // Vista principal de solicitud de traspasos
    Route::get('/traspasos', [TraspasoSucursalController::class, 'index'])->name('traspasos.index');

    // Gestión del carrito de traspaso
    Route::post('/traspasos/add', [TraspasoSucursalController::class, 'addCart'])->name('traspasos.addCart');
    Route::post('/traspasos/add-by-barcode', [TraspasoSucursalController::class, 'addByBarcode'])->name('traspasos.add-by-barcode');
    Route::post('/traspasos/bulk-add', [TraspasoSucursalController::class, 'bulkAddCart'])->name('traspasos.bulkAddCart');
    Route::post('/traspasos/update/{rowId}', [TraspasoSucursalController::class, 'updateCart'])->name('traspasos.updateCart');
    Route::get('/traspasos/delete/{rowId}', [TraspasoSucursalController::class, 'deleteCart'])->name('traspasos.deleteCart');
    Route::get('/traspasos/clear-cart', [TraspasoSucursalController::class, 'clearCart'])->name('traspasos.clearCart');
    Route::post('/traspasos/Vaciar-Carrito', [TraspasoSucursalController::class, 'VaciarCarrito'])->name('traspasos.VaciarCarrito');

    // Procesamiento de traspasos
    Route::post('/traspasos/invoice/create', [TraspasoSucursalController::class, 'createInvoice'])->name('traspasos.createInvoice');
    Route::post('/traspasos/invoice/print', [TraspasoSucursalController::class, 'printInvoice'])->name('traspasos.printInvoice');
    Route::post('/traspasos/order', [TraspasoSucursalController::class, 'storeOrder'])->name('traspasos.storeOrder');

    // Impresión de documentos de traspaso
    Route::get('/traspasos/imprimir/{id}', [TraspasoSucursalController::class, 'imprimir'])
        ->name('traspasos.imprimir_traspaso');
});

/*
|--------------------------------------------------------------------------
| Sistema de Compras
|--------------------------------------------------------------------------
|
| Gestión completa del sistema de compras, incluyendo compras generales,
| compras por sucursal y listas de productos por proveedor.
|
*/

/**
 * Compras de Inventario (Vista General)
 * Gestión y seguimiento de todas las compras del sistema
 */
Route::middleware(['permission:compras.menu'])->group(function () {
    Route::resource('/compras', ComprasController::class);
});

/**
 * Realizar Compras (POS de Compras)
 * Interfaz para realizar nuevas compras con carrito
 */
Route::middleware(['permission:compras.realizar_compra'])->group(function () {
    Route::resource('/nuevascompras', ComprasSucursalController::class);

    // Gestión del carrito de compras
    Route::post('/nuevascompras/add', [ComprasSucursalController::class, 'addCart'])->name('nuevascompras.addCart');
    Route::post('/nuevascompras/add-by-barcode', [ComprasSucursalController::class, 'addByBarcode'])->name('nuevascompras.add-by-barcode');
    Route::post('/nuevascompras/update/{rowId}', [ComprasSucursalController::class, 'updateCart'])->name('nuevascompras.updateCart');
    Route::get('/nuevascompras/delete/{rowId}', [ComprasSucursalController::class, 'deleteCart'])->name('nuevascompras.deleteCart');
    Route::post('/nuevascompras/Vaciar-Carrito', [ComprasSucursalController::class, 'VaciarCarrito'])->name('nuevascompras.VaciarCarrito');

    // Procesamiento de compras
    Route::post('/nuevascompras/invoice/create', [ComprasSucursalController::class, 'createInvoice'])->name('nuevascompras.createInvoice');
    Route::post('/nuevascompras/order', [ComprasSucursalController::class, 'storeOrder'])->name('nuevascompras.storeOrder');
});

/**
 * Listas de Productos por Proveedor
 * Gestión de catálogos de productos específicos por proveedor
 */
Route::middleware(['permission:equivalencias.menu'])->group(function () {
    Route::resource('/listasproductosproveedor', ListProveedorController::class);
});

/**
 * Compras desde Lista de Proveedor
 * Sistema para realizar compras basadas en listas predefinidas de proveedores
 */
Route::middleware(['permission:traspasos_emitidos.menu'])->group(function () {
    Route::resource('/nuevascomprasproveedor', CompraListProveedorController::class);

    // Gestión del carrito para compras de proveedor
    Route::post('/nuevascomprasproveedor/add', [CompraListProveedorController::class, 'addCart'])->name('nuevascomprasproveedor.addCart');
    Route::post('/nuevascomprasproveedor/add-by-barcode', [CompraListProveedorController::class, 'addByBarcode'])->name('nuevascomprasproveedor.add-by-barcode');
    Route::post('/nuevascomprasproveedor/update/{rowId}', [CompraListProveedorController::class, 'updateCart'])->name('nuevascomprasproveedor.updateCart');
    Route::get('/nuevascomprasproveedor/delete/{rowId}', [CompraListProveedorController::class, 'deleteCart'])->name('nuevascomprasproveedor.deleteCart');
    Route::post('/nuevascomprasproveedor/Vaciar-Carrito', [CompraListProveedorController::class, 'VaciarCarrito'])->name('nuevascomprasproveedor.VaciarCarrito');

    // Procesamiento de órdenes de compra
    Route::post('/nuevascomprasproveedor/invoice/create', [CompraListProveedorController::class, 'createInvoice'])->name('nuevascomprasproveedor.createInvoice');
    Route::post('/nuevascomprasproveedor/order', [CompraListProveedorController::class, 'storeOrder'])->name('nuevascomprasproveedor.storeOrder');
    Route::post('/nuevascomprasproveedor/invoice/print', [CompraListProveedorController::class, 'guardar'])->name('nuevascomprasproveedor.guardar');
});

/*
|--------------------------------------------------------------------------
| Reportes y Análisis
|--------------------------------------------------------------------------
|
| Sistema de reportes, historiales y análisis de datos del negocio.
|
*/

/**
 * Historiales y Análisis
 * Reportes y análisis de ventas, inventarios y operaciones
 */
Route::middleware(['permission:vender'])->group(function () {
    // Análisis de datos (debe ir antes del resource para evitar conflictos)
    Route::get('/historiales/analisis', [HistorialesController::class, 'analisis'])->name('historiales.analisis');

    // CRUD de historiales
    Route::resource('/historiales', HistorialesController::class);
});

/*
|--------------------------------------------------------------------------
| Configuraciones Especiales y SAT
|--------------------------------------------------------------------------
|
| Gestión de equivalencias, claves SAT, conversiones y otras
| configuraciones específicas del sistema fiscal mexicano.
|
*/

/**
 * Gestión de Equivalencias
 * Sistema para manejar equivalencias entre productos
 */
Route::middleware(['permission:equivalencias.menu'])->group(function () {
    Route::resource('/equivalencias', EquivalenciasController::class);
});

/**
 * Claves SAT
 * Gestión de claves del Servicio de Administración Tributaria
 * para cumplimiento fiscal mexicano
 */
Route::middleware(['permission:claves.menu'])->group(function () {
    Route::resource('/satclaves', ClaveSatController::class);

    // Gestión de productos asociados a claves SAT
    Route::get('/satclaves/{id}/productos', [ClaveSatController::class, 'verProductos'])->name('satclaves.verproductos');
    Route::post('/productos/agregar', [ClaveSatController::class, 'agregarASatClave'])->name('productos.agregar');
    Route::delete('/productos/{producto}', [ClaveSatController::class, 'destroy_product'])->name('productos.eliminar');
});

/**
 * Sistema de Conversiones de Productos
 * Gestión de conversiones de unidades y transformaciones de productos
 */
// Conversiones
Route::middleware(['permission:conversiones.menu'])->group(function () {

    // Rutas para creación y edición de conversiones
    Route::middleware(['permission:conversiones.crear'])->group(function () {
        Route::get('/conversiones', [ConversionController::class, 'index'])->name('conversiones.index');
        Route::get('/conversiones/crear/{inventario}', [ConversionController::class, 'edit'])->name('conversiones.crear');
        Route::put('/conversiones/{id}', [ConversionController::class, 'update'])->name('conversiones.update');

    });


    // Ruta para buscar producto por código
    Route::get('/dashboard/productos/buscar-codigo/{codigo}', [ConversionController::class, 'buscarProductoPorCodigo'])->name('productos.buscar-codigo');

    // Historial de conversiones y detalles
    Route::middleware(['permission:conversion.historial'])->group(function () {
        Route::get('/conversiones/historial', [ConversionController::class, 'historial'])->name('conversiones.historial');

        Route::middleware(['permission:conversion.historial.ver'])->group(function () {
            Route::get('/conversiones/historial/ver/{id}', [ConversionController::class, 'mostrarDetalle'])->name('conversiones.historial.ver');
        });

        Route::middleware(['permission:conversion.historial.detalle'])->group(function () {
            Route::get('/conversiones/historial/detalle/{id}', [ConversionController::class, 'mostrarDetalle'])->name('conversiones.historial.detalle');
        });
    });
});

Route::prefix('configuracion')->name('configuracion.')->group(function () {
    Route::get('/negocio',          [ConfiguracionNegocioController::class, 'index'])->name('negocio');
    Route::put('/negocio',          [ConfiguracionNegocioController::class, 'update'])->name('negocio.update');
    Route::delete('/negocio/logo',  [ConfiguracionNegocioController::class, 'deleteLogo'])->name('negocio.deleteLogo');
    Route::delete('/negocio/favicon',[ConfiguracionNegocioController::class, 'deleteFavicon'])->name('negocio.deleteFavicon');
});

/*
|--------------------------------------------------------------------------
| Sistema de Respaldo de Base de Datos
|--------------------------------------------------------------------------
|
| Herramientas para crear, descargar y gestionar respaldos de la base de datos.
|
*/
Route::middleware(['permission:database.menu'])->group(function () {
    Route::get('/database/backup', [DatabaseBackupController::class, 'index'])->name('backup.index');
    Route::get('/database/backup/now', [DatabaseBackupController::class, 'create'])->name('backup.create');
    Route::get('/database/backup/download/{getFileName}', [DatabaseBackupController::class, 'download'])->name('backup.download');
    Route::get('/database/backup/delete/{getFileName}', [DatabaseBackupController::class, 'delete'])->name('backup.delete');
});

/*
|
| Rustas para registrar los abonos a una venta registrada.
|
*/
Route::middleware(['permission:compras.menu'])->group(function () {
    Route::resource('/abonos', AbonosController::class);
    Route::get('/abonos/ver/{id}', [AbonosController::class, 'verindex'])->name('abonos.verindex');
    Route::get('/abonos/ticket/{id}', [AbonosController::class, 'showTicket'])->name('abonos.ticket');

});


/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
|
| Incluye todas las rutas relacionadas con autenticación (login, registro, etc.)
| definidas en el archivo auth.php
|
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Notas para Desarrolladores
|--------------------------------------------------------------------------
|
| IMPORTANTE:
| - Todas las rutas están protegidas por middleware de permisos específicos
| - Los permisos deben estar correctamente configurados en la base de datos
| - Algunas rutas tienen dependencias entre sí (ej: cajas y transacciones)
| - Los nombres de rutas siguen el patrón: {modulo}.{accion}
|
| CONVENCIONES USADAS:
| - Resource routes para CRUD estándar
| - Middleware de permisos para control de acceso
| - Agrupación lógica por funcionalidad
| - Nombres descriptivos para rutas especiales
|
| MIDDLEWARE PRINCIPALES:
| - 'auth': Requiere autenticación
| - 'permission:xxx.menu': Requiere permiso específico para acceder al módulo
| - 'permission:xxx.accion': Requiere permiso específico para una acción
|
*/
Route::middleware(['permission:orders.menu'])->group(function () {
    Route::get('/ventas/{id}/editar',           [EditOrderController::class, 'editarOrder'])->name('ventas.editar');
    Route::get('/ventas/editar/index',          [EditOrderController::class, 'editarIndex'])->name('ventas.editar.index');
    Route::post('/ventas/editar/add-cart',      [EditOrderController::class, 'addCartEdit'])->name('ventas.editar.addCart');
    Route::post('/ventas/editar/update-cart/{rowId}', [EditOrderController::class, 'updateCartEditar'])->name('ventas.editar.updateCart');
    Route::get('/ventas/editar/delete-cart/{rowId}',  [EditOrderController::class, 'deleteCartEditar'])->name('ventas.editar.deleteCart');
    Route::post('/ventas/editar/guardar',       [EditOrderController::class, 'guardarEdicionVenta'])->name('ventas.editar.guardar');
});