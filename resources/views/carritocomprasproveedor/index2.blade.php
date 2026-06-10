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
            <h4 class="mb-3">
                Nueva lista de productos a proveedor
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
                    <form action="{{ route('nuevascomprasproveedor.index') }}" method="GET">
                        <div class="row d-flex align-items-end justify-content-between mb-3">

                            {{-- Cantidad por página --}}
                            <div class="form-group col-md-3 mb-2">
                                <label for="row" class="form-label">
                                    <i class="ri-align-justify"></i> Filas por página
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

                            {{-- Escaneo --}}
                            <div class="form-group col-md-6 mb-2">
                                <label class="control-label align-self-center" for="barcode"><i class="ri-barcode-box-line me-1"></i> Escanear código de barras</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-barcode"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="barcode" class="form-control" name="barcode" placeholder="Escanea el código" autocomplete="off" autofocus>
                                </div>
                            </div>

                            {{-- Buscar --}}
                            <div class="form-group col-md-6 mb-2">
                                <label for="search"><i class="ri-search-line"></i> Buscar Producto</label>
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <a href="{{ route('nuevascomprasproveedor.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{-- TABLA PRODUCTOS --}}
                    <div class="table-responsive rounded mb-3 border-none">
                        <table class="table table-sm mb-0 small">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data text-center">
                                    <th>N°.</th>
                                    <th>Código</th>
                                    <th>Foto</th>
                                    <th>Nombre Producto</th>
                                    <th>Código de Barras</th>
                                    <th>Marca</th>
                                    <th>Proveedor</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr>
                                    <td class="text-center">{{ (($products->currentPage() * 10) - 10) + $loop->iteration }}</td>
                                    <td class="text-center">{{ $product->product_code }}</td>
                                    <td class="text-center"><img class="avatar-40 rounded" src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}"></td>
                                    <td>{{ $product->product_name }}</td>
                                    <td class="text-center">{{ $product->codigo_barras }}</td>
                                    <td class="text-center">{{ $product->marca_nombre }}</td>
                                    <td class="text-center">{{ $product->proveedor_nombre }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('nuevascomprasproveedor.addCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <button type="submit" class="btn btn-primary btn-sm" title="Agregar al carrito">
                                                <i class="far fa-plus mr-0"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
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
                    <h5 class="mb-0"><i class="fa-solid fa-cart-shopping me-2"></i> Lista de Productos</h5>
                </div>

                {{-- Tabla de productos en el carrito --}}
                <div class="table-responsive1 rounded shadow-sm border mb-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                                <tr class="ligth ligth-data text-center">
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Marca</th>
                                    <th>Proveedor</th>
                                    <th style="min-width: 140px;">Cantidad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productItem as $item)
                                    <tr class="text-center">
                                        <td>{{ $item->options->product_code }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->options->marca }}</td>
                                        <td>{{ $item->options->proveedor }}</td>
                                        <td>
                                            <form action="{{ route('nuevascomprasproveedor.updateCart', $item->rowId) }}" method="POST" class="d-flex justify-content-center align-items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item->id }}">
                                                <input type="number" name="qty" class="form-control form-control-sm text-center" style="max-width: 70px;" min="1" value="{{ old('qty', $item->qty) }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success ms-2" title="Actualizar cantidad">
                                                    <i class="fas fa-check mr-0"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('nuevascomprasproveedor.deleteCart', $item->rowId) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este producto del carrito?')" title="Eliminar">
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
                        $cart = Cart::instance('compraslitproveedor');
                    @endphp

                    {{-- Cantidad total de productos --}}
                    <div class="row justify-content-end mb-3">
                        <div class="col-md-4">
                            <div class="border rounded bg-light text-center py-3">
                                <h6 class="mb-1">Total de productos en la lista</h6>
                                <span class="h5 text-primary">{{ $cart->count() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Botones de acción simétricos --}}
                    <div class="row text-center mt-4">
                        {{-- Cancelar - Izquierda --}}
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('listasproductosproveedor.index') }}" class="btn btn-danger w-100">
                                <i class="fa-solid fa-xmark me-2"></i> Cancelar
                            </a>
                        </div>

                        {{-- Vaciar Carrito - Centro --}}
                        <div class="col-md-4 mb-2">
                            <form id="vaciar-carrito-form" action="{{ route('nuevascomprasproveedor.VaciarCarrito') }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-warning text-white w-100" id="btn-vaciar-carrito">
                                    <i class="fa-solid fa-trash me-2"></i> Vaciar Lista
                                </button>
                            </form>
                        </div>

                        {{-- Solicitar Compra - Derecha --}}
                        <div class="col-md-4 mb-2">
                            <form action="{{ route('nuevascomprasproveedor.createInvoice') }}" method="POST">
                                @csrf
                                @if($cart->count() > 0)
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Realizar Lista
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
document.addEventListener('DOMContentLoaded', () => {
    const barcodeInput = document.getElementById('barcode');
    const vaciarBtn = document.getElementById('btn-vaciar-carrito');
    const vaciarForm = document.getElementById('vaciar-carrito-form');

    /** ================================
     *  📦 Escaneo y agregado al carrito
     *  ================================ */
    if (barcodeInput) {
        barcodeInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                const barcode = barcodeInput.value.trim();

                if (barcode.length === 0) return;

                fetch("{{ route('nuevascomprasproveedor.add-by-barcode') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ barcode })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error de red');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Producto agregado!',
                            text: `Se agregó: ${data.message || data.product_name}`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload(); // recargar para reflejar el nuevo producto
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Producto no encontrado',
                            text: data.message || 'Verifica el código e intenta de nuevo.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'Hubo un problema al procesar la solicitud.'
                    });
                });

                barcodeInput.value = ""; // limpiar campo
            }
        });
    }

    /** ===============================
     *  🗑️ Vaciar carrito con confirmación
     *  =============================== */
    if (vaciarBtn && vaciarForm) {
        vaciarBtn.addEventListener('click', function () {
            Swal.fire({
                title: '¿Vaciar el carrito?',
                text: 'Esta acción eliminará todos los productos agregados.',
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

