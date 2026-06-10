@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
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
                        <h3 class="mb-3">Productos asociados al código SAT: {{$clave->c_ClaveProdServ}}</h3>
                        <strong>Descripción:</strong> {{$clave->descripcion}}
                    </div>
                </div>

        <!-- Selector filas por página -->
        <form action="{{ route('satclaves.verproductos', ['id' => request()->route('id')]) }}" method="GET" class="mb-4">
            <div class="row align-items-end">
                <div class="form-group col-md-2">
                    <label for="row" class="form-label">
                        <i class="ri-align-justify"></i> Filas por página
                    </label>
                    <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                        <option value="20" @if(request('row') == '20') selected @endif>20</option>
                        <option value="25" @if(request('row') == '25') selected @endif>25</option>
                        <option value="50" @if(request('row') == '50') selected @endif>50</option>
                        <option value="100" @if(request('row') == '100') selected @endif>100</option>
                    </select>
                </div>
                <div class="form-group col-md-10">
                            <label for="search"><i class="ri-search-line"></i> Buscar producto asociado</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control"
                                    value="{{ request('search') }}" placeholder="Nombre o código del producto asociado">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('satclaves.verproductos', ['id' => request()->route('id')]) }}" class="input-group-text bg-danger" title="Limpiar búsqueda">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                </div>
        </form>

        <!-- Buscador para asociar un producto con clave SAT -->
        <form action="{{ route('productos.agregar') }}" method="POST" class="mb-4">
            <label for="search" class="form-label">
                <i class="ri-links-line me-1"></i> Asociar producto con clave SAT
            </label>
            @csrf
            <input type="hidden" name="satclave_id" value="{{ request()->route('id') }}">

            <div class="input-group">
                <input type="text" name="busqueda" class="form-control" placeholder="Agregar producto por nombre, código o código de barra" required>
                <button type="submit" class="input-group-text bg-primary text-white">
                    <i class="fa fa-plus me-1"></i> Agregar Producto
                </button>
            </div>
        </form>

            <!-- Lista de productos -->
            @if ($productos->isEmpty())
                <div class="alert alert-info">
                    No hay productos asociados a esta clave SAT.
                </div>
            @else
            <ul class="list-group">
                @foreach ($productos as $producto)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $producto->product_name }}</strong>
                            <br>
                            <small class="text-muted">Código del producto: {{ $producto->product_code }}</small>
                        </div>

                        <div class="d-flex align-items-center">
                            {{-- <span class="badge bg-secondary me-3">ID: {{ $producto->id }}</span> --}}

                            <!-- Formulario para eliminar -->
                            <form action="{{ route('productos.eliminar', $producto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto asociado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="ri-delete-bin-6-line"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                {{ $productos->appends(request()->except('page'))->links() }}
            </div>
        @endif
        <br>
        <div>

        <!-- Al final del contenedor principal -->
        <div class="mt-4 text-end">
            <a href="{{ route('satclaves.index') }}" class="btn btn-secondary">
                   <i class="fas fa-arrow-left me-1"></i> Regresar
            </a>
        </div>
        <br>
    </div>
</div>

@endsection
