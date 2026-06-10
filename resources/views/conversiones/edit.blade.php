@extends('dashboard.body.main')

@section('specificpagestyles')

    <style>
    .conversion-icon {
        font-size: 2rem;
        margin-bottom: 20px;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        animation: spinPause 3s ease-in-out infinite;
    }

    @keyframes spinPause {
        0%, 20% { transform: rotate(0deg); }
        80%, 100% { transform: rotate(360deg); }
    }
    </style>
@endsection

@section('container')
<div class="container-fluid">
    <!-- Alertas -->
    @if (session()->has('error'))
        <div id="alert-error" class="alert text-white bg-danger" role="alert">
            <div class="iq-alert-text">{{ session('error') }}</div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="ri-close-line"></i>
            </button>
        </div>
    @endif

    <!-- Código de Barras -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        Código de Barras
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label for="codigo_barras"><i class="ri-barcode-line me-1"></i> Código de barras</label>
                            <input type="text" class="form-control"
                                   value="{{ $invent->codigo_barras }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label><i class="ri-barcode-box-line me-1"></i> Código de barras generado</label>
                            <div>{!! $barcode !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        Información del Producto
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-thumbnail" style="max-width: 150px;"
                             src="{{ $invent->product_image ? asset('storage/products/'.$invent->product_image) : asset('assets/images/product/default.webp') }}"
                             alt="Imagen del producto">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="product_code"><i class="ri-qr-code-line me-1"></i> Código de producto</label>
                            <input type="text" class="form-control"
                                   value="{{ $invent->product_code }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="ri-price-tag-3-line me-1"></i> Nombre del Producto</label>
                            <input type="text" class="form-control"
                                   value="{{ $invent->producto }}" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="fa-solid fa-balance-scale me-1"></i> Unidad de Medida</label>
                            <input type="text" class="form-control"
                                   value="{{ $invent->unidad }}" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="ri-shopping-bag-line me-1"></i> Marca</label>
                            <input type="text" class="form-control"
                                   value="{{ $invent->marca_nombre }}" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="ri-archive-line me-1"></i> Categoría</label>
                            <input type="text" class="form-control"
                                   value="{{ $invent->category_name }}" disabled>
                        </div>

                        <div class="col-md-3">
                            <label for="stock_minimo"><i class="ri-arrow-down-line me-1"></i> Stock Mínimo</label>
                            <input type="number" class="form-control"
                                   value="{{ $invent->stock_minimo }}" disabled>
                        </div>

                        <!-- Existencias -->
                        <div class="form-group col-md-3">
                            <label for="stock"><i class="ri-stack-line me-1"></i> Existencias Actuales</label>
                            <input type="number" min="0"
                                class="form-control {{ old('stock', $invent->stock) <= $invent->stock_minimo ? 'text-danger' : 'text-success' }} @error('stock') is-invalid @enderror"
                                name="stock" id="stock"
                                value="{{ old('stock', $invent->stock) }}"
                                style="font-weight: bold;" disabled>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversión de Unidades -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <div class="conversion-icon">
                        <i class="ri-refresh-line"></i>
                    </div>
                    <h4 class="card-title mb-0">Conversión de Unidades a Granel</h4>
                    <p class="mb-0 text-muted">Convierte tus productos de una unidad a otra</p>
                </div>

                <div class="card-body">
                    <form id="formConversion" action="{{ route('conversiones.update', $invent->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h6 class="text-center mb-4">
                            <i class="ri-settings-3-line me-2"></i>Datos de Conversión
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="codigo_producto">
                                    Código del Producto Destino <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('codigo_producto') is-invalid @enderror"
                                    id="codigo_producto"
                                    name="codigo_producto"
                                    value="{{ old('codigo_producto') }}"
                                    placeholder="Código del producto..."
                                    required>
                                @error('codigo_producto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- DIV PARA MOSTRAR LA INFORMACIÓN DEL PRODUCTO -->
                                <div id="producto-info" style="display: none; margin-top: 10px;"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="unidades">
                                    Cantidad a Convertir <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       name="unidades"
                                       id="unidades"
                                       class="form-control @error('unidades') is-invalid @enderror"
                                       required
                                       value="{{ old('unidades') }}"
                                       min="1"
                                       max="{{ $invent->stock }}"
                                       placeholder="¿Cuántas unidades?">
                                @error('unidades')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    Máximo: {{ $invent->stock }} unidades disponibles
                                </small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="equivalencia">
                                    Factor de Conversión <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       name="equivalencia"
                                       id="equivalencia"
                                       class="form-control @error('equivalencia') is-invalid @enderror"
                                       required
                                       value="{{ old('equivalencia') }}"
                                       min="1"
                                       placeholder="Factor de equivalencia...">
                                @error('equivalencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    Ejemplo: 1 unidad = X nuevas unidades
                                </small>
                            </div>
                        </div>

                        <br>

                        <!-- Resultado del cálculo -->
                        <div class="calculation-result text-center">
                            <h6 class="mb-2">
                                <i class="ri-calculator-line me-2"></i>Resultado de la Conversión
                            </h6>
                            <strong>Total a generar: <span id="total-unidades">0 nuevas unidades</span></strong>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('conversiones.index') }}" class="btn btn-danger">
                                <i class="ri-close-line me-1"></i> Cancelar
                            </a>
                            <button type="button" class="btn btn-primary" id="btnConfirm">
                                <i class="ri-refresh-line me-1"></i> Procesar Conversión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="ri-alert-line me-1"></i> Confirmar Conversión
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="ri-question-line text-warning" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p class="fs-5 mb-2">¿Estás seguro que deseas realizar esta conversión?</p>
                <p class="text-muted mb-0">Esta acción modificará el inventario y no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">
                    <i class="ri-check-line me-1"></i> Sí, Convertir
                </button>
            </div>
        </div>
    </div>
</div>

@include('components.preview-img-form')
@endsection

@section('specificpagescripts')
// Reemplazar la sección @section('specificpagescripts') completa
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnConfirm = document.getElementById('btnConfirm');
        const confirmSubmit = document.getElementById('confirmSubmit');
        const form = document.getElementById('formConversion');
        const unidadesInput = document.getElementById('unidades');
        const equivalenciaInput = document.getElementById('equivalencia');
        const totalUnidadesSpan = document.getElementById('total-unidades');
        const codigoProductoInput = document.getElementById('codigo_producto');

        // Variable global para almacenar el producto encontrado
        let productoEncontrado = null;

        // Función para calcular el total
        function calcularTotal() {
            const unidades = parseInt(unidadesInput.value) || 0;
            const equivalencia = parseInt(equivalenciaInput.value) || 0;
            const total = unidades * equivalencia;

            // Actualizar el span del total
            if (productoEncontrado) {
                totalUnidadesSpan.innerHTML = `${total.toLocaleString()} nuevas unidades de <strong>${productoEncontrado.product_name}</strong> (${productoEncontrado.unidad})`;
            } else {
                totalUnidadesSpan.textContent = total.toLocaleString() + ' nuevas unidades';
            }
        }

        // Función para buscar producto por código
        function buscarProducto(codigo) {
            if (codigo.length >= 3) { // Buscar después de 3 caracteres
                fetch(`/dashboard/productos/buscar-codigo/${codigo}`)
                    .then(response => response.json())
                    .then(data => {
                        const infoProducto = document.getElementById('producto-info');

                        if (data.success) {
                            // Guardar el producto encontrado globalmente
                            productoEncontrado = data.producto;

                            // Mostrar información del producto encontrado
                            infoProducto.innerHTML = `
                                <div class="alert alert-success" role="alert">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>Producto encontrado:<br> </strong> ${data.producto.product_name}<br>
                                            <small class="text-muted">Unidad: ${data.producto.unidad}</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                            infoProducto.style.display = 'block';

                            // Recalcular el total con el nuevo producto
                            calcularTotal();
                        } else {
                            // Limpiar el producto encontrado
                            productoEncontrado = null;

                            // Mostrar que no se encontró el producto
                            infoProducto.innerHTML = `
                                <div class="alert alert-warning" role="alert">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>Producto no encontrado</strong><br>
                                            <small>Verifica el código e intenta nuevamente</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                            infoProducto.style.display = 'block';

                            // Recalcular el total sin producto
                            calcularTotal();
                        }
                    })
                    .catch(error => {
                        console.error('Error al buscar producto:', error);
                        productoEncontrado = null;

                        const infoProducto = document.getElementById('producto-info');
                        infoProducto.innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                <i class="ri-error-warning-line me-2"></i>
                                Error al buscar el producto
                            </div>
                        `;
                        infoProducto.style.display = 'block';

                        // Recalcular el total sin producto
                        calcularTotal();
                    });
            } else {
                // Limpiar información si el código es muy corto
                productoEncontrado = null;
                const infoProducto = document.getElementById('producto-info');
                infoProducto.style.display = 'none';

                // Recalcular el total sin producto
                calcularTotal();
            }
        }

        // Event listeners para cálculo dinámico
        unidadesInput.addEventListener('input', calcularTotal);
        equivalenciaInput.addEventListener('input', calcularTotal);

        // Event listener para búsqueda de producto
        let timeoutId;
        codigoProductoInput.addEventListener('input', function() {
            // Limpiar timeout anterior
            clearTimeout(timeoutId);

            // Crear nuevo timeout para evitar muchas peticiones
            timeoutId = setTimeout(() => {
                const codigo = this.value.trim();
                if (codigo) {
                    buscarProducto(codigo);
                } else {
                    productoEncontrado = null;
                    document.getElementById('producto-info').style.display = 'none';
                    calcularTotal();
                }
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });

        // Event listener para el botón de confirmación
        btnConfirm.addEventListener('click', function () {
            // Validar campos antes de mostrar modal
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        });

        // Event listener para el botón de confirmación final
        confirmSubmit.addEventListener('click', function () {
            form.submit();
        });
    });
</script>
@endsection
