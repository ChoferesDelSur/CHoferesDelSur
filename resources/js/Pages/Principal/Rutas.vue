<script setup>
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import jsZip from 'jszip';
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import FormularioRuta from '../../Components/Principal/FormularioRuta.vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import pdfMake from 'pdfmake/build/pdfmake';
import Mensaje from '../../Components/Mensaje.vue';
import FormularioActualizarRuta from '../../Components/Principal/FormularioActualizarRuta.vue';

// Variables e inicializaciones necesarias para el datatable y el uso de generacion de 
// documentos
window.JSZip = jsZip;

pdfMake.fonts = {
    Roboto: {
        normal: "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Regular.ttf",
        bold: "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Medium.ttf",
        italics: "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Italic.ttf",
        bolditalics: "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-MediumItalic.ttf",
    },
};

DataTable.use(DataTablesLib);
DataTable.use(Select);
const props = defineProps({
    ruta: { type: Object },
    usuario: { type: Object },
});

const botonesPersonalizados = [
    {
        extend: 'copyHtml5',
        text: '<i class="fa-solid fa-copy"></i> Copiar', // Texto del botón
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [0, 2] // Indica qué columnas deben ser copiadas (por ejemplo, aquí se copiarían las columnas 0 y 2)
        },
        button: true
    },
    {
        title: 'Rutas registradas',
        extend: 'excelHtml5',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        exportOptions: {
            columns: [1, 2]
        }
    },
    {
        title: 'Rutas registradas',
        extend: 'pdfHtml5',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [1, 2]
        },
    },
    {
        title: 'Rutas registradas',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [1, 2] // Índices de las columnas que deseas imprimir (por ejemplo, imprimir las columnas 0 y 2)
        }
    }
];

const columnas = [
    {
        data: null,
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="ruta-checkboxes" data-id="${row.idRuta}" ">`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    { data: 'nombreRuta' },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idRuta}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        }
    },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({
    _method: 'DELETE', // Método simulado para Laravel
});
const rutasSeleccionados = ref([]);
var rutaE = ({});

const abrirE = ($rutass) => {
    rutaE = $rutass;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleRutaSelection = (ruta) => {
    if (rutasSeleccionados.value.includes(ruta)) {
        // Si el alumno ya está seleccionado, la eliminamos del array
        rutasSeleccionados.value = rutasSeleccionados.value.filter((r) => r !== ruta);
    } else {
        // Si el alumno no está seleccionado, la agregamos al array
        rutasSeleccionados.value.push(ruta);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    // Cambio de estado del botón eliminar dependiendo de las materias seleccionadas
    if (rutasSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};
/* 
const importarPdfMake = async () => {
    try {
        // Importa pdfmake y configura las fuentes
        const pdfMake = await import('pdfmake/build/pdfmake');
        const pdfFonts = await import('pdfmake/build/vfs_fonts');

        // Asigna pdfMake al objeto global y configura las fuentes
        window.pdfMake = pdfMake.default;
        window.pdfMake.vfs = pdfFonts.default.pdfMake.vfs;

        // Configura las fuentes específicas (en este caso, solo Roboto)
        pdfMake.defaultFont = {
            normal: 'Roboto-Regular.ttf',
            bold: 'Roboto-Medium.ttf',
            italics: 'Roboto-Italic.ttf',
            bolditalics: 'Roboto-MediumItalic.ttf'
        };

        // Ahora puedes utilizar pdfMake y pdfFonts en tu código
    } catch (error) {
        console.error('Error al importar pdfmake:', error);
    }
}; */

onMounted(() => {
    /* importarPdfMake(); */
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('rutasTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('ruta-checkboxes')) {
            const rutaId = parseInt(checkbox.getAttribute('data-id'));
            if (props.ruta) {
                const rutt = props.ruta.find(rutt => rutt.idRuta === rutaId);
                if (rutt) {
                    toggleRutaSelection(rutt);
                } else {
                    console.log("No se tiene ruta");
                }
            }
        }
    });

    // Manejar clic en el botón de editar
    $('#rutasTablaId').on('click', '.editar-button', function () {
        const rutaId = $(this).data('id');
        const rutt = props.ruta.find(r => r.idRuta === rutaId);
        abrirE(rutt);
    });
});

const eliminarRutas = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    // Obtener los nombres de las rutas seleccionadas
    const nombresRutas = rutasSeleccionados.value.map((ruta) => ruta.nombreRuta).join(', ');

    swal.fire({
        title: '¿Estas seguro que deseas eliminar la(s) ruta(s) seleccionada(s)?',
        html: `Rutas seleccionadas: ${nombresRutas}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const rutaE = rutasSeleccionados.value.map((ruta) => ruta.idRuta);
                const $rutasIds = rutaE.join(',');
                await form.post(route('principal.eliminarRuta', $rutasIds));
                rutasSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (rutasSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Ruta(s) eliminada(s) correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Ruta(s) eliminada(s) correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varias rutas: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar las rutas. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
};
</script>

<template>
    <PrincipalLayout title="Rutas" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Rutas</h2>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Ruta
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarRutas">
                    <i class="fa fa-trash mr-2"></i>Borrar Ruta
                </button>
            </div>

            <div>
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="rutasTablaId" name="rutasTablaId" :columns="columnas" :data="ruta" :options="{
                        responsive: true, autoWidth: false, dom: 'Bftrip', language: {
                            search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
                            info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                            infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                            infoFiltered: '(filtrado de un total de _MAX_ registros)',
                            lengthMenu: 'Mostrar _MENU_ registros',
                            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
                        }, buttons: [botonesPersonalizados],
                    }">
                    <thead>
                        <tr class="text-sm leading-normal">
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                ID
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Ruta
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioRuta :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir ruta'" :op="'1'" :modal="'modalCreate'" :ruta="props.ruta"></FormularioRuta>
        <FormularioActualizarRuta :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalE" :title="'Editar ruta'" :op="'2'" :modal="'modalEdit'" :ruta="rutaE">
        </FormularioActualizarRuta>
    </PrincipalLayout>
</template>
<style>
.swal2-popup {
    font-size: 14px !important;
}
</style>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>