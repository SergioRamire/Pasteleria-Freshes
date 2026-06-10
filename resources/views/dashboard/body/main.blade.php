<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {{-- Título dinámico --}}
    <title>{{ $configNegocio?->nombre_negocio ?? 'ACUARIO' }}</title>

    {{-- Favicon dinámico --}}
    <link rel="shortcut icon" href="{{ $configNegocio?->favicon
            ? asset('storage/' . $configNegocio->favicon)
            : asset('assets/images/logo/logo-min.png') }}"/>

    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/backend.css?v=1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @yield('specificpagestyles')
</head>
<body>
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    <!-- loader END -->

    <!-- Wrapper Start -->
    <div class="wrapper">
        @include('dashboard.body.sidebar')

        @include('dashboard.body.navbar')

        <div class="content-page">
            @yield('container')
        </div>
    </div>
    <!-- Wrapper End-->

    @include('dashboard.body.footer')

    <!-- Backend Bundle JavaScript -->
    <script src="{{ asset('assets/js/backend-bundle.min.js') }}"></script>
    <script src="https://kit.fontawesome.com/4c897dc313.js" crossorigin="anonymous"></script>

    @yield('specificpagescripts')

    <!-- App JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>


    {{-- Esto es para hacer un buscador en el select  --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#customer_id').select2({
                placeholder: "Selecciona un cliente",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script>
        document.addEventListener("DOMContentLoaded", function () {
            const alerts = ['alert-success', 'alert-error'];

            alerts.forEach(function(id) {
                const alertBox = document.getElementById(id);
                if (alertBox) {
                    setTimeout(() => {
                        alertBox.classList.add('fade');
                        alertBox.style.opacity = '0';
                        setTimeout(() => alertBox.remove(), 500); // Desaparece completamente
                    }, 4000); // ⏱️ 4 segundos visible
                }
            });
        });
    </script>


</body>
</html>
