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
            @if (session()->has('error'))
                <div  id="alert-error" class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif
            {{-- <h4 class="mb-3">Solictar Traspasos</h4> --}}
            <h4 class="mb-3">
                Solicitar Traspaso
                <i class="fas fa-info-circle text-primary"
                data-toggle="tooltip"
                data-placement="right"
                title="Selecciona los productos que deseas agregar al traspaso. Puedes filtrarlos por Categoría o Sucursal.">
                </i>
            </h4>
        </div>

        {{-- Tabla de productos - IZQUIERDA --}}
        <div class="col-lg-12 col-md-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <form action="{{ route('traspasos.index') }}" method="GET">
                        <div class="row d-flex align-items-end justify-content-between mb-3">

                            {{-- Cantidad por página --}}
                            <div class="form-group col-md-2 mb-2">
                                <label for="row" class="form-label">
                                    <i class="ri-align-justify"></i> Fila por página
                                </label>
                                    <select class="form-control" name="row" id="row">
                                    <option value="10" @if(request('row') == '10') selected @endif>10</option>
                                    <option value="25" @if(request('row') == '25') selected @endif>25</option>
                                    <option value="50" @if(request('row') == '50') selected @endif>50</option>
                                    <option value="100" @if(request('row') == '100') selected @endif>100</option>
                                </select>
                            </div>

                            {{-- Categoría --}}
                            <div class="form-group col-md-3 mb-2">
                                <label for="category_id" class="form-label"><i class="ri-archive-line me-1"></i> Categoría</label>
                                <select name="category_id" id="category_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Sucursal --}}
                            <div class="form-group col-md-3 mb-2">
                                <label for="branche_id" class="form-label"><i class="ri-building-line me-1"></i> Sucursal</label>
                                <select name="branche_id" id="branche_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($sucursales as $sucursale)
                                        <option value="{{ $sucursale->id }}" @if(request('branche_id') == $sucursale->id) selected @endif>
                                            {{ $sucursale->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Buscar --}}
                            <div class="form-group col-md-4 mb-2">
                                <label for="search"><i class="ri-search-line"></i>Buscar Producto</label>
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <a href="{{ route('traspasos.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Escaneo --}}
                            <div class="form-group col-md-12">
                                <label class="control-label align-self-center" for="barcode"><i class="ri-barcode-line me-1"></i> Escanear código de barras</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-barcode"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="barcode" class="form-control" name="barcode" placeholder="Escanea el código" autocomplete="off" autofocus>
                                </div>
                            </div>
                    </form>

                    <div class="table-responsive rounded mb-3 border-none">
                        <table class="table table-sm mb-0 small">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th>N°.</th>
                                    <th>Sucursal</th>
                                    <th>Código Barras</th>
                                    <th>Código</th>
                                    <th>@sortablelink('product_name', 'nombre')</th>
                                    <th>Unidad</th>
                                    <th>@sortablelink('stock', 'stock')</th>
                                    <th>Precio venta</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr>
                                    {{-- <td>{{ (($products->currentPage() * 10) - 10) + $loop->iteration }}</td> --}}
                                    <td>{{$product->inventario_id}}</td>
                                    <td>{{ $product->sucursal_nombre }}</td>
                                    <td>{{ $product->codigo_barras }}</td>
                                    <td>{{ $product->product_code }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>
                                        {{ $product->equivalencia ?? 'Sin unidad' }}
                                    </td>

                                    <td>{{ $product->stock }}</td>
                                    <td class="text-center">${{ number_format($product->selling_price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('traspasos.addCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="inventario_id" value="{{ $product->inventario_id }}">
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <input type="hidden" name="name" value="{{ $product->product_name }}">
                                            <input type="hidden" name="price" value="{{ $product->selling_price }}">
                                            <input type="hidden" name="dealer_price" value="{{ $product->dealer_price }}">
                                            <button type="submit" class="btn btn-primary btn-sm" title="Agregar al carrito"><i class="far fa-plus"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="alert text-white bg-danger" role="alert">
                                            <div class="iq-alert-text">Datos no encontrados.</div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>

        {{-- Tabla del carrito - DERECHA --}}
        <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa-solid fa-cart-shopping me-2"></i> Carrito de Traspasos</h5>
        </div>

        <div class="card-body">
            {{-- Tabla de productos en el carrito --}}
            <div class="table-responsive1 rounded shadow-sm border mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Producto</th>
                            <th>Codigo Producto</th>
                            <th>Unidad</th>
                            <th style="min-width: 120px;">Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productItem as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">
                                    {{ $item->options->codigo_producto }}
                                </td>
                                <td class="text-center">
                                    {{ $item->options->unidad ?? 'Sin unidad' }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('traspasos.updateCart', $item->rowId) }}" method="POST" class="d-flex justify-content-center align-items-center">
                                        @csrf
                                        <input type="hidden" name="inventario_id" value="{{ $item->options->inventario_id }}">
                                        <input type="number" name="qty" class="form-control form-control-sm text-center" style="max-width: 70px;" min="1" value="{{ old('qty', $item->qty) }}">
                                        <button type="submit" class="btn btn-sm btn-outline-success ms-2" title="Actualizar cantidad">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('traspasos.deleteCart', $item->rowId) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este producto del carrito?')" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay productos en el carrito.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @php
                $cart = Cart::instance('traspaso');
            @endphp

            {{-- Cantidad total de productos --}}
            <div class="row justify-content-end mb-3">
                <div class="col-md-4">
                    <div class="border rounded bg-light text-center py-3">
                        <h6 class="mb-1">Total de productos en el carrito</h6>
                        <span class="h5 text-primary">{{ $cart->count() }}</span>
                    </div>
                </div>
            </div>

        {{-- Botones de acción simétricos --}}
        <div class="row text-center mt-4">
            {{-- Cancelar - Izquierda --}}
            <div class="col-md-4 mb-2">
                <a href="{{ route('listTraspasos.index') }}" class="btn btn-danger w-100">
                    <i class="fa-solid fa-xmark me-2"></i> Cancelar
                </a>
            </div>

            {{-- Vaciar Carrito - Centro --}}
            <div class="col-md-4 mb-2">
                <form id="vaciar-carrito-form" action="{{ route('traspasos.VaciarCarrito') }}" method="POST">
                    @csrf
                    <button type="button" class="btn btn-warning text-white w-100" id="btn-vaciar-carrito">
                        <i class="fa-solid fa-trash me-2"></i> Vaciar Carrito
                    </button>
                </form>
            </div>

            {{-- Solicitar Traspaso - Derecha --}}
            <div class="col-md-4 mb-2">
                <form action="{{ route('traspasos.createInvoice') }}" method="POST">
                    @csrf
                    @if($cart->count() > 0)
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-paper-plane me-2"></i> Solicitar Traspaso
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


<style>
  /* Contenedor para permitir scroll vertical y horizontal */
  .table-responsive1 {
    max-height: 600px;               /* Altura máxima visible */
    overflow-x: auto;                /* Scroll horizontal si necesario */
    overflow-y: auto;                /* Scroll vertical si necesario */
    -webkit-overflow-scrolling: touch; /* Suaviza scroll en dispositivos móviles */
    border: 1px solid #e6dfde;       /* Opcional: bordes para mejor visual */
    border-radius: 0.25rem;          /* Bordes redondeados */
    padding: 0.1rem;                 /* Espaciado interno */
    background-color: #fff;          /* Fondo blanco para contraste */
  }

  /* Tabla interna con ancho mínimo para forzar scroll horizontal si es necesario */
  .table1 {
    min-width: 100px;               /* Ajusta esto según tu contenido real */
    width: 80%;
    border-collapse: collapse;       /* Unifica bordes */
  }

  .table1 th, .table1 td {
    padding: 2px 1px;               /* Espaciado interno en celdas */
    text-align: center;
    vertical-align: middle;
    border: 1px solid #dee2e6;
  }

  /* Opcional: mejora visibilidad de los encabezados */
  .table1 thead {
    background-color: #f8f9fa;
    font-weight: bold;
  }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 📦 Código de escaneo de barras
        const barcodeInput = document.getElementById('barcode');

        if (barcodeInput) {
            barcodeInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const barcode = barcodeInput.value.trim();

                    if (barcode.length > 0) {
                        fetch("{{ route('traspasos.add-by-barcode') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ barcode: barcode })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Producto agregado!',
                                    text: `Se agregó: ${data.product_name}`,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload(); // Puedes quitar esto si actualizas dinámicamente
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Producto no encontrado o sin stock en la sucursal',
                                    text: 'Verifica el código de barras e intenta nuevamente.',
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de servidor',
                                text: 'Hubo un problema al procesar la solicitud.',
                            });
                        });

                        barcodeInput.value = "";
                    }
                }
            });
        }

    // 🗑️ Vaciar carrito
    const vaciarBtn = document.getElementById('btn-vaciar-carrito');
    const vaciarForm = document.getElementById('vaciar-carrito-form');

    if (vaciarBtn && vaciarForm) {
        vaciarBtn.addEventListener('click', function () {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esto eliminará todos los productos del carrito!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    vaciarForm.submit();
                }
            });
        });
    }
});
document.addEventListener('DOMContentLoaded', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- SweetAlert2 -->

