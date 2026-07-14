<!-- Sidebar -->
<div class="iq-sidebar sidebar-default">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between px-3 py-1 border-bottom">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-white text-decoration-none" aria-label="Ir al Panel de Control">

            {{-- Logo: BD o default --}}
            @if(!empty($configNegocio?->logo))
                <img src="{{ asset('storage/' . $configNegocio->logo) }}"
                     alt="Logo principal"
                     class="img-fluid"
                     style="height: 80px; width: auto; object-fit: contain;">
            @else
                <img src="{{ asset('assets/images/logo/logo.png') }}"
                     alt="Logo principal"
                     class="img-fluid"
                     style="height: 80px; width: auto; object-fit: contain;">
            @endif

            {{-- Nombre: BD o default oculto --}}
            <i4 class="logo-title light-logo ml-1 mb-0">
                {{ $configNegocio?->nombre_negocio ?? 'AcuarioA.' }}
            </i4>

        </a>
        <!-- Botón de hamburguesa -->
        <div class="iq-menu-bt-sidebar ml-2" role="button" >
            <i class="las la-bars wrapper-menu"></i>
        </div>
    </div>

    <div class="data-scrollbar" data-scroll="1">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">

                {{-- Panel de control --}}
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="svg-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00aaff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                        <span class="ml-4">Dashboard</span>
                    </a>
                </li>

                {{-- Punto de venta --}}
                @can('vender')
                    <li class="{{ Request::is('ventas*') ? 'active' : '' }}">
                        <a href="{{ route('ventas.index') }}">
                            <i class="fa-solid fa-shopping-cart"></i>
                            <span class="ml-3">Punto de venta</span>
                        </a>
                    </li>
                @endcan
                @can('vender')
                    <li class="{{ Request::is('cotizaciones*') ? 'active' : '' }}">
                        <a href="{{ route('cotizaciones.index') }}">
                            <i class="fa-solid fa-file-contract"></i>
                            <span class="ml-3">Cotización</span>
                        </a>
                    </li>
                @endcan

                <hr>

                {{-- Pedidos --}}
                @can('orders.menu')
                <li>
                    <a href="#orders" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="fa-solid fa-basket-shopping"></i>
                        <span class="ml-3">Pedidos</span>
                        @include('components.arrow')
                    </a>
                    <ul id="orders" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ Request::is('orders/pending*') ? 'active' : '' }}">
                            <a href="{{ route('order.pendingOrders') }}"><i class="fa-solid fa-arrow-right"></i>Pedidos Pedientes</a>
                        </li>
                        <li class="{{ Request::is('orders/complete*') ? 'active' : '' }}">
                            <a href="{{ route('order.completeOrders') }}"><i class="fa-solid fa-arrow-right"></i>Pedidos Completos</a>
                        </li>
                        <li class="{{ Request::is('pending/due*') ? 'active' : '' }}">
                            <a href="{{ route('order.pendingDue') }}"><i class="fa-solid fa-arrow-right"></i>Pedidos No Pagados</a>
                        </li>
                        <li class="{{ Request::is('orders/cancelled*') ? 'active' : '' }}">
                            <a href="{{ route('order.cancelledOrders') }}"><i class="fa-solid fa-arrow-right"></i>Pedidos Cancelados</a>
                        </li>
                    </ul>
                </li>
                @endcan

                @can('myinventarios.menu')
                <li>
                    <a href="#inventarios" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        	<i class="fa-solid fa-dolly"></i>
                        <span class="ml-3">Inventarios</span>
                        @include('components.arrow')
                    </a>
                    <ul id="inventarios" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                        @can('myinventarios.menu')
                            <li class="{{ Request::is('myinventarios*') ? 'active' : '' }}">
                            <a href="{{ route('myinventarios.index') }}"><i class="fa-solid fa-dolly"></i>Inventario</a>
                        </li>
                        @endcan

                        @can('inventarios.menu')
                            <li class="{{ Request::is('inventarios*') ? 'active' : '' }}">
                                <a href="{{ route('inventarios.index') }}"><i class="fa-solid fa-warehouse"></i>Inventario General</a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @canany('traspasos_emitidos.menu' , 'traspasos_recibidos.menu')
                <li>
                    <a href="#traspasos" class="collapsed" data-toggle="collapse" aria-expanded="false" title="Traspasos">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span class="ml-3">Traspasos</span>
                        @include('components.arrow')
                    </a>
                    <ul id="traspasos" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        @can('traspasos_emitidos.menu')
                            <li class="{{ Request::is('listTraspasos*') ? 'active' : '' }}">
                                <a href="{{ route('listTraspasos.index') }}"> <i class="fa-solid fa-paper-plane"></i>Traspasos Emitidos</a>
                            </li>
                        @endcan
                        @can('traspasos_recibidos.menu')
                            <li class="{{ Request::is('listTraspasosRecibidos*') ? 'active' : '' }}">
                                <a href="{{ route('listTraspasosRecibidos.index') }}"><i class="fa-solid fa-envelope-open-text"></i>Traspaso Solicitados</a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                <hr>

                {{-- Módulos individuales --}}
                {{-- @can('employee.menu')
                <li class="{{ Request::is('employees*') ? 'active' : '' }}">
                    <a href="{{ route('employees.index') }}"><i class="fa-solid fa-users"></i> <span class="ml-3">Empleados</span></a>
                </li>
                @endcan --}}

                @can('mi_caja.menu')
                    <li class="{{ Request::is('mis_cajas*') ? 'active' : '' }}">
                        <a href="{{ route('mis_cajas.index') }}"><i class="fas fa-cash-register me-1"></i><span class="ml-3">Mi Caja</span></a>
                    </li>
                @endcan

                @canany('cajas_general.menu', 'cajas_sucursal.menu')
                <li>
                    <a href="#cajas" class="collapsed" data-toggle="collapse" aria-expanded="false" title="Corte de cajas">
                        <i class="ri-archive-fill"></i>
                        <span class="ml-3">Cajas</span>
                        @include('components.arrow')
                    </a>
                    <ul id="cajas" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        @can('cajas_general.menu')
                            <li class="{{ Request::is('cajas*') ? 'active' : '' }}">
                                <a href="{{ route('cajas_sucursales.index') }}"><i class="fa-solid fa-arrow-right"></i>Cajas por Sucursales</a>
                            </li>
                        @endcan
                        @can('cajas_sucursal.menu')
                            <li class="{{ Request::is('caja_sucursal*') ? 'active' : '' }}">
                                <a href="{{ route('caja_sucursal.index') }}"><i class="fa-solid fa-arrow-right"></i>Caja En Sucursal</a>
                            </li>
                        @endcan
                        <li class="{{ Request::is('transacciones*') ? 'active' : '' }}">
                            <a href="{{ route('transacciones.index') }}"><i class="fa-solid fa-arrow-right"></i>Transacciones</a>
                        </li>
                    </ul>
                </li>
                @endcanany

                <hr>

                {{-- Productos --}}
                @canany('productos.menu', 'categorias.menu')
                <li>
                    <a href="#products" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span class="ml-3">Productos</span>
                        @include('components.arrow')
                    </a>
                    <ul id="products" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        @can('productos.menu')
                            <li class="{{ Request::is('products') ? 'active' : '' }}">
                                <a href="{{ route('products.index') }}"><i class="fa-solid fa-arrow-right"></i>Productos</a>
                            </li>
                        @endcan

                        @can('categorias.menu')
                            <li class="{{ Request::is('categories*') ? 'active' : '' }}">
                                <a href="{{ route('categories.index') }}"><i class="fa-solid fa-arrow-right"></i>Categorías</a>
                            </li>
                        @endcan

                        {{--
                        @can('productos.crear')
                            <li class="{{ Request::is('products/create') ? 'active' : '' }}">
                                <a href="{{ route('products.create') }}"><i class="fa-solid fa-arrow-right"></i>Crear Producto</a>
                            </li>
                        @endcan
                        --}}
                    </ul>
                </li>
                @endcanany

                @can('compras.menu')
                    <li>
                        <a href="#compras" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <i class="fa-solid fa-cart-plus"></i>
                            <span class="ml-3">Compras Proveedor</span>
                            @include('components.arrow')
                        </a>
                        <ul id="compras" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            <li class="{{ Request::is('compras*') ? 'active' : '' }}">
                                <a href="{{ route('compras.index') }}"> <i class="fa-solid fa-bag-shopping"></i>Compras</a>
                            </li>
                            @can('lista.menu')
                                <li class="{{ Request::is('listasproductosproveedor*') ? 'active' : '' }}">
                                    <a href="{{ route('listasproductosproveedor.index') }}"> <i class="fa-solid fa-bag-shopping"></i>Lista Para Proveedor</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('equivalencias.menu')
                    <li class="{{ Request::is('equivalencias*') ? 'active' : '' }}">
                        <a href="{{ route('equivalencias.index') }}"><i class="ri-scales-3-line"></i><span class="ml-3">Equivalencias</span></a>
                    </li>
                @endcan

                @can('conversiones.menu')
                    <li>
                        <a href="#conversiones" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-shuffle-line"></i>
                            <span class="ml-3">Conversiones</span>
                            @include('components.arrow')
                        </a>
                        <ul id="conversiones" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            @can('conversiones.crear')
                                <li class="{{ Request::is('conversiones') || Request::is('conversiones/*') ? 'active' : '' }}">
                                    <a href="{{ route('conversiones.index') }}">
                                        <i class="ri-shuffle-line"></i> Conversiones
                                    </a>
                                </li>
                            @endcan

                            @can('conversion.historial')
                                <li class="{{ request()->routeIs('conversiones.historial') ? 'active' : '' }}">
                                    <a href="{{ route('conversiones.historial') }}">
                                        <i class="ri-history-line"></i> Historial
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                @can('claves.menu')
                    <li class="{{ Request::is('satclaves*') ? 'active' : '' }}">
                        <a href="{{ route('satclaves.index') }}"><i class="fas fa-key"></i><span class="ml-3">Claves Sat</span></a>
                    </li>
                @endcan

                <hr>

                @can('clientes.menu')
                <li class="{{ Request::is('customers*') ? 'active' : '' }}">
                    <a href="{{ route('customers.index') }}"><i class="ri-user-search-fill"></i><span class="ml-3">Clientes</span></a>
                </li>
                @endcan

                @can('proveedores.menu')
                <li class="{{ Request::is('suppliers*') ? 'active' : '' }}">
                    <a href="{{ route('suppliers.index') }}"><i class="ri-user-3-line"></i><span class="ml-3">Proveedores</span></a>
                </li>
                @endcan

                 @can('marcas.menu')
                <li class="{{ Request::is('marcas*') ? 'active' : '' }}">
                    <a href="{{ route('marcas.index') }}"><i class="fas fa-tags"></i><span class="ml-3">Marcas</span></a>
                </li>
                @endcan

                @can('sucursales.menu')
                    <li class="{{ Request::is('sucursales*') ? 'active' : '' }}">
                        <a href="{{ route('sucursales.index') }}"><i class="ri-store-3-fill"></i><span class="ml-3">Sucursales</span></a>
                    </li>
                @endcan

                @can('historial.menu')
                <li class="{{ Request::is('historial*') ? 'active' : '' }}">
                    <a href="{{ route('historiales.index') }}"><i class="fa-solid fa-clock"></i> <span class="ml-3">Historial</span></a>
                </li>
                @endcan

                {{-- Salario --}}
                {{-- @can('salary.menu')
                <li>
                    <a href="#advance-salary" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="fa-solid fa-cash-register"></i>
                        <span class="ml-3">Salario</span>
                        @include('components.arrow')
                    </a>
                    <ul id="advance-salary" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ Request::is('advance-salary') || Request::is('advance-salary/*/edit') ? 'active' : '' }}">
                            <a href="{{ route('advance-salary.index') }}"><i class="fa-solid fa-arrow-right"></i> Salario anticipado</a>
                        </li>
                        <li class="{{ Request::is('advance-salary/create*') ? 'active' : '' }}">
                            <a href="{{ route('advance-salary.create') }}"><i class="fa-solid fa-arrow-right"></i> Crear salario anticipado</a>
                        </li>
                        <li class="{{ Request::is('pay-salary') ? 'active' : '' }}">
                            <a href="{{ route('pay-salary.index') }}"><i class="fa-solid fa-arrow-right"></i> Pagar salario</a>
                        </li>
                        <li class="{{ Request::is('pay-salary/history*') ? 'active' : '' }}">
                            <a href="{{ route('pay-salary.payHistory') }}"><i class="fa-solid fa-arrow-right"></i> Historial de pago</a>
                        </li>
                    </ul>
                </li>
                @endcan --}}

                {{-- Asistencia --}}
                {{-- @can('attendence.menu')
                <li>
                    <a href="#attendence" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span class="ml-3">Asistencia</span>
                        @include('components.arrow')
                    </a>
                    <ul id="attendence" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ Request::is('employee/attendence') ? 'active' : '' }}">
                            <a href="{{ route('attendence.index') }}"><i class="fa-solid fa-arrow-right"></i> Toda la asistencia</a>
                        </li>
                        <li class="{{ Request::is('employee/attendence/*') ? 'active' : '' }}">
                            <a href="{{ route('attendence.create') }}"><i class="fa-solid fa-arrow-right"></i> Crear asistencia</a>
                        </li>
                    </ul>
                </li>
                @endcan --}}

                <hr>

                {{-- Roles y permisos --}}
                @can('roles.menu')
                <li>
                    <a href="#permission" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="fa-solid fa-key"></i>
                        <span class="ml-3">Rol & Permiso</span>
                        @include('components.arrow')
                    </a>
                    <ul id="permission" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ Request::is('permission*') ? 'active' : '' }}">
                            <a href="{{ route('permission.index') }}"><i class="fa-solid fa-arrow-right"></i>Permisos</a>
                        </li>
                        <li class="{{ Request::is('role*') ? 'active' : '' }}">
                            <a href="{{ route('role.index') }}"><i class="fa-solid fa-arrow-right"></i>Roles</a>
                        </li>
                        <li class="{{ Request::is('role/permission*') ? 'active' : '' }}">
                            <a href="{{ route('rolePermission.index') }}"><i class="fa-solid fa-arrow-right"></i>Papel En Permisos</a>
                        </li>
                    </ul>
                </li>
                @endcan

                @can('usuarios.menu')
                <li class="{{ Request::is('users*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}"><i class="fa-solid fa-users"></i> <span class="ml-3">Usuarios</span></a>
                </li>
                @endcan

                @can('usuarios.menu')
                    <li class="{{ request()->routeIs('configuracion.negocio') ? 'active' : '' }}">
                        <a href="{{ route('configuracion.negocio') }}">
                            <i class="ri-settings-3-line"></i>
                            <span class="ml-3">Configuración</span>
                        </a>
                    </li>
                @endcan

                 <li class="" @disabled(false)>
                    <a href=""> <span class="ml-3"></span></a>
                </li>

                {{-- @can('database.menu')
                <li class="{{ Request::is('database/backup*') ? 'active' : '' }}">
                    <a href="{{ route('backup.index') }}"><i class="fa-solid fa-database"></i> <span class="ml-3">Backup</span></a>
                </li>
                @endcan --}}

            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>

<style>
    .logo-title {
        font-family: 'Arial Black', Arial, sans-serif;
        color: #1268be; /* Azul rey oscuro */
    }


    .separator-line {
        border: none;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin: 0 20px;
    }

    .menu-title {
        padding: 15px 20px 5px;
        color: rgba(255, 255, 255, 0.6);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        list-style: none;
    }

    .iq-menu li a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 0;
    }

    .iq-menu li a:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        padding-left: 25px;
    }

    .iq-menu li.active > a,
    .iq-menu li.active-parent > a {
        background-color: rgba(0, 170, 255, 0.2);
        color: #00aaff;
        border-right: 3px solid #00aaff;
    }

    .iq-submenu {
        background-color: rgba(0, 0, 0, 0.2);
    }

    .iq-submenu li a {
        padding: 10px 20px 10px 50px;
        font-size: 14px;
    }

    .iq-submenu li a:hover {
        padding-left: 55px;
        background-color: rgba(255, 255, 255, 0.05);
    }

    .iq-submenu li.active > a {
        background-color: rgba(0, 170, 255, 0.3);
        color: #00aaff;
    }


    .submenu-arrow {
        margin-left: auto;
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .collapsed .submenu-arrow {
        transform: rotate(-90deg);
    }

    .sidebar-bottom-spacer {
        height: 20px;
    }

    /* Iconos consistentes */
    .iq-menu i {
        width: 20px;
        text-align: center;
        font-size: 16px;
    }

    .iq-submenu i {
        width: 16px;
        font-size: 14px;
    }

    i4 {
    opacity: 0; /* 0 = transparente total, 1 = opaco total */
    }

</style>
