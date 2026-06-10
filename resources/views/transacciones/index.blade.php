@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div  id="alert-success" class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
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
                    <h3 class="mb-3">Transacciones Generales
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Consulta y analiza los movimientos económicos registrados en el sistema, incluyendo ventas, compras, pagos y otros ingresos o egresos.">
                        </i>
                    </h3>
                </div>
                 {{-- <div>
                    <a href="{{ route('transacciones.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Nueva Transacción</a>
                </div> --}}
            </div>
        </div>

        {{-- Filtros y busqueda --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('transacciones.index') }}" method="get">
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
                        <label for="caja"><i class="ri-exchange-line me-1"></i> Tipo de Transacción</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="ri-archive-line"></i></span>
                            </div>
                            <select class="form-control" id="tipo_transaccion" name="tipo_transaccion" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                <option value="" disabled selected hidden>Selecciona Un Tipo</option>
                                <option value="venta" {{ request('tipo_transaccion') == 'venta' ? 'selected' : '' }}>Venta</option>
                                <option value="devolucion" {{ request('tipo_transaccion') == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                                <option value="Venta cancelada" {{ request('tipo_transaccion') == 'Venta cancelada' ? 'selected' : '' }}>Venta Cancelada</option>
                                <option value="retiro" {{ request('tipo_transaccion') == 'retiro' ? 'selected' : '' }}>Retiro en sucursal</option>
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
                        <label for="search"><i class="ri-search-line"></i> Buscar</label>
                        <div class="input-group">
                        <input type="text" id="search" class="form-control" name="search" placeholder="Buscar" value="{{ request('search') }}" oninput="document.getElementById('filtroForm').submit()">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('transacciones.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
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
                        <tr class="ligth ligth-data">
                            <th>#</th>
                            <th>Nº de caja</th>
                            <th>Responsable</th>
                            <th>@sortablelink('fecha', 'Fecha')</th>
                            <th>Tipo de Transacción</th>
                            <th>Metodo de Pago</th>
                            <th>Total</th>
                            <th>Monto Pagado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($transacciones as $transaccione)
                            <tr>
                                <td>{{ (($transacciones->currentPage() - 1) * $transacciones->perPage()) + $loop->iteration }}</td>
                                <td>{{ $transaccione->numero_caja}}</td>
                                <td>{{ $transaccione->nombre_usuario}}</td>
                                <td>{{ \Carbon\Carbon::parse($transaccione->fecha)->format('d-m-Y') }}</td>
                                <td>{{ ucfirst( $transaccione->tipo_transaccion )}}</td>
                                <td>{{ucfirst( $transaccione->metodo_pago) }}</td>
                                <td>${{ number_format($transaccione->total, 2) }}</td>
                                <td>${{ number_format($transaccione->monto, 2) }}</td>
                                <td>
                                    <form action="{{ route('transacciones.destroy', $transaccione->id) }}" method="POST" style="margin-bottom: 5px">
                                    @method('delete')
                                    @csrf
                                    <div class="d-flex align-items-center list-action">
                                        <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver"
                                            href="{{ route('transacciones.show', $transaccione->id) }}"><i class="ri-eye-line mr-0"></i>
                                        </a>
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
            {{ $transacciones->links() }}
        </div>
    </div>
</div>
@endsection
