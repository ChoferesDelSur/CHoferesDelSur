<script setup>
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import Swal from 'sweetalert2';
import { ref, onMounted, nextTick } from 'vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import Mensaje from '../../Components/Mensaje.vue';
import RHLayout from '../../Layouts/RHLayout.vue';
import FormularioVacaciones from '../../Components/RH/FormularioVacaciones.vue';
import { jsPDF } from 'jspdf';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    usuario: { type: Object },
    personal: { type: Object },
    vacaciones: { type: Object },
});

const exportarPDF = (titulo = 'Documento') => {
    const doc = new jsPDF('landscape');
    // Título del documento
    doc.setFontSize(12);
    doc.text(titulo, 14, 22); // Posiciona el título en la parte superior izquierda
    // Fecha de generación del documento
    const fecha = new Date().toLocaleDateString();
    doc.setFontSize(8);
    doc.text(`Fecha: ${fecha}`, 260, 22); // Posiciona la fecha en la parte superior derecha
    // Definir las columnas de la tabla
    const columnas = [
        "ID",
        "Personal Administrativo",
        "Motivo",
        "Número De Días",
        "Fecha De Inicio",
        "Fecha De Fín"
    ];
    // Extraer los datos filtrados y visibles de la tabla
    const filas = [];
    nextTick(() => {
        const table = $('#vacacionesTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados
        data.each((row) => {
            filas.push([
                row.idVacaciones,
                props.personal.find(per => per.idPersonal === row.idPersonal)?.nombre_completo || '',
                row.motivo,
                row.numeroDias,
                row.fechaInicio,
                row.fechaFin
            ]);
        });
        // Generar la tabla en el PDF
        doc.autoTable({
            head: [columnas],
            body: filas,
            startY: 24 // Ajusta el inicio de la tabla debajo del título y la fecha
        });
        // Guardar el documento con el título como nombre del archivo
        doc.save(`${titulo}.pdf`);
    });
};

const exportarExcel = () => {
    nextTick(() => {
        // Obtener la instancia de DataTable
        const table = $('#vacacionesTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados

        // Convertir los datos a formato JSON
        const jsonData = data.toArray().map(row => ({
            ID: row.idVacaciones,
            'Personal administrativo': props.personal.find(per => per.idPersonal === row.idPersonal)?.nombre_completo || '',
            'Motivo': row.motivo,
            'Número de Días': row.numeroDias,
            'Fecha De Inicio': row.fechaInicio,
            'Fecha De Fin': row.fechaFin,
        }));

        // Crear la hoja de Excel
        const ws = XLSX.utils.json_to_sheet(jsonData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Directivos Registrados');

        // Guardar el archivo Excel
        XLSX.writeFile(wb, 'Directivos Registrados.xlsx');
    });
};

const botonesPersonalizados = [
    {
        extend: 'copyHtml5',
        text: '<i class="fa-solid fa-copy"></i> Copiar', // Texto del botón
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [0, 2, 3, 4, 5, 6] // Indica qué columnas deben ser copiadas (por ejemplo, aquí se copiarían las columnas 0 y 2)
        },
        button: true
    },
    {
        title: 'Movimientos registrados',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        action: () => exportarExcel()
    },
    {
        title: 'Movimientos registrados',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        orientation: 'landscape', // Configurar la orientación horizontal
        action: () => exportarPDF(props.title || 'Vacaciones Registrados')
    },
    {
        title: 'Operadores registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6] // Índices de las columnas que deseas imprimir 
        }
    }
];

const columnas = [
    {
        data: null,
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="vacaciones-checkboxes" data-id="${row.idVacaciones}" ">`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    {
        data: 'idPersonal',
        render: function (data, type, row, meta) {
            const persona = props.personal.find(persona => persona.idPersonal === data);
            return persona ? persona.nombre_completo : '';
        }
    },
    { data: 'motivo' },
    { data: 'numeroDias' },
    { data: 'fechaInicio' },
    { data: 'fechaFin' },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;
const vacacionesSeleccionados = ref([]);

const form = useForm({
    _method: 'DELETE',
});

var vacacionesE = ({});
const abrirE = ($vacacioness) => {
    vacacionesE = $vacacioness;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleVacacionesSelection = (vacaciones) => {
    if (vacacionesSeleccionados.value.includes(vacaciones)) {
        // Si el operador ya está seleccionado, la eliminamos del array
        vacacionesSeleccionados.value = vacacionesSeleccionados.value.filter((v) => v !== vacaciones);
    } else {
        // Si el operador no está seleccionado, la agregamos al array
        vacacionesSeleccionados.value.push(vacaciones);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    if (vacacionesSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

onMounted(() => {
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('vacacionesTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('vacaciones-checkboxes')) {
            const vacacionesId = parseInt(checkbox.getAttribute('data-id'));
            if (props.vacaciones) {
                const vac = props.vacaciones.find(vac => vac.idVacaciones === vacacionesId);
                if (vac) {
                    toggleVacacionesSelection(vac);
                } else {
                    console.log("No se tiene movimiento");
                }
            }
        }
    });
});

const eliminarVacaciones = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    // Construir el mensaje con los datos del operador y el estado
    const mensaje = vacacionesSeleccionados.value.map((vacaciones) => {
        const personal = props.personal.find(per => per.idPersonal === vacaciones.idPersonal);

        return `${personal ? personal.nombre_completo : 'Desconocido'}`;
    }).join('<br>');

    swal.fire({
        title: '¿Estas seguro que deseas eliminar las vacaciones del personal seleccionado?',
        html: `Vacaciones seleccionado:<br>${mensaje}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const vacacionesE = vacacionesSeleccionados.value.map((vacaciones) => vacaciones.idVacaciones);
                const $vacacionesIds = vacacionesE.join(',');
                await form.post(route('rh.eliminarVacaciones', $vacacionesIds));
                vacacionesSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (vacacionesSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Vacaciones eliminado(s) correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Vacaciones eliminado(s) correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varias vacaciones: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar el movimiento. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
};

</script>

<template>

    <RHLayout title="Vacaciones" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Vacaciones</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Registrar Vacaciones
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarVacaciones">
                    <i class="fa fa-trash mr-2"></i> Eliminar Vacaciones
                </button>
                <button class="bg-sky-500 hover:bg-sky-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModalReincor = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-refresh" aria-hidden="true"></i> Restablecer Vacaciones
                </button>
            </div>

            <div class="overflow-x-auto">
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="vacacionesTablaId" name="vacacionesTablaId" :columns="columnas" :data="vacaciones" :options="{
                        responsive: false, autoWidth: false, dom: 'Bftrip', language: {
                            search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
                            info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                            infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                            infoFiltered: '(filtrado de un total de _MAX_ registros)',
                        }, buttons: [botonesPersonalizados],
                        paging: false,// Esto es para quitar la paginacion
                    }">
                    <thead>
                        <tr class="text-sm leading-normal">
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                ID
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Personal Administrativo
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Motivo
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Número de Días
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha de Inicio
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha de Fin
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioVacaciones :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir Vacaciones'" :modal="'modalCreate'" :personal="props.personal" :vacaciones="props.vacaciones">
        </FormularioVacaciones>
    </RHLayout>
</template>

<style>
/* Estilo personalizado para centrar el texto en las celdas de la tabla */
#movimientosTablaId th {
    text-align: center !important;
}

.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}

.expired-row {
    background-color: red !important;
    color: white;
    /* Opcional, para mejorar la legibilidad del texto */
}

.bg-red-500 {
    background-color: #f56565;
    /* rojo claro en Tailwind CSS */
}

.text-white {
    color: #ffffff;
}
</style>