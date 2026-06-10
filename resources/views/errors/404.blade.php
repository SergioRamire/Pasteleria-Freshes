<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ACUARIO | ERROR 404</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo-min.png') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="row no-gutters height-self-center">
                <div class="col-sm-12 text-center align-self-center">
                    <div class="iq-error position-relative">
                        <img src="../assets/images/error/Error404.png" alt="Error 404" style="max-width: 200px; width: 100%; height: auto;">
                        <p>¡Ups! Esta página no se encuentra.</p>
                        <a class="btn btn-primary d-inline-flex align-items-center mt-3" href="{{ route('dashboard') }}"><i class="ri-home-4-line"></i>Volver al inicio</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
