@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div  id="alert-success" class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div id="alert-error" class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">Permisos del Rol
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Define los diferentes niveles de acceso y responsabilidades dentro del sistema, asignando permisos específicos a cada grupo de usuarios para una gestión eficiente y segura.">
                        </i>
                    </h3>
                </div>
                <div>
                    <a href="{{ route('rolePermission.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Agregar Permiso del Rol</a>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>N°</th>
                            <th>Nombre de rol</th>
                            <th>Nombre del permiso</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @foreach ($roles as $role)
                        <tr>
                            <td>{{ (($roles->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                @foreach ($role->permissions as $permission)
                                    <span class="badge rounded-pill bg-danger">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                {{-- Formulario oculto para eliminación --}}
                                <form id="delete-form-{{ $role->id }}" action="{{ route('rolePermission.destroy', $role->id) }}" method="POST" style="display: none;">
                                    @method('delete')
                                    @csrf
                                </form>

                                <div class="d-flex align-items-center list-action">
                                    <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"
                                        href="{{ route('rolePermission.edit', $role->id) }}"><i class="ri-pencil-line mr-0"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning mr-2 border-none btn-delete"
                                            data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title=""
                                            data-original-title="Eliminar">
                                        <i class="ri-delete-bin-line mr-0"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $roles->links() }}
        </div>
    </div>
    <!-- Page end  -->
</div>

{{-- Script para confirmación de eliminación con SweetAlert2 --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¡Vas a eliminar el rol "${itemName}" y no se puede deshacer!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Enviar formulario
                    document.getElementById(`delete-form-${itemId}`).submit();
                }
            });
        });
    });
});
</script>

@endsection
