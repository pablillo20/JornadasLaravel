<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            .nav-link {
                font-size: 1.2rem;
            }
        </style>
    @endif
</head>

<body>
    <header class="container py-3">
        <nav class="d-flex align-items-center justify-content-between">
            <img src="https://s3.eu-west-3.amazonaws.com/buscocolegio-static-content/assets/28074918/28074918.png"
                alt="Logo" class="img-fluid" style="max-height: 50px;">
            <div class="ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('EventosUser') }}" class="btn btn-outline-dark mx-2">Ver Eventos</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-dark mx-2">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-dark mx-2">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <!-- Carrusel -->
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://concepto.de/wp-content/uploads/2018/09/redes-informaticas-e1537289477478.jpg"
                    class="d-block w-100" style="height: 75vh; object-fit: cover;" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="https://blog.ssd.com.py/wp-content/uploads/2022/09/tipos-de-redes-informaticas-ssd-1.jpg"
                    class="d-block w-100" style="height: 75vh; object-fit: cover;" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="https://concepto.de/wp-content/uploads/2018/09/Red-e1537290443698.jpg" class="d-block w-100"
                    style="height: 75vh; object-fit: cover;" alt="Slide 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Sección de Ponentes -->
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Lista de Ponentes</h3>
        </div>
        <div id="ponentes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        <p id="no-ponentes" class="text-gray-500 hidden">No hay ponentes</p>
    </div>


    <!-- Sección de Eventos -->
    <div id="eventos" class="container py-5">
        <h2 class="text-center mb-4">Eventos</h2>
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
        <p id="mensajeNoEventos" class="hidden text-center text-red-500">No hay eventos disponibles.</p>
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

    <script
        src="https://www.paypal.com/sdk/js?client-id=AYQHnK5GXxmgtWFkmMi55h6SYrpjIjg7yGD3MzCfBqUIZZ0KIRFy_tziYmGyZWB40iPmC0OlhAIwNIXT">
    </script>



    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Jornadas en Ayala. Pablo Rubio Nogales.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const userId = {{ auth()->id() ?? 'null' }};

        // Función para cargar los ponentes
        function cargarPonentes() {
            fetch("http://localhost:8000/api/ponentes")
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById("ponentes-container");
                    const mensajeNoPonentes = document.getElementById("no-ponentes");

                    container.innerHTML = "";

                    if (!data.ponente || data.ponente.length === 0) {
                        mensajeNoPonentes.classList.remove("hidden");
                    } else {
                        mensajeNoPonentes.classList.add("hidden");
                        data.ponente.forEach(ponente => {
                            let card = document.createElement("div");
                            card.className =
                                "bg-white shadow-md rounded-lg p-4 flex flex-col items-center border border-gray-200";
                            card.innerHTML = `
                            <img src="${ponente.foto}" alt="Foto de ${ponente.nombre}" class="w-24 h-24 rounded-full mb-3 object-cover border-2 border-gray-300">
                            <h4 class="text-lg font-semibold">${ponente.nombre}</h4>
                            <p class="text-gray-600 text-sm">Experiencia: ${ponente.experiencia}</p>
                            <a href="https://www.${ponente.redes_sociales}.com/" target="_blank" class="text-blue-500 text-sm mt-2 underline">Sígueme en ${ponente.redes_sociales}</a>
                        `;
                            container.appendChild(card);
                        });
                    }
                })
                .catch(error => {
                    console.error("Error al obtener ponentes:", error);
                    const mensajeNoPonentes = document.getElementById("no-ponentes");
                    mensajeNoPonentes.textContent = "Error al cargar los ponentes.";
                    mensajeNoPonentes.classList.remove("hidden");
                });
        }

        // Llamar la función para cargar los ponentes cuando cargue la página
        document.addEventListener("DOMContentLoaded", cargarPonentes);

        // Función para cargar los eventos
        function cargarEventos() {
            $.ajax({
                url: '/api/eventos',
                method: 'GET',
                success: function(response) {
                    let eventos = response.evento;
                    let tablaEventos = $('#tablaEventos');
                    let esEstudiante =
                        {{ auth()->check() ? (auth()->user()->es_estudiante ? 'true' : 'false') : 'false' }};
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
                    mostrarError(xhr.responseJSON?.message || "Error al verificar la inscripción.");
                }
            });
        };

        window.inscribirse = function(eventoId) {
            let esEstudiante = {{ auth()->check() ? (auth()->user()->es_estudiante ? 'true' : 'false') : 'false' }};
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

            let esEstudiante = {{ auth()->check() ? (auth()->user()->es_estudiante ? 'true' : 'false') : 'false' }};
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
                        let errorMessage = response.responseJSON?.message || "Error al inscribirse.";
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
                                    let errorMessage = response.responseJSON?.message ||
                                        "Error al inscribirse.";
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

        cargarPonentes();
        cargarEventos();
    </script>
</body>

</html>
