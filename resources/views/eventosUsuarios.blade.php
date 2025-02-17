<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Eventos') }}
        </h2>
    </x-slot>

    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <div class="container mt-5">
        <h2>Gestión de Eventos</h2>

        <div id="error-message" class="alert alert-danger alert-dismissible fade show"
            style="display:none; position: absolute; top: 10px; z-index: 999;">
            <strong>Error: </strong><span id="error-text"></span>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cupo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaEventos">
                <!-- Eventos cargados dinámicamente -->
            </tbody>
        </table>
    </div>


    <div class="modal fade" id="inscripcionModal" tabindex="-1" aria-labelledby="inscripcionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inscripcionModalLabel">Inscribirse al Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="inscripcionForm">
                        <div class="mb-3">
                            <label for="tipo_inscripcion" class="form-label">Tipo de Inscripción</label>
                            <select class="form-select" id="tipo_inscripcion">
                                <option value="presencial">Presencial</option>
                                <option value="virtual">Virtual</option>
                            </select>
                        </div>
                        <div id="paypal-button-container"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script
        src="https://www.paypal.com/sdk/js?client-id=AYQHnK5GXxmgtWFkmMi55h6SYrpjIjg7yGD3MzCfBqUIZZ0KIRFy_tziYmGyZWB40iPmC0OlhAIwNIXT">
    </script>
    <script>
        const userId = {{ auth()->id() ?? 'null' }};

        $(document).ready(function() {
            function cargarEventos() {
                $.ajax({
                    url: '/api/eventos',
                    method: 'GET',
                    success: function(response) {
                        let eventos = response.evento;
                        let tablaEventos = $('#tablaEventos');
                        let esEstudiante = {{ auth()->user()->es_estudiante ? 'true' : 'false' }};
                        let inscripcionText = esEstudiante ? "Apuntarse Gratis" : "Inscribirse";
                        if (eventos.length > 0) {
                            eventos.forEach(evento => {
                                tablaEventos.append(`
                                    <tr>
                                        <td>${evento.id}</td>
                                        <td>${evento.titulo}</td>
                                        <td>${evento.tipo}</td>
                                        <td>${evento.fecha}</td>
                                        <td>${evento.hora}</td>
                                        <td>${evento.cupo}</td>
                                        <td>
                                            <button class="btn btn-success btn-sm" onclick="verificarInscripcion(${evento.id})">${inscripcionText}</button>
                                        </td>
                                    </tr>
                                `);
                            });
                        } else {
                            tablaEventos.append(
                                `<tr><td colspan="7" class="text-center">No hay eventos disponibles.</td></tr>`
                            );
                        }
                    },
                    error: function() {
                        mostrarError('Error al cargar los eventos.');
                    }
                });
            }

            window.verificarInscripcion = function(eventoId) {
                if (!userId) {
                    alert("Debes iniciar sesión para inscribirte.");
                    return;
                }

                $.ajax({
                    url: `/api/inscripcion/${userId}/${eventoId}`,
                    method: 'GET',
                    success: function(response) {
                        switch (response.status) {
                            case 215:
                                alert("El evento no existe.");
                                break;
                            case 210:
                                alert("El cupo está lleno, no puedes inscribirte.");
                                break;
                            case 220:
                                alert("No puedes inscribirte en más de 5 conferencias.");
                                break;
                            case 221:
                                alert("No puedes inscribirte en más de 4 talleres.");
                                break;
                            default:
                                if (response.inscrito) {
                                    alert("Ya estás inscrito en este evento.");
                                } else {
                                    inscribirse(eventoId);
                                }
                                break;
                        }
                    },
                    error: function(xhr) {
                        mostrarError(xhr.responseJSON?.message ||
                            "Error al verificar la inscripción.");
                    }
                });
            };


            window.inscribirse = function(eventoId) {
                let esEstudiante = {{ auth()->user()->es_estudiante ? 'true' : 'false' }};
                selectedEventoId = eventoId;
                if (!esEstudiante) {
                    $('#inscripcionModal').modal('show');
                }
                iniciarProcesoPago();
            };



            function iniciarProcesoPago() {
                if (!$('#paypal-button-container').length) {
                    $('body').append('<div id="paypal-button-container"></div>');
                }

                $('#paypal-button-container').empty();

                let esEstudiante = {{ auth()->user()->es_estudiante ? 'true' : 'false' }};
                let precioInscripcion = 9;
                let tipoInscripcion = document.getElementById("tipo_inscripcion").value;

                if (esEstudiante) {
                    precioInscripcion = 0;
                } else if (tipoInscripcion == "presencial") {
                    precioInscripcion = 9;
                } else if (tipoInscripcion == "virtual") {
                    precioInscripcion = 5;
                }

                console.log('Precio Inscripción:', precioInscripcion);


                if (esEstudiante) {
                    $.ajax({
                        url: '/api/inscripcion',
                        method: 'POST',
                        data: {
                            user_id: userId,
                            evento_id: selectedEventoId,
                            tipo_inscripcion: $('#tipo_inscripcion').val()
                        },
                        success: function(response) {
                            alert("Inscripción exitosa!");
                        },
                        error: function(response) {
                            let errorMessage = response.responseJSON?.message ||
                            "Error al inscribirse.";
                            mostrarError(errorMessage);
                        }
                    });
                } else {


                    console.log('Precio Inscripción:', precioInscripcion);
                    paypal.Buttons({
                        createOrder: function(data, actions) {
                            return actions.order.create({
                                purchase_units: [{
                                    amount: {
                                        value: precioInscripcion
                                    }
                                }]
                            });
                        },
                        onApprove: function(data, actions) {
                            return actions.order.capture().then(function(details) {
                                $.ajax({
                                    url: '/api/inscripcion',
                                    method: 'POST',
                                    data: {
                                        user_id: userId,
                                        evento_id: selectedEventoId,
                                        tipo_inscripcion: $('#tipo_inscripcion').val()
                                    },
                                    success: function(response) {
                                        alert("Inscripción exitosa!");
                                        $('#inscripcionModal').modal('hide');
                                    },
                                    error: function(response) {
                                        let errorMessage = response.responseJSON
                                            ?.message || "Error al inscribirse.";
                                        mostrarError(errorMessage);
                                    }
                                });
                            });
                        },
                        onCancel: function(data) {
                            alert("Pago Cancelado");
                        }
                    }).render('#paypal-button-container');

                    $('#paypal-button-container').show();
                }
            }

            function mostrarError(message) {
                $('#error-text').text(message);
                $('#error-message').show();
                setTimeout(function() {
                    $('#error-message').fadeOut();
                }, 3000);
            }

            cargarEventos();

        });
    </script>
</x-app-layout>
