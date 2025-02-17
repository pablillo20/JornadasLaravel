<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ponentes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Lista de Ponentes</h3>
        </div>
        <div id="ponentes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        <p id="no-ponentes" class="text-gray-500 hidden">No hay ponentes</p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("ponentes-container");

            function cargarPonentes() {
                fetch("http://localhost:8000/api/ponentes")
                    .then(response => response.json())
                    .then(data => {
                        container.innerHTML = "";
                        const noPonentes = document.getElementById("no-ponentes");

                        if (!data.ponente || data.ponente.length === 0) {
                            noPonentes.classList.remove("hidden");
                        } else {
                            noPonentes.classList.add("hidden");
                            data.ponente.forEach(ponente => {
                                let card = document.createElement("div");
                                card.className = "bg-white shadow-md rounded-lg p-4 flex flex-col items-center border border-gray-200";
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
                        console.error('Error al cargar los ponentes:', error);
                    });
            }

            cargarPonentes();
        });
    </script>
</x-app-layout>
    