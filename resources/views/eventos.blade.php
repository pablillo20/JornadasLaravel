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

        <div id="error-message" class="alert alert-danger alert-dismissible fade show" style="display:none; position: absolute; top: 10px; z-index: 999;">
            <strong>Error: </strong><span id="error-text"></span>
        </div>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearEventoModal">Crear Evento</button>

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

    <div class="modal fade" id="crearEventoModal" tabindex="-1" aria-labelledby="crearEventoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearEventoModalLabel">Crear Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="crearEventoForm">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" required>
                                <option value="conferencia">Conferencia</option>
                                <option value="taller">Taller</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" required>
                        </div>
                        <div class="mb-3">
                            <label for="hora" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="hora" required>
                        </div>
                        <div class="mb-3">
                            <label for="cupo" class="form-label">Cupo</label>
                            <input type="number" class="form-control" id="cupo" required min="1" placeholder="Número de asistentes">
                        </div>
                        <div class="mb-3">
                            <label for="ponente_id" class="form-label">Ponente</label>
                            <select class="form-select" id="ponente_id" required>
                                <option value="" disabled selected>Seleccionando ponentes...</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear Evento</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="inscripcionModal" tabindex="-1" aria-labelledby="inscripcionModalLabel" aria-hidden="true">
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
                            <select class="form-select" id="tipo_inscripcion" required>
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

    <script src="https://www.paypal.com/sdk/js?client-id=AYQHnK5GXxmgtWFkmMi55h6SYrpjIjg7yGD3MzCfBqUIZZ0KIRFy_tziYmGyZWB40iPmC0OlhAIwNIXT"></script>
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
                        tablaEventos.empty();
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
                                            <button class="btn btn-danger btn-sm" onclick="borrarEvento(${evento.id})">Borrar</button>
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

            window.borrarEvento = function(id) {
                $.ajax({
                    url: '/api/eventos/' + id,
                    method: 'DELETE',
                    success: function(response) {
                        cargarEventos();
                    },
                    error: function(response) {
                        mostrarError(response.responseJSON.message);
                    }
                });
            }

            function cargarPonentes() {
                const selectPonente = $("#ponente_id");

                $.ajax({
                    url: "/api/ponentes",
                    method: "GET",
                    success: function(data) {
                        if (data.ponente && Array.isArray(data.ponente) && data.ponente.length > 0) {
                            selectPonente.html('<option value="" disabled selected>Selecciona un ponente</option>');
                            data.ponente.forEach(ponente => {
                                selectPonente.append(`<option value="${ponente.id}">${ponente.nombre}</option>`);
                            });
                        } else {
                            console.error("No se encontraron ponentes.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al obtener los ponentes:", error);
                    }
                });
            }

            $('#crearEventoForm').submit(function(e) {
                e.preventDefault();

                let titulo = $('#titulo').val();
                let tipo = $('#tipo').val();
                let fecha = $('#fecha').val();
                let hora = $('#hora').val();
                let cupo = $('#cupo').val();
                let ponente_id = $('#ponente_id').val();

                $.ajax({
                    url: '/api/eventos',
                    method: 'POST',
                    data: {
                        titulo: titulo,
                        tipo: tipo,
                        fecha: fecha,
                        hora: hora,
                        cupo: cupo,
                        ponente_id: ponente_id
                    },
                    success: function(response) {
                        cargarEventos();
                        $('#crearEventoModal').modal('hide');
                    },
                    error: function(response) {
                        let errorMessage = response.responseJSON?.message || "Error al crear el evento";
                        mostrarError(errorMessage);
                    }
                });
            });

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
                    mostrarError(xhr.responseJSON?.message || "Error al verificar la inscripción.");
                }
            });
        };
        
        
            window.inscribirse = function(eventoId) {
                let esEstudiante = {{ auth()->user()->es_estudiante ? 'true' : 'false' }}; 
                selectedEventoId = eventoId;
                if(!esEstudiante){
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
                    precioInscripcion = 0.01; 
                } else if (tipoInscripcion == "presencial") {
                    precioInscripcion = 9; 
                } else if (tipoInscripcion == "virtual") {
                    precioInscripcion = 5; 
                }

                console.log('Precio Inscripción:', precioInscripcion);


                if(esEstudiante){
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
                            let errorMessage = response.responseJSON?.message || "Error al inscribirse."; 
                            mostrarError(errorMessage);
                        }
                    });
                }else{

                
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
                                    let errorMessage = response.responseJSON?.message || "Error al inscribirse.";
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
            cargarPonentes();
        });
    </script>
</x-app-layout>
