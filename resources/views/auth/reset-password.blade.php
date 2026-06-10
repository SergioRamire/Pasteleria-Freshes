@extends('auth.body.main')

@section('container')
<div class="row justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-lg-5 col-md-7">
        <div class="card shadow-sm rounded">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center fw-bold">Restablecer Contraseña</h3>

                <form method="POST" action="{{ route('password.store') }}" novalidate>
                    @csrf

                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold"><i class="ri-mail-line me-2"></i> Correo electrónico</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', request()->input('email')) }}"
                            readonly
                            required
                            autofocus
                            placeholder="tu@correo.com"
                        />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold"><i class="ri-lock-line me-2"></i> Nueva Contraseña</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required
                            placeholder="Nueva contraseña"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold"><i class="ri-lock-password-line me-2"></i> Confirmar Nueva Contraseña</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="form-control"
                            required
                            placeholder="Confirmar contraseña"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="ri-lock-password-line me-2"></i> Restablecer Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
