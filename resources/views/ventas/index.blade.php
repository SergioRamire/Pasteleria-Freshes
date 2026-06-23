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
            <h4 class="mb-3">Punto de venta
                <i class="fas fa-info-circle text-primary"
                data-toggle="tooltip"
                data-placement="right"
                title="Realiza ventas rápidas y registra productos, aplica descuentos, selecciona métodos de pago y genera tickets de compra de forma eficiente y segura.">
                </i>
            </h4>
        </div>

        {{-- Tabla de productos - IZQUIERDA --}}
        <div class="col-lg-6 col-md-12">
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
                                        <option value="10" @if(request('row') == '10')selected @endif>10</option>
                                        <option value="25" @if(request('row') == '25')selected @endif>25</option>
                                        <option value="50" @if(request('row') == '50')selected @endif>50</option>
                                        <option value="100" @if(request('row') == '100')selected @endif>100</option>
                                    </select>
                                </div>
                            </div>

                            {{--
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">
                                    <i class="ri-archive-line"></i> Categoría
                                </label>
                                <select name="category_id" id="category_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="marca_id" class="form-label">
                                    <i class="ri-price-tag-3-line"></i> Marcas
                                </label>
                                <select name="marca_id" id="marca_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}" @if(request('marca_id') == $marca->id) selected @endif>{{ $marca->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            --}}

                            <div class="form-group col-md-9">
                                <label for="search"><i class="ri-search-line"></i> Buscar</label>
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <a href="{{ route('ventas.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
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
                        </div>
                    </form>

        {{-- Tabla de productos --}}
                    <div class="table-responsive rounded mb-3 border-none">
                        <table class="table table-sm mb-0 small">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth-data text-center">
                                    <th>Código</th>
                                    {{--
                                    <th>@sortablelink('codigo', 'Codigo Producto')</th>
                                    --}}
                                    <th>Foto</th>
                                    <th>@sortablelink('product_name', 'nombre')</th>
                                    <!-- <th>Unidad</th> -->
                                    <th>Precio</th>
                                    {{--
                                    <th>@sortablelink('selling_price', 'precio venta')</th>
                                    --}}
                                    <th>Stock</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr>
                                    {{-- <td>{{ (($products->currentPage() * 10) - 10) + $loop->iteration }}</td> --}}
                                    {{-- <td>{{ $product->codigo_barras }}</td> --}}
                                    <td>{{ $product->product_code }}</td>
                                    <td><img class="avatar-40 rounded" src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}"></td>
                                    <td>{{ $product->product_name }}</td>
                                    <!-- <td>
                                        {{ $product->equivalencia ? strtoupper($product->equivalencia) : 'Sin unidad' }}
                                    </td> -->
                                    <td>${{ number_format($product->selling_price, 2) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <form action="{{ route('ventas.addCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="inventario_id" value="{{ $product->inventario_id }}">
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <input type="hidden" name="name" value="{{ $product->product_name }}">
                                            <input type="hidden" name="price" value="{{ $product->selling_price }}">
                                            <input type="hidden" name="dealer_price" value="{{ $product->dealer_price }}">
                                            <input type="hidden" name="equivalencia" value="{{ $product->equivalencia }}"> <!-- AGREGAR ESTA LÍNEA -->
                                            <button type="submit" class="btn btn-primary btn-sm" title="Agregar al carrito"><i class="far fa-plus mr-0"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Producto no encontrado o sin stock.</td>
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
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        Carrito de Compras <i class="fa-solid fa-cart-shopping text-primary me-2"></i>
                    </h5>

                    <div class="table-responsive1 rounded shadow-sm border mb-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Nombre</th>
                                    <th style="min-width:120px;">Cantidad</th>
                                    <!-- <th>Unidad</th> -->
                                    <th>Precio U.</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productItem as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        {{-- Formulario para actualizar cantidad --}}
                                        <form action="{{ route('ventas.updateCart', $item->rowId) }}" method="POST" class="d-flex justify-content-center align-items-center">
                                            @csrf
                                            <input type="hidden" name="inventario_id" value="{{ $item->options->inventario_id }}">
                                            {{-- <input type="number" class="form-control form-control-sm text-center" name="qty" min="1" required value="{{ old('qty', $item->qty) }}" style="max-width: 70px;"> --}}
                                            <input type="number" class="form-control form-control-sm text-center" name="qty" min="1" step="1" required value="{{ old('qty', $item->qty) }}" style="max-width: 70px;" oninput="this.value = this.value.replace(/\D+/g, '')"/>
                                            <button type="submit" class="btn btn-success btn-sm ms-2" title="Actualizar cantidad">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <!-- <td>
                                        {{ $item->options->equivalencia ?? 'N/A' }}
                                    </td> -->
                                    <td class="text-center">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">${{ number_format($item->subtotal, 2) }}</td>
                                    <td class="text-center">
                                        {{-- <a href="{{ route('ventas.deleteCart', $item->rowId) }}" class="btn btn-danger btn-sm" title="Borrar producto" onclick="return confirm('¿Eliminar este producto del carrito?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a> --}}
                                        <a href="{{ route('ventas.deleteCart', $item->rowId) }}" class="btn btn-danger btn-sm" title="Borrar producto" onclick="return confirm('¿Eliminar este producto del carrito?')">
                                            <i class="fa-solid fa-trash mr-0"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Carrito Vacio.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row text-center mt-4">
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-primary text-white rounded py-2">

                                @php
                                    $cart = Cart::instance('venta');
                                    $total = $cart->subtotal();
                                    $subtotal = $total / 1.16; // Asumiendo que el total ya incluye IVA
                                    $iva = $subtotal * 0.16;
                                @endphp
                                <strong>Cantidad</strong><br>
                                 {{ $cart->count() }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-secondary text-white rounded py-2">
                                <strong>Subtotal</strong><br>
                                ${{ number_format($subtotal, 2) }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-info text-white rounded py-2">
                                <strong>IVA 16%</strong><br>

                                ${{ number_format($iva, 2) }}
                                {{-- ${{ number_format(Cart::tax(), 2) }} --}}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-success text-white rounded py-2">
                                <strong>Total</strong><br>
                                ${{ number_format($cart->subtotal(), 2) }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('ventas.createInvoice') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-bold">
                                <i class="fa-solid fa-user me-1"></i> Seleccionar Cliente
                            </label>
                            <a href="{{ route('customers.create') }}" class="btn btn-outline-primary" title="Agregar nuevo cliente">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                            <div class="input-group">
                                @php
                                    // Buscar el cliente "Cliente General" dentro de la colección
                                    $clienteGeneral = $customers->firstWhere('name', 'Cliente General');
                                    // Determinar el ID seleccionado: prioriza old() (después de error) o el ID del cliente general
                                    $selectedId = old('customer_id', $clienteGeneral ? $clienteGeneral->id : null);
                                @endphp

                                <select class="form-select" id="customer_id" name="customer_id">
                                    <option value="" disabled>-- Selecciona un cliente --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $selectedId == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('customer_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($cart->count() > 0)
                            <div class="d-flex justify-content-center gap-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-shopping-cart-line"></i> Crear Venta
                                </button>
                            </div>
                        @endif
                    </form>

                    <div class="d-flex justify-content-end mt-4">
                        <form id="vaciar-carrito-form" action="{{ route('ventas.VaciarCarrito') }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-danger" id="btn-vaciar-carrito">
                                <i class="fa-solid fa-trash me-1"></i> Vaciar Carrito
                            </button>
                        </form>
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
                        fetch("{{ route('ventas.add-by-barcode') }}", {
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
                                    title: 'Producto no encontrado o sin stock',
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

