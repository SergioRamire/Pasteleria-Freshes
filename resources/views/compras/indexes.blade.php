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
            <h4 class="mb-3">Registrar compra de inventario
                <i class="fas fa-info-circle text-primary"
                data-toggle="tooltip"
                data-placement="right"
                title="Selecciona los productos que deseas agregar al stock. Puedes filtrarlos por Categoría, Marca o buscar por codigo de barras.">
                </i>
            </h4>
        </div>

        {{-- Tabla de productos - IZQUIERDA --}}
        <div class="col-lg-12 col-md-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <form action="#" method="get">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">

                           <div class="col-md-3 mb-3">
                                <label for="row" class="form-label">
                                    <i class="ri-align-justify me-1"></i> Fila
                                </label>
                                <div>
                                    <select class="form-control" name="row">
                                        <option value="30" @if(request('row') == '30')selected @endif>30</option>
                                        <option value="35" @if(request('row') == '35')selected @endif>35</option>
                                        <option value="50" @if(request('row') == '50')selected @endif>50</option>
                                        <option value="100" @if(request('row') == '100')selected @endif>100</option>
                                    </select>
                                </div>
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

                            {{-- Marcas --}}
                            <div class="form-group col-md-3 mb-2">
                                <label for="marca_id" class="form-label"><i class="ri-shopping-bag-line me-1"></i> Marcas</label>
                                <select name="marca_id" id="marca_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" @if(request('marca_id') == $marca->id) selected @endif>
                                            {{ $marca->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Proveedor --}}
                            <div class="form-group col-md-3 mb-2">
                                <label for="proveedor_id" class="form-label"><i class="ri-store-2-line me-1"></i> Proveedor</label>
                                <select name="proveedor_id" id="proveedor_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" @if(request('proveedor_id') == $proveedor->id) selected @endif>
                                            {{ $proveedor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label align-self-center" for="barcode">
                                    <i class="ri-barcode-line me-1"></i> Escanear código de barras
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-barcode"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="barcode" class="form-control" name="barcode" placeholder="Escanea el código" autocomplete="off" autofocus>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="search"><i class="ri-search-line"></i> Buscar</label>
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <a href="{{ route('nuevascompras.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

        {{-- Tabla de productos --}}
                    <div class="table-responsive rounded mb-3 border-none">
                        <table class="table table-sm mb-0 small">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-center">N°.</th>
                                    <th>Nombre Producto</th>
                                    <th class="text-center">Código Barras</th>
                                    <th class="text-center">Código Producto</th>
                                    <th class="text-center">Unidad</th>
                                    <th class="text-center">Marca</th>
                                    <th class="text-center">Proveedor</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr>
                                    <td class="text-center">{{ (($products->currentPage() * 10) - 10) + $loop->iteration }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td class="text-center">{{ $product->codigo_barras }}</td>
                                    <td class="text-center">{{ $product->product_code }}</td>
                                    <td class="text-center">
                                        {{ $product->equivalencia ?? 'Sin unidad' }}
                                    </td>
                                    <td class="text-center">{{ $product->marca_nombre }}</td>
                                    <td class="text-center">{{ $product->proveedor_nombre }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('nuevascompras.addCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <button type="submit" class="btn btn-primary btn-sm" title="Agregar al carrito">
                                                <i class="far fa-plus mr-0"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No se encontraron resultados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div  class="table-responsive rounded mb-3 border-none">
                         {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla del carrito - DERECHA --}}
        <div class="col-lg-12 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        Carrito de Compras <i class="fa-solid fa-cart-shopping text-primary me-2"></i>
                    </h5>

                    <div class="table-responsive1 rounded shadow-sm border mb-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Código</th>
                                    <th>Producto</th>
                                    <th class="text-center" style="min-width: 140px;">Cantidad</th>
                                    <th class="text-center">Unidad</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productItem as $item)
                                    <tr>
                                        <td class="text-center">{{ $item->options->codigo_product }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('nuevascompras.updateCart', $item->rowId) }}" method="POST" class="d-flex justify-content-center align-items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item->id }}">
                                                 <input type="hidden" name="equivalencia" value="{{ $item->equivalencia }}">
                                                <input type="number" name="qty" class="form-control form-control-sm text-center" style="max-width: 70px;" min="1" value="{{ old('qty', $item->qty) }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success ms-2" title="Actualizar cantidad">
                                                    <i class="fas fa-check mr-0"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            {{ $item->options->unidad ?? 'Sin unidad' }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('nuevascompras.deleteCart', $item->rowId) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este producto del carrito?')" title="Eliminar">
                                                <i class="fa-solid fa-trash mr-0"></i>
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
                        $cart = Cart::instance('compras');
                    @endphp
                    <br>
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
                            <a href="{{ route('compras.index') }}" class="btn btn-danger w-100">
                                <i class="fa-solid fa-xmark me-2"></i> Cancelar
                            </a>
                        </div>

                        {{-- Vaciar Carrito - Centro --}}
                        <div class="col-md-4 mb-2">
                            <form id="vaciar-carrito-form" action="{{ route('nuevascompras.VaciarCarrito') }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-warning text-white w-100" id="btn-vaciar-carrito">
                                    <i class="fa-solid fa-trash me-2"></i> Vaciar Carrito
                                </button>
                            </form>
                        </div>

                        {{-- Solicitar Compra - Derecha --}}
                        <div class="col-md-4 mb-2">
                            <form action="{{ route('nuevascompras.createInvoice') }}" method="POST">
                                @csrf
                                @if($cart->count() > 0)
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Registrar Compra
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>

                </div>
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
                        fetch("{{ route('nuevascompras.add-by-barcode') }}", {
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
                                    title: 'Producto no encontrado',
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
</script>

<!-- SweetAlert2 -->

