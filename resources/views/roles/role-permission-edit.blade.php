@extends('dashboard.body.main')

@section('specificpagestyles')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
@endsection

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar rol en permiso</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('rolePermission.update', $role->id) }}" method="POST">
                    @csrf
                    @method('put')
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center mb-2">
                            <div class="form-group col-md-6">
                                <label for="role_id"><i class="ri-user-line me-1"></i> Nombre de rol <span class="text-danger">*</span></label>
                                <h4>{{ $role->name }}</h4>
                                {{-- <input type="text" class="form-control" value="{{ $role->name }}" readonly> --}}
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="name">Nombre del permiso <span class="text-danger">*</span></label>
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col-md-3">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="check-all">
                                    <label class="custom-control-label" for="check-all">Selecionar todo</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        @foreach ($permission_groups as $permission_group)
                        @php
                            $permissions = App\Models\User::getPermissionByGroupName($permission_group->group_name);
                        @endphp

                        <div class="row">
                            <div class="form-group col-md-3">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="permission_group_id[{{ $loop->iteration }}]"
                                        name="permission_group_id[]"
                                        {{ App\Models\User::roleHasPermission($role, $permissions) ? 'checked' : '' }}
                                    >
                                    <label
                                        for="permission_group_id[{{ $loop->iteration }}]"
                                        class="custom-control-label"
                                    >
                                        {{ $permission_group->group_name }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                @foreach ($permissions as $permission)
                                    <div class="custom-control custom-checkbox custom-control-inline my-2">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="permission_id[{{ $permission->id }}]"
                                            name="permission_id[]"
                                            value="{{ $permission->id }}"
                                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                        >
                                        <label
                                            for="permission_id[{{ $permission->id }}]"
                                            class="custom-control-label"
                                        >
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        @endforeach

                        <!-- end: Input Data -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <a class="btn bg-danger text-white" href="{{ route('rolePermission.index') }}">
                                <i class="ri-close-line me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-refresh-line me-1"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

<script>
    $('#check-all').click(function() {
        if($(this).is(':checked')) {
            $('input[type=checkbox]').prop('checked', true);
        } else {
            $('input[type=checkbox]').prop('checked', false);
        }
    });

    // Validación antes de enviar el formulario
    $('form').on('submit', function(e) {
        const permisosSeleccionados = $('input[name="permission_id[]"]:checked').length;

        if (permisosSeleccionados === 0) {
            e.preventDefault(); // Evita el envío
            alert('Debes seleccionar al menos un permiso.');
        }
    });
</script>



@endsection
