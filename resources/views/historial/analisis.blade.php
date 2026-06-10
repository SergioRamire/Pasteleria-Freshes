@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <h4 class="mb-3">Análisis de cambios de precio
                <i class="fas fa-info-circle text-primary"
                data-toggle="tooltip"
                data-placement="right"
                title="Analiza las variaciones de precios de los productos para tomar decisiones informadas sobre cambios de precio.">
                </i>
            </h4>
            <form action="{{ route('historiales.analisis') }}" method="GET" class="row g-3 align-items-end">
                @php
                    $hoy = now()->timezone('America/Mexico_City')->toDateString();
                @endphp

                <div class="form-group col-md-3">
                    <label for="fecha_inicio">
                        <i class="ri-calendar-line me-1"></i> Desde
                    </label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" max="{{ $hoy }}" class="form-control" value="{{ $fechaInicio }}">
                </div>

                <div class="form-group col-md-3">
                    <label for="fecha_fin">
                        <i class="ri-calendar-line me-1"></i> Hasta
                    </label>
                    <input type="date" id="fecha_fin" name="fecha_fin" max="{{ $hoy }}" class="form-control" value="{{ $fechaFin }}">
                </div>

                <div class="form-group col-md-4">
                    <label for="search">
                        <i class="ri-search-line me-1"></i> Buscar producto
                    </label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre o código">
                </div>

                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-chart-line"></i> Analizar
                    </button>
                    <a href="{{ route('historiales.index') }}" class="btn btn-secondary w-100 mt-2">
                        <i class="ri-arrow-go-back-line me-1"></i> Volver
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-12" style="overflow-x: auto;">
            <div style="width: max-content; min-width: 90%;">
                <canvas id="graficaPrecios" height="120" style="max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const datos = @json($historial->groupBy(fn($item) => $item->codigo));

    const labels = Object.keys(datos);
    const cantidades = labels.map(codigo => datos[codigo].length);

    const nombrePorCodigo = {};
    labels.forEach(codigo => {
        nombrePorCodigo[codigo] = datos[codigo][0].producto ?? codigo;
    });

    const ctx = document.getElementById('graficaPrecios').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad de cambios de precio',
                data: cantidades,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Cantidad de cambios de precio por producto'
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: context => {
                            const codigo = context[0].label;
                            return nombrePorCodigo[codigo] || codigo;
                        },
                        label: context => 'Cambios: ' + context.parsed.y
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Código de producto'
                    },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                        padding: 10
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: value => Number.isInteger(value) ? value : null
                    },
                    title: {
                        display: true,
                        text: 'Número de cambios'
                    }
                }
            }
        }
    });
</script>
@endsection
