@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">

            {{-- Mensajes de Éxito y Error --}}
            @if (session()->has('success'))
                <div id="alert-success" class="alert text-white bg-success d-flex justify-content-between align-items-center" role="alert">
                    <div class="iq-alert-text mb-0">{{ session('success') }}</div>
                    <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div id="alert-error" class="alert text-white bg-danger d-flex justify-content-between align-items-center" role="alert">
                    <div class="iq-alert-text mb-0">{{ session('error') }}</div>
                    <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            {{-- Encabezado --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">Usuarios
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Administra los perfiles de acceso de los usuarios autorizados, permitiendo gestionar permisos y actividades.">
                        </i>
                    </h3>
                </div>
                <div class="d-flex gap-2">
                    @can('usuarios.crear')
                        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus mr-2"></i> Crear Usuario</a>
                    @endcan
                </div>
            </div>

            {{-- Filtros --}}
            <form action="{{ route('users.index') }}" method="get" class="mb-4">
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

                    <div class="form-group col-md-4">
                        <label for="branch"><i class="ri-store-2-line me-1"></i> Sucursal</label>
                        <select class="form-control" name="branch" id="branch" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(request('branch') == $branch->id)>
                                    {{ $branch->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="search"><i class="ri-search-line"></i> Buscar</label>
                        <div class="input-group">
                            <input type="text" id="search" name="search" class="form-control" placeholder="Nombre o Usuario" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('users.index') }}" class="input-group-text bg-danger" title="Limpiar búsqueda">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Mantener valores --}}
                    <input type="hidden" name="row" value="{{ request('row', 10) }}">
                </div>
            </form>

            {{-- Tabla de Usuarios --}}
            <div class="table-responsive rounded shadow-sm border mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="ligth ligth-data">
                            <th class="text-center">#</th>
                            <th class="text-center">Foto</th>
                            <th>@sortablelink('name', 'Nombre')</th>
                            <th class="text-center">@sortablelink('username', 'Usuario')</th>
                            <th class="text-center">@sortablelink('email', 'Correo')</th>
                            <th class="text-center">@sortablelink('Sucursal', 'Sucursal')</th>
                            <th class="text-center">Rol</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($users as $item)
                            <tr>
                                <td class="text-center">{{ (($users->currentPage() - 1) * $users->perPage()) + $loop->iteration }}</td>
                                <td class="text-center">
                                    <img class="avatar-60 rounded" src="{{ $item->photo ? asset('storage/profile/'.$item->photo) : asset('assets/images/user/1.png') }}">
                                </td>
                                <td>{{ ucwords(strtolower($item->name . ' ' . $item->apellido_p)) }}</td>
                                <td class="text-center">{{ $item->username }}</td>
                                <td class="text-center">{{ $item->email }}</td>
                                <td class="text-center">{{ $item->branch_name }}</td>
                                <td class="text-center">
                                    @foreach ($item->roles as $role)
                                        <span class="badge bg-danger">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center align-middle">
                                    <form action="{{ route('users.destroy', $item->username) }}" method="POST" id="delete-form-{{ $item->id }}" class="d-flex justify-content-center align-items-center gap-2 m-0">
                                        @csrf
                                        @method('delete')

                                        @can('usuarios.editar')
                                            <a class="btn btn-success me-2 mr-2" data-toggle="tooltip" data-placement="top" title="Editar Usuario"
                                            href="{{ route('users.edit', $item->username) }}">
                                                <i class="ri-pencil-line mr-0"></i>
                                            </a>
                                        @endcan

                                        @php
                                            $count = App\Models\Caja::where('user_id', $item->id)->count();
                                        @endphp

                                        @if ($count == 0)
                                            @can('usuarios.eliminar')
                                                <button type="button" class="btn btn-warning btn-delete me-2 mr-2" data-toggle="tooltip" data-placement="top"
                                                        title="Eliminar" data-id="{{ $item->id }}">
                                                    <i class="ri-delete-bin-line mr-0"></i>
                                                </button>
                                            @endcan
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron datos relacionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

{{-- Modal SweetAlert para eliminar --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Estás seguro de eliminar este usuario?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${itemId}`).submit();
                }
            });
        });
    });
});
</script>
