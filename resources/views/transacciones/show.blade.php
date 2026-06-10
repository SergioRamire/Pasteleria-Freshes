@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

               <div class="card-header d-flex justify-content-between">
                    <div class="header-title d-flex align-items-center">
                        <h4 class="card-title mb-0">Datos de la transacción</h4>
                    </div>
                </div>


                <div class="card-body">
                        <div class=" row align-items-center">
                            <div class="form-group col-md-3">
                                <label for="user_id"><i class="ri-user-star-line me-1"></i> Empleado Responsable</label>
                                <input type="text" class="form-control @error('empleado') is-invalid @enderror"
                                             value="{{ $transaccion->nombre_usuario }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="user_id"><i class="ri-safe-2-line me-1"></i> Caja</label>
                                <input type="text" class="form-control "
                                             value="{{ $transaccion->numero_caja  }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="user_id"><i class="ri-calendar-2-line me-1"></i> Fecha</label>
                                <input type="text" class="form-control "
                                             value="{{ $transaccion->fecha  }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="user_id"><i class="ri-time-line me-1"></i> Hora</label>
                                <input type="text" class="form-control "
                                             value="{{ $transaccion->hora  }}" disabled>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="user_id"><i class="ri-exchange-line me-1"></i> Tipo de Transacción</label>
                                <input type="text" class="form-control "
                                             value="{{ucfirst( $transaccion->tipo_transaccion ) }}" disabled>
                            </div>

                             <div class="form-group col-md-4">
                                <label for="user_id"><i class="ri-exchange-dollar-line me-1"></i> Método de Cobro/Retiro</label>
                                <input type="text" class="form-control "
                                             value="{{ ucfirst($transaccion->metodo_pago)  }}" disabled>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="user_id"><i class="ri-money-dollar-box-line me-1"></i> Monto</label>
                                <input type="text" class="form-control "
                                             value="${{ number_format($transaccion->monto, 2) }}" disabled>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="descripcion"><i class="ri-edit-line me-1"></i> Descripción</label>
                                <textarea class="form-control" cols="25" rows="3" disabled>{{ $transaccion->descripcion }}</textarea>

                            </div>

                        </div>
                        <!-- end: Input Data -->
                        <div class="mt-2">

                            <a class="btn bg-danger" href="{{ route('transacciones.index') }}"> <i class="ri-close-line"></i> Regresar</a>
                        </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@include('components.preview-img-form')
@endsection
