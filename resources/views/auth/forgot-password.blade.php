@extends('auth.body.main')

@section('container')
<div class="row justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-lg-5 col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3 text-center">¿Olvidaste tu contraseña?</h4>
                <p class="text-center mb-4">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2" role="alert" style="font-size: 0.9rem;">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                    <span aria-hidden="true" style="color: black; font-weight: bold; font-size: 1.2rem;">&times;</span>
                </button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2" role="alert" style="font-size: 0.9rem;">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                    <span aria-hidden="true" style="color: black; font-weight: bold; font-size: 1.2rem;">&times;</span>
                </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required
                            autofocus
                            placeholder="tuemail@ejemplo.com"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i> Enviar Enlace
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Regresar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
