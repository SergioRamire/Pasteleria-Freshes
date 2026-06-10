@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div id="alert-success" class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif
                 {{-- Abrir reporte de impresión en nueva pestaña --}}
            @if (session('imprimir_url'))
                <script>
                    window.open('{{ session('imprimir_url') }}', '_blank');
                </script>
            @endif
            @if (session()->has('error'))
                <div id="alert-error" class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">Mis Cajas
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Revisa y administra el estado de tu caja actual, incluyendo movimientos, ingresos y egresos registrados durante tu turno.">
                        </i>
                    </h3>
                </div>
                 <div>
                    <a href="{{ route('mis_cajas.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Abrir Caja</a>
                    {{-- <a href="{{ route('inventarios.index') }}" class="btn btn-danger add-list"><i class="fa-solid fa-trash mr-3"></i>Borrar búsqueda</a> --}}
                </div>
            </div>
        </div>

        {{-- Filtros y busqueda --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('mis_cajas.index') }}" method="get">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-2">
                        <label for="row" class="form-label">
                            <i class="ri-align-justify"></i> Filas por página
                        </label>
                        <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                            <option value="10" @if(request('row') == '10') selected @endif>10</option>
                            <option value="25" @if(request('row') == '25') selected @endif>25</option>
                            <option value="50" @if(request('row') == '50') selected @endif>50</option>
                            <option value="100" @if(request('row') == '100') selected @endif>100</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="caja"><i class="ri-repeat-line me-1"></i> Estado de la Caja</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ri-archive-line"></i></span>
                            </div>
                            <select class="form-control" id="caja" name="caja" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                <option value="" disabled selected hidden>Selecciona Un Estado</option>
                                <option value="abierta" {{ request('caja') == 'abierta' ? 'selected' : '' }}>Caja Abierta</option>
                                <option value="cerrada" {{ request('caja') == 'cerrada' ? 'selected' : '' }}>Caja Cerrada</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="form-group">
                            <i class="ri-calendar-line"></i> Fecha
                        </label>
                        <input type="date" name="fecha" id="fecha" class="form-control" value="{{ request('fecha') }}" onchange="document.getElementById('filtroForm').submit()" max="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="search"><i class="ri-search-line"></i> Buscar por Responsable o Sucursal</label>
                        <div class="input-group">
                        <input type="text" id="search" class="form-control" name="search" placeholder="Buscar" value="{{ request('search') }}" oninput="document.getElementById('filtroForm').submit()">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('mis_cajas.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabla --}}
        <div class="col-lg-12">
            <div class="table-responsive rounded shadow-sm border mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data text-center">
                            <th>#</th>
                            <th>Nº de caja</th>
                            <th>Responsable</th>
                            <th>@sortablelink('fecha', 'Fecha')</th>
                            <th>@sortablelink('hora_apertura', 'Horario')</th>
                            {{-- <th>@sortablelink('hora_cierre', 'Hora C')</th> --}}
                            <th>Sucursal</th>
                            <th>Caja</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body text-center">
                        @forelse ($cajas as $caja)
                            <tr>
                                <td>{{ (($cajas->currentPage() - 1) * $cajas->perPage()) + $loop->iteration }}</td>
                                <td>{{ $caja->numero_caja}}</td>
                                <td>{{ $caja->nombre_usuario}}</td>
                                <td>{{ \Carbon\Carbon::parse($caja->fecha)->format('d-m-Y') }}</td>
                                <td>
                                    {{ $caja->hora_apertura ? \Carbon\Carbon::parse($caja->hora_apertura)->format('h:i A') : 'Sin apertura' }} -
                                    {{ $caja->hora_cierre ? \Carbon\Carbon::parse($caja->hora_cierre)->format('h:i A') : 'Sin cierre' }}
                                </td>
                                <td>{{ $caja->sucursal->nombre}}</td>
                                 <td>
                                    @if($caja->estado == 'abierta')
                                        <span class="badge badge-success">Abierta</span>
                                    @elseif($caja->estado == 'cerrada')
                                        <span class="badge badge-danger">Cerrada</span>
                                    @endif
                                    {{-- $inventario->sucursal }} --}}
                                </td>
                                <td>
                                    <form action="{{ route('mis_cajas.destroy', $caja->id) }}" method="POST" style="margin-bottom: 5px">
                                    @method('delete')
                                    @csrf
                                    <div class="d-flex align-items-center list-action">
                                        <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Caja"
                                            href="{{ route('mis_cajas.show', $caja->id) }}"><i class="ri-eye-line mr-0"></i>
                                        </a>
                                         @if($caja->estado == 'abierta')
                                            <a class="btn btn-primary  mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Nueva Transacción"
                                                href="{{ route('cajas_transacciones.create', $caja->id) }}">
                                                <i class="ri-exchange-dollar-line mr-0"></i>
                                            </a>
                                            <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cerrar Caja"
                                                href="{{ route('mis_cajas.edit', $caja->id) }}"><i class="ri-lock-2-line mr-0"></i>
                                            </a>

                                        @endif

                                            {{-- <button type="submit" class="btn btn-warning mr-2 border-none" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="ri-delete-bin-line mr-0"></i></button> --}}
                                    </div>
                                </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron resultados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $cajas->links() }}
        </div>
    </div>
</div>
@endsection
