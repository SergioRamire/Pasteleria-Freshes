@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">Permisos
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Asigna o restringe las acciones que cada usuario puede realizar dentro del sistema, garantizando un control adecuado de acceso y seguridad.">
                        </i>
                    </h3>
                </div>
                <div>
                    <a href="{{ route('permission.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Añadir Permiso</a>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>N°</th>
                            <th>Nombre del permiso</th>
                            <th>Nombre del grupo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ (($permissions->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->group_name }}</td>
                            <td>
                                {{-- Formulario oculto para eliminación --}}
                                <form id="delete-form-{{ $permission->id }}" action="{{ route('permission.destroy', $permission->id) }}" method="POST" style="display: none;">
                                    @method('delete')
                                    @csrf
                                </form>

                                <div class="d-flex align-items-center list-action">
                                    <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"
                                        href="{{ route('permission.edit', $permission->id) }}"><i class="ri-pencil-line mr-0"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning mr-2 border-none btn-delete"
                                            data-id="{{ $permission->id }}"
                                            data-name="{{ $permission->name }}"
                                            data-group="{{ $permission->group_name }}"
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
            {{ $permissions->links() }}
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
            const itemGroup = this.getAttribute('data-group');

            Swal.fire({
                title: '¿Estás seguro?',
                html: `¡Vas a eliminar el permiso <strong>"${itemName}"</strong> del grupo <strong>"${itemGroup}"</strong>!<br><br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar permiso',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Eliminando permiso...',
                        text: 'Por favor espera mientras se procesa la eliminación',
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

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            $(alert).alert('close');
        });
    }, 5000);
});
</script>

@endsection
