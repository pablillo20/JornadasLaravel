<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ponentes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Lista de Ponentes</h3>
            <button id="btnCrearPonente" class="bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2 shadow-lg">
                <span class="text-2xl">+</span> Crear Ponente
            </button>
        </div>
        <div id="ponentes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        <p id="no-ponentes" class="text-gray-500 hidden">No hay ponentes</p>
    </div>

    <!-- Modal para crear/editar ponente -->
    <div id="modalCrearPonente" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 id="modalTitulo" class="text-lg font-bold mb-4">Crear Ponente</h3>
            <input id="nombre" type="text" placeholder="Nombre" class="w-full border p-2 mb-2 rounded">
            <input id="foto" type="text" placeholder="URL de la foto" class="w-full border p-2 mb-2 rounded">
            <input id="experiencia" type="text" placeholder="Experiencia" class="w-full border p-2 mb-2 rounded">
            <input id="redes_sociales" type="text" placeholder="Red Social (ej: twitter)" class="w-full border p-2 mb-2 rounded">
            <div class="flex justify-end gap-2">
                <button id="btnCerrarModal" class="bg-gray-600 px-4 py-2 rounded">Cancelar</button>
                <button id="btnGuardarPonente" class="bg-blue-600 px-4 py-2 rounded">Guardar</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("ponentes-container");
            const modalCrearPonente = document.getElementById("modalCrearPonente");
            const btnCrearPonente = document.getElementById("btnCrearPonente");
            const btnCerrarModal = document.getElementById("btnCerrarModal");
            const btnGuardarPonente = document.getElementById("btnGuardarPonente");
            const modalTitulo = document.getElementById("modalTitulo");

            let editando = false;
            let idPonenteEditando = null;

            function cargarPonentes() {
                fetch("http://localhost:8000/api/ponentes")
                    .then(response => response.json())
                    .then(data => {
                        container.innerHTML = "";
                        if (!data.ponente || data.ponente.length === 0) {
                            document.getElementById("no-ponentes").classList.remove("hidden");
                        } else {
                            document.getElementById("no-ponentes").classList.add("hidden");
                            data.ponente.forEach(ponente => {
                                let card = document.createElement("div");
                                card.className = "bg-white shadow-md rounded-lg p-4 flex flex-col items-center border border-gray-200";
                                card.innerHTML = `
                                    <img src="${ponente.foto}" alt="Foto de ${ponente.nombre}" class="w-24 h-24 rounded-full mb-3 object-cover border-2 border-gray-300">
                                    <h4 class="text-lg font-semibold">${ponente.nombre}</h4>
                                    <p class="text-gray-600 text-sm">Experiencia: ${ponente.experiencia}</p>
                                    <a href="https://www.${ponente.redes_sociales}.com/" target="_blank" class="text-blue-500 text-sm mt-2 underline">Sígueme en ${ponente.redes_sociales}</a>
                                    <div class="flex gap-2 mt-3">
                                        <button class="btn-editar px-4 py-2 rounded-lg hover:bg-yellow-600" 
                                            data-id="${ponente.id}" data-nombre="${ponente.nombre}" data-foto="${ponente.foto}" 
                                            data-experiencia="${ponente.experiencia}" data-redes="${ponente.redes_sociales}">✏ Editar</button>
                                        <button class="btn-eliminar bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700" data-id="${ponente.id}">🗑 Eliminar</button>
                                    </div>
                                `;
                                container.appendChild(card);
                            });

                            document.querySelectorAll(".btn-editar").forEach(button => {
                                button.addEventListener("click", function () {
                                    const id = this.getAttribute("data-id");
                                    document.getElementById("nombre").value = this.getAttribute("data-nombre");
                                    document.getElementById("foto").value = this.getAttribute("data-foto");
                                    document.getElementById("experiencia").value = this.getAttribute("data-experiencia");
                                    document.getElementById("redes_sociales").value = this.getAttribute("data-redes");

                                    editando = true;
                                    idPonenteEditando = id;
                                    modalTitulo.textContent = "Editar Ponente";
                                    modalCrearPonente.classList.remove("hidden");
                                });
                            });

                            document.querySelectorAll(".btn-eliminar").forEach(button => {
                                button.addEventListener("click", function () {
                                    const id = this.getAttribute("data-id");
                                    eliminarPonente(id);
                                });
                            });
                        }
                    })
                    .catch(error => console.error("Error al obtener ponentes:", error));
            }

            function eliminarPonente(id) {
                if (!confirm("¿Seguro que quieres eliminar este ponente?")) return;

                fetch(`http://localhost:8000/api/ponentes/${id}`, {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" }
                })
                .then(response => response.json())
                .then(() => {
                    alert("Ponente eliminado correctamente.");
                    cargarPonentes();
                })
                .catch(error => {
                    console.error("Error al eliminar ponente:", error);
                    alert("No se pudo eliminar el ponente.");
                });
            }

            btnGuardarPonente.addEventListener("click", () => {
                const nombre = document.getElementById("nombre").value;
                const foto = document.getElementById("foto").value;
                const experiencia = document.getElementById("experiencia").value;
                const redes_sociales = document.getElementById("redes_sociales").value;

                if (!nombre || !foto || !experiencia || !redes_sociales) {
                    alert("Todos los campos son obligatorios");
                    return;
                }

                const ponenteData = { nombre, foto, experiencia, redes_sociales };
                let url = "http://localhost:8000/api/ponentes";
                let method = "POST";

                if (editando) {
                    url = `http://localhost:8000/api/ponentes/${idPonenteEditando}`;
                    method = "PUT";
                }

                fetch(url, {
                    method: method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(ponenteData)
                })
                .then(response => response.json())
                .then(() => {
                    modalCrearPonente.classList.add("hidden");
                    editando = false;
                    idPonenteEditando = null;
                    cargarPonentes();
                })
                .catch(error => console.error("Error al guardar ponente:", error));
            });

            btnCerrarModal.addEventListener("click", () => {
                modalCrearPonente.classList.add("hidden");
                editando = false;
                idPonenteEditando = null;
            });

            btnCrearPonente.addEventListener("click", () => {
                editando = false;
                idPonenteEditando = null;
                modalTitulo.textContent = "Crear Ponente";
                modalCrearPonente.classList.remove("hidden");
            });

            cargarPonentes();
        });
    </script>
</x-app-layout>
