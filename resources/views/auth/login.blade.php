@extends('auth.body.main')

@section('container')
<div class="row justify-content-center align-items-center" style="min-height: 100vh;">
    <!-- Columna formulario -->
    <div class="col-lg-7 d-flex flex-column justify-content-center">
        <div class="card auth-card shadow-sm">
            <div class="card-body p-4">
                {{-- Mensaje de error general --}}
                @error('estado')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $message }}
                       <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true" style="color: black; font-weight: bold; font-size: 1.2rem;">&times;</span>
                        </button>
                    </div>
                @enderror
                <!-- Columna imagen -->
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/images/login/01.png') }}" style="max-width: 150px; height: auto;" alt="Login Image">
                </div>

                {{-- <h2 class="mb-3 text-center">Acceso</h2> --}}
                <p class="text-center mb-4">Inicie sesión para mantenerse conectado.</p>

                <form action="{{ route('login') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="input_type" class="form-label">Nombre de usuario</label>
                        <input
                            id="input_type"
                            name="input_type"
                            type="text"
                            class="form-control @error('email') is-invalid @enderror @error('username') is-invalid @enderror"
                            value="{{ old('input_type') }}"
                            required
                            autocomplete="off"
                            autofocus
                            placeholder="Introduce tu usuario o email"
                        >
                        @error('username')
                            <div class="invalid-feedback">Nombre de usuario o contraseña incorrecta.</div>
                        @enderror
                        @error('email')
                            <div class="invalid-feedback">Nombre de usuario o contraseña incorrecta.</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control @error('email') is-invalid @enderror @error('username') is-invalid @enderror"
                            required
                            placeholder="Introduce tu contraseña"
                        >
                    </div>

                    <div class="mb-3 text-end">
                        <a href="{{ route('password.request') }}" class="text-primary">¿Has olvidado tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i> Acceso
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
