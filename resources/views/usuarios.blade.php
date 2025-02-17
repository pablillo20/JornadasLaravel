<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <div class="container mt-5">
        <h2>Gestión de Usuarios</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo Electrónico</th>
                    <th>Rol</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tablaUsuarios">
                <!-- Usuarios cargados dinámicamente -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            // Función para cargar los usuarios desde la API
            function cargarUsuarios() {
                $.ajax({
                    url: '/api/usuarios',
                    method: 'GET',
                    success: function(response) {
                        console.log(response);

                        let usuarios = response.usuario;
                        let tablaUsuarios = $('#tablaUsuarios');
                        tablaUsuarios.empty();

                        if (usuarios.length > 0) {
                            usuarios.forEach(usuario => {
                                let boton = usuario.es_estudiante == 0 ?
                                    `<button class="btn btn-primary btn-sm hacerEstudiante" data-id="${usuario.id}">Hacer Estudiante</button>` :
                                    `<span class="badge bg-success">Estudiante</span>`;

                                tablaUsuarios.append(`
                                    <tr>
                                        <td>${usuario.id}</td>
                                        <td>${usuario.name}</td>
                                        <td>${usuario.email}</td>
                                        <td>${usuario.rol}</td> 
                                        <td>${boton}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            tablaUsuarios.append(`
                                <tr>
                                    <td colspan="5" class="text-center">No hay usuarios disponibles.</td>
                                </tr>
                            `);
                        }
                    },
                    error: function() {
                        alert('Error al cargar los usuarios.');
                    }
                });
            }

            $(document).on('click', '.hacerEstudiante', function() {
                let userId = $(this).data('id');

                $.ajax({
                    url: `api/usuario/${userId}/actualizar-estudiante`,
                    method: 'PATCH',
                    success: function(response) {
                        alert(response.mensaje); // Si la actualización es exitosa
                        cargarUsuarios(); // Recargar la lista de usuarios
                    },
                    error: function(xhr, status, error) {
                        console.error("Status:", status);
                        console.error("Error:", error);
                        console.error("Response:", xhr.responseText);
                        alert(
                            'Error al actualizar el usuario. Verifica la consola para más detalles.');
                    }
                });
            });

            // Cargar los usuarios al inicio
            cargarUsuarios();
        });
    </script>
</x-app-layout>
