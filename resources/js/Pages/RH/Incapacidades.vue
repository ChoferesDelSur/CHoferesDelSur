<script setup>
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import { ref, onMounted, nextTick } from 'vue';
import Swal from 'sweetalert2';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import Mensaje from '../../Components/Mensaje.vue';
import RHLayout from '../../Layouts/RHLayout.vue';
import FormularioIncapacidad from '../../Components/RH/FormularioIncapacidad.vue';
import FormularioActualizarIncapacidad from '../../Components/RH/FormularioActualizarIncapacidad.vue';
import FormularioReincorporar from '../../Components/RH/FormularioReincorporar.vue';
import { jsPDF } from 'jspdf';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    operador: { type: Object },
    incapacidad: { type: Object },
    directivo: { type: Object },
    usuario: { type: Object },
    operadoresAlta: { type: Object },
    operadoresIncapacidad: {type: Object},
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
        "Operador",
        "Motivo de Incapacidad",
        "Número de Días",
        "Fecha de Inicio",
        "Fecha de Fin"
    ];
    // Extraer los datos filtrados y visibles de la tabla
    const filas = [];
    nextTick(() => {
        const table = $('#incapacidadesTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados
        data.each((row) => {
            filas.push([
                row.idIncapacidad,
                props.operador.find(chofer => chofer.idOperador === row.idOperador)?.nombre_completo || '',
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
        const table = $('#incapacidadesTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados

        // Convertir los datos a formato JSON
        const jsonData = data.toArray().map(row => ({
            ID: row.idIncapacidad,
            'Operador': props.operador.find(chofer => chofer.idOperador === row.idOperador)?.nombre_completo || '',
            'Motivo de Incapacidad': row.motivo,
            'Número de Días': row.numeroDias,
            'Fecha de Inicio': row.fechaInicio,
            'Fecha de Fin': row.fechaFin
        }));

        // Crear la hoja de Excel
        const ws = XLSX.utils.json_to_sheet(jsonData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Incapacidades Registrados');

        // Guardar el archivo Excel
        XLSX.writeFile(wb, 'Incapacidades Registrados.xlsx');
    });
};

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
        title: 'Directivos registrados',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        action: () => exportarExcel() // Usa la función de exportar a Excel
    },
    {
        title: 'Directivos registrados',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        action: () => exportarPDF(props.title || 'Incapacidades Registradas')
    },
    {
        title: 'Directivos registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [2, 3, 4, 5, 6] // Índices de las columnas que deseas imprimir (por ejemplo, imprimir las columnas 0 y 2)
        }
    }
];

const columnas = [
    {
        data: null,
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="incapacidades-checkboxes" data-id="${row.idIncapacidad}" ">`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    {
        data: 'idOperador',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const chofer = props.operador.find(chofer => chofer.idOperador === data);
            return chofer ? chofer.nombre_completo : '';
        }
    },
    { data: 'motivo' },
    { data: 'numeroDias' },
    { data: 'fechaInicio' },
    { data: 'fechaFin' },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idIncapacidad}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        }
    },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const mostrarModalReincor = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({
    _method: 'DELETE',
});

const incapacidadesSeleccionados = ref([]);

var incapacidadE = ({});
const abrirE = ($incapacidadess) => {
    incapacidadE = $incapacidadess;
    mostrarModalE.value = true;
}

const cerrarModalR = () => {
    mostrarModalReincor.value = false;
};


const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleIncapacidadSelection = (incapacidad) => {
    if (incapacidadesSeleccionados.value.includes(incapacidad)) {
        // Si el alumno ya está seleccionado, la eliminamos del array
        incapacidadesSeleccionados.value = incapacidadesSeleccionados.value.filter((i) => i !== incapacidad);
    } else {
        // Si el alumno no está seleccionado, la agregamos al array
        incapacidadesSeleccionados.value.push(incapacidad);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    // Cambio de estado del botón eliminar dependiendo de las materias seleccionadas
    if (incapacidadesSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

onMounted(() => {
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('incapacidadesTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('incapacidades-checkboxes')) {
            const incapacidadId = parseInt(checkbox.getAttribute('data-id'));
            if (props.incapacidad) {
                const inc = props.incapacidad.find(inc => inc.idIncapacidad === incapacidadId);
                if (inc) {
                    toggleIncapacidadSelection(inc);
                } else {
                    console.log("No se tiene incapacidad");
                }
            }
        }
    });

    // Manejar clic en el botón de editar
    $('#incapacidadesTablaId').on('click', '.editar-button', function () {
        const incapacidadId = $(this).data('id');
        const inc = props.incapacidad.find(i => i.idIncapacidad === incapacidadId);
        abrirE(inc);
    });
});

const eliminarIncapacidades = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })

    // Recoger los nombres de los operadores seleccionados
    const nombresOperadores = incapacidadesSeleccionados.value.map((incapacidad) => {
        const operador = props.operador.find(o => o.idOperador === incapacidad.idOperador);
        return operador ? operador.nombre_completo : '';
    }).filter(nombre => nombre !== '').join(', ');

    swal.fire({
        title: '¿Esta seguro que desea eliminar la(s) incapacidad(es) del(los) operador(es) seleccionado(s)?',
        html: `Operador(es) seleccionado(s): ${nombresOperadores}`,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const incapacidadE = incapacidadesSeleccionados.value.map((incapacidad) => incapacidad.idIncapacidad);
                const $incapacidadesIds = incapacidadE.join(',');
                await form.post(route('rh.eliminarIncapacidad', $incapacidadesIds));
                incapacidadesSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (incapacidadesSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Incapacidad eliminado correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Incapacidad eliminado correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varias incapacidades: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar la incapacidad. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
};

</script>

<template>
    <RHLayout title="Incapacidades" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Incapacidades</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Incapacidad
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarIncapacidades">
                    <i class="fa fa-trash mr-2"></i>Borrar Incapacidad
                </button>
                <button class="bg-sky-500 hover:bg-sky-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModalReincor = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-refresh" aria-hidden="true"></i> Reincorporar Operador
                </button>
            </div>
            <div>
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="incapacidadesTablaId" name="incapacidadesTablaId" :columns="columnas" :data="incapacidad" :options="{
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
                                Operador
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Motivo de incapacidad
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Número de Días
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha de Inicio
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha de Fin
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioIncapacidad :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir incapacidad'" :op="'1'" :modal="'modalCreate'" :operador="props.operador"
            :operadoresAlta="props.operadoresAlta"></FormularioIncapacidad>
        <FormularioActualizarIncapacidad :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalE" :title="'Editar Incapacidad'" :modal="'modalEdit'" :operador="props.operador" :incapacidad="incapacidadE">
        </FormularioActualizarIncapacidad>
        <FormularioReincorporar :show="mostrarModalReincor" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalR" :title="'Reincorporar Operador'" :modal="'modalEdit'" :operador="props.operador" :operadoresIncapacidad="props.operadoresIncapacidad">
        </FormularioReincorporar>
    </RHLayout>
</template>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>