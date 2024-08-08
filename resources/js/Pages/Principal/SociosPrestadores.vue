<script setup>
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import { ref, onMounted, nextTick } from 'vue';
import Swal from 'sweetalert2';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import FormularioSP from '../../Components/Principal/FormularioSP.vue';
import Mensaje from '../../Components/Mensaje.vue';
import FormularioActualizarSP from '../../Components/Principal/FormularioActualizarSP.vue';
import { jsPDF } from 'jspdf';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    directivo: { type: Object },
    tipDirectivo: { type: Object },
    usuario: { type: Object},
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
        "Apellido Paterno",
        "Apellido Materno",
        "Nombre",
        "Tipo De Directivo",
        "Número De Unidades",
        "Número De Operadores"
    ];
    // Extraer los datos filtrados y visibles de la tabla
    const filas = [];
    nextTick(() => {
        const table = $('#directivosTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados
        data.each((row) => {
            filas.push([
                row.idDirectivo,
                row.apellidoP,
                row.apellidoM,
                row.nombre,
                props.tipDirectivo.find(tDir => tDir.idTipoDirectivo === row.idTipoDirectivo)?.tipoDirectivo || '',
                row.numUnidades,
                row.numOperadores
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
        const table = $('#directivosTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados

        // Convertir los datos a formato JSON
        const jsonData = data.toArray().map(row => ({
            ID: row.idDirectivo,
            'Apellido Paterno': row.apellidoP,
            'Apellido Materno': row.apellidoM,
            'Nombre': row.nombre,
            'Tipo De Directivo': props.tipDirectivo.find(tDir => tDir.idTipoDirectivo === row.idTipoDirectivo)?.tipoDirectivo || '',
            'Número De Unidades': row.numUnidades,
            'Número De Operadores': row.numOperadores,
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
            columns: [0, 2] // Indica qué columnas deben ser copiadas (por ejemplo, aquí se copiarían las columnas 0 y 2)
        },
        button: true
    },
    {
        title: 'Directivos Registrados',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        action: () => exportarExcel() // Usa la función de exportar a Excel
    },
    {
        title: 'Directivos Registrados',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        action: () => exportarPDF(props.title || 'Directivos Registrados')
    },
    {
        title: 'Directivos Registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
        columns: [2,3,4,5,6] // Índices de las columnas que deseas imprimir (por ejemplo, imprimir las columnas 0 y 2)
    }
    }
];

const columnas = [
    {
        data: null,
        render: function (data, type, row, meta) {
            return "";
        }
    },
    {
        data: null,
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="directivos-checkboxes" data-id="${row.idDirectivo}" ">`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    { data: 'apellidoP' },
    { data: 'apellidoM' },
    { data: 'nombre' },
    {
        data: 'idTipoDirectivo',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const tDirectivo = props.tipDirectivo.find(tDirectivo => tDirectivo.idTipoDirectivo === data);
            return tDirectivo ? tDirectivo.tipoDirectivo : '';
        }
    },
    { data: 'numUnidades' },
    { data: 'numOperadores' },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idDirectivo}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        }
    },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({
    _method: 'DELETE',
});

const directivosSeleccionados = ref([]);

var directivoE = ({});
const abrirE = ($directivoss) => {
    directivoE = $directivoss;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleDirectivoSelection = (directivo) => {
    if (directivosSeleccionados.value.includes(directivo)) {
        // Si el alumno ya está seleccionado, la eliminamos del array
        directivosSeleccionados.value = directivosSeleccionados.value.filter((d) => d !== directivo);
    } else {
        // Si el alumno no está seleccionado, la agregamos al array
        directivosSeleccionados.value.push(directivo);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    // Cambio de estado del botón eliminar dependiendo de las materias seleccionadas
    if (directivosSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

onMounted(() => {
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('directivosTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('directivos-checkboxes')) {
            const directivoId = parseInt(checkbox.getAttribute('data-id'));
            if (props.directivo) {
                const dir = props.directivo.find(dir => dir.idDirectivo === directivoId);
                if (dir) {
                    toggleDirectivoSelection(dir);
                } else {
                    console.log("No se tiene directivo");
                }
            }
        }
    });

    // Manejar clic en el botón de editar
    $('#directivosTablaId').on('click', '.editar-button', function () {
        const directivoId = $(this).data('id');
        const dir = props.directivo.find(d => d.idDirectivo === directivoId);
        abrirE(dir);
    });
});

const eliminarDirectivos = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    // Obtener los nombres de las rutas seleccionadas
    const nombreDirectivos = directivosSeleccionados.value.map((directivo) => directivo.nombre_completo).join(', ');

    swal.fire({
        title: '¿Estas seguro que deseas eliminar al directivo seleccionado?',
        html: `Directivo seleccionado: ${nombreDirectivos}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const directivoE = directivosSeleccionados.value.map((directivo) => directivo.idDirectivo);
                const $directivosIds = directivoE.join(',');
                await form.post(route('principal.eliminarDirectivo', $directivosIds));
                directivosSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (directivosSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Directivo eliminado correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Directivo eliminado correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varias directivos: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar al directivo. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
};

</script>

<template>
    <PrincipalLayout title="Directivos" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Directivos</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

            <Mensaje/>

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Socio/Prestador
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarDirectivos">
                    <i class="fa fa-trash mr-2"></i>Borrar Socio/Prestador
                </button>
            </div>
            <div>
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="directivosTablaId" name="directivosTablaId" :columns="columnas" :data="directivo" :options="{
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
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                ID
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Paterno
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Materno
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Nombre
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Tipo Directivo
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Numero de Unidades
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Numero de Operadores
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioSP :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir directivo'" :op="'1'" :modal="'modalCreate'" :tipDirectivo="props.tipDirectivo"
            :directivo="props.directivo"></FormularioSP>
        <FormularioActualizarSP :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable" @close="cerrarModalE"
            :title="'Editar directivo'" :op="'2'" :modal="'modalEdit'" :tipDirectivo="props.tipDirectivo"
            :directivo="directivoE"></FormularioActualizarSP>
    </PrincipalLayout>
</template>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>