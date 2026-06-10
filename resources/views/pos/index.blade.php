@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif
            <h4 class="mb-3">Punto de venta</h4>
        </div>

        {{-- Tabla de productos - IZQUIERDA --}}
        <div class="col-lg-6 col-md-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <form action="#" method="get">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                           <div class="col-md-3 mb-3">
                                <label for="row" class="form-label">Fila:</label>
                                <div>
                                    <select class="form-control" name="row">
                                        <option value="10" @if(request('row') == '10')selected @endif>10</option>
                                        <option value="25" @if(request('row') == '25')selected @endif>25</option>
                                        <option value="50" @if(request('row') == '50')selected @endif>50</option>
                                        <option value="100" @if(request('row') == '100')selected @endif>100</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="category_id" class="form-label">Categoría</label>
                                <select name="category_id" id="category_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="supplier_id" class="form-label">Proveedor</label>
                                <select name="supplier_id" id="supplier_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @if(request('supplier_id') == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group row rounded mb-3 border-none">
                            <label class="control-label col-sm-3 align-self-center" for="search">Buscar:</label>
                            <div class="input-group col-sm-8">
                                <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}" autofocus>
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-primary">
                                        <i class="fa-solid fa-magnifying-glass font-size-20"></i>
                                    </button>
                                    <a href="{{ route('pos.index') }}" class="input-group-text bg-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>



                        </div>
                        <div class="form-group row rounded mb-6 border-none">
                            <label class="control-label col-sm-6 align-self-center" for="barcode">Escanear código de barras:</label>
                            <div class="input-group col-sm-8">
                                <input type="text" id="barcode" class="form-control" name="barcode" placeholder="Escanea el código" autocomplete="off" autofocus>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive rounded mb-3 border-none">
                        <table class="table table-sm mb-0 small">
                            <thead class="bg-white text-uppercase">
                                <tr>
                                    <th>N°.</th>
                                    <th>Foto</th>
                                    <th>@sortablelink('product_name', 'nombre')</th>
                                    <th>@sortablelink('selling_price', 'precio')</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr>
                                    <td>{{ (($products->currentPage() * 10) - 10) + $loop->iteration }}</td>
                                    <td><img class="avatar-40 rounded" src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}"></td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->selling_price }}</td>
                                    <td>
                                        <form action="{{ route('pos.addCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <input type="hidden" name="name" value="{{ $product->product_name }}">
                                            <input type="hidden" name="price" value="{{ $product->selling_price }}">
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
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        Carrito de Compras <i class="fa-solid fa-cart-shopping text-primary me-2"></i>
                    </h5>

                    <div class="table-responsive1">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Nombre</th>
                                    <th style="min-width:120px;">Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productItem as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <form action="{{ route('pos.updateCart', $item->rowId) }}" method="POST" class="d-flex justify-content-center align-items-center">
                                            @csrf
                                            <input type="number" class="form-control form-control-sm text-center" name="qty" min="1" required value="{{ old('qty', $item->qty) }}" style="max-width: 70px;">
                                            <button type="submit" class="btn btn-success btn-sm ms-2" title="Actualizar cantidad">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">${{ number_format($item->subtotal, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('pos.deleteCart', $item->rowId) }}" class="btn btn-danger btn-sm" title="Borrar producto" onclick="return confirm('¿Eliminar este producto del carrito?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row text-center mt-4">
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-primary text-white rounded py-2">
                                <strong>Cantidad</strong><br>
                                {{ Cart::count() }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-secondary text-white rounded py-2">
                                <strong>Subtotal</strong><br>
                                ${{ number_format(Cart::subtotal(), 2) }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-info text-white rounded py-2">
                                <strong>IVA</strong><br>
                                @php
                                    $iva = floatval(str_replace(',', '', Cart::subtotal())) * 0.08; // Ejemplo 8% IVA
                                @endphp
                                ${{ number_format($iva, 2) }}
                                {{-- ${{ number_format(Cart::tax(), 2) }} --}}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-success text-white rounded py-2">
                                <strong>Total</strong><br>
                                ${{ number_format(Cart::subtotal(), 2) }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('pos.createInvoice') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-bold">
                                <i class="fa-solid fa-user me-1"></i> Seleccionar Cliente
                            </label>
                            <a href="{{ route('customers.create') }}" class="btn btn-outline-primary" title="Agregar nuevo cliente">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            <div class="input-group">
                                <select class="form-select" id="customer_id" name="customer_id">
                                    <option selected disabled>-- Selecciona un cliente --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                            @error('customer_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            {{-- <a href="{{ route('customers.create') }}" class="btn btn-outline-primary">Agregar cliente</a> --}}
                            <button type="submit" class="btn btn-success">Crear factura</button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-end mt-4">
                    <form id="vaciar-carrito-form" action="{{ route('pos.VaciarCarrito') }}" method="POST">
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
                    fetch("{{ route('pos.add-by-barcode') }}", {
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

