<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pagos') }}
        </h2>
    </x-slot>

    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <div class="container mt-5">
        <h2>Gestión de Pagos</h2>

        <div id="error-message" class="alert alert-danger alert-dismissible fade show" style="display:none; position: absolute; top: 10px; z-index: 999;">
            <strong>Error: </strong><span id="error-text"></span>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody id="tablaPagos">
                <!-- Pagos cargados dinámicamente -->
            </tbody>
        </table>

        <!-- Mostrar el total de los pagos -->
        <div class="mt-3">
            <h5>Total de Pagos: <span id="totalPagos">0</span></h5>
        </div>
    </div>

    <script>
        
        $(document).ready(function() {
            function cargarPagos() {
                $.ajax({
                    url: '/api/pago',
                    method: 'GET',
                    success: function(response) {
                        let pagos = response.pago;
                        let tablaPagos = $('#tablaPagos');
                        tablaPagos.empty();
                        let totalPagos = 0; 
                        if (pagos.length > 0) {
                            pagos.forEach(pago => {
                                tablaPagos.append(`
                                    <tr>
                                        <td>${pago.id}</td>
                                        <td>${pago.cantidad}</td>
                                        <td>confirmado</td>
                                        <td>${pago.user_id}</td>
                                    </tr>
                                `);
                                totalPagos += parseFloat(pago.cantidad); 
                            });
                        } else {
                            tablaPagos.append(`
                                <tr>
                                    <td colspan="5" class="text-center">No hay pagos disponibles.</td>
                                </tr>
                            `);
                        }
                        $('#totalPagos').text(totalPagos.toFixed(2)); 
                    },
                    error: function() {
                        mostrarError('Error al cargar los pagos.');
                    }
                });
            }


            function mostrarError(message) {
                $('#error-text').text(message);
                $('#error-message').show();
                setTimeout(function() {
                    $('#error-message').fadeOut();
                }, 3000);
            }

            cargarPagos();
        });
    </script>
</x-app-layout>
