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
                    <h3 class="mb-3">Clientes
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Consulta, registra y edita la información de tus clientes para facilitar futuras ventas y mantener un historial detallado.">
                        </i>
                    </h3>
                </div>
                <div>
                    @can('clientes.crear')
                    <a href="{{ route('customers.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Agregar Cliente</a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('customers.index') }}" method="GET">
                <div class="row align-items-end">
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

                    <div class="form-group col-md-10">
                        <label for="search"><i class="ri-search-line"></i> Buscar Cliente</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Nombre o Nombre Empresa">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('customers.index') }}" class="input-group-text bg-danger" title="Limpiar búsqueda">
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
                            <th>N°</th>
                            <th>Foto</th>
                            <th>@sortablelink('name', 'Nombre')</th>
                            <th>@sortablelink('email', 'Correo Electrónico')</th>
                            <th>@sortablelink('phone', 'Teléfono')</th>
                            <th>@sortablelink('shopname', 'Nombre Empresa')</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ (($customers->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                                <td>
                                    <img class="avatar-60 rounded" src="{{ $customer->photo ? asset('storage/customers/'.$customer->photo) : asset('assets/images/user/1.png') }}">
                                </td>
                                <td>{{ucwords(strtolower( $customer->name)) }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->shopname }}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="Inspeccionar Cliente"
                                            href="{{ route('customers.show', $customer->id) }}"><i class="ri-eye-line mr-0"></i>
                                        </a>
                                        @can('clientes.editar')
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Editar Cliente"
                                                href="{{ route('customers.edit', $customer->id) }}"><i class="ri-pencil-line mr-0"></i>
                                            </a>
                                        @endcan
                                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="margin-bottom: 5px">
                                            @method('delete')
                                            @csrf
                                            @can('clientes.eliminar')
                                                @php
                                                    $count = App\Models\Order::where('customer_id', $customer->id)->count();
                                                @endphp
                                                @if ($count == 0)
                                                    <button type="submit" class="badge bg-warning mr-2 border-none" onclick="return confirm('¿Estás seguro de que deseas eliminar a {{ $customer->name }}? No podrá deshacer esta acción.')" data-toggle="tooltip" title="Eliminar">
                                                        <i class="ri-delete-bin-line mr-0"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tipo de dato cliente no encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $customers->links() }}
        </div>
    </div>
    <!-- Page end  -->
</div>

@endsection
