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
import ServicioLayout from '../../Layouts/ServicioLayout.vue';
import FormularioOperadores from '../../Components/Servicio/FormularioOperadores.vue';
import FormularioActualizarOperadores from '../../Components/Servicio/FormularioActualizarOperadores.vue';
import { jsPDF } from 'jspdf';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    operador: { type: Object },
    tipoOperador: { type: Object },
    estado: { type: Object },
    directivo: { type: Object },
    usuario: { type: Object },
    incapacidad: { type: Object },
    empresa: { type: Object },
    convenioPago: { type: Object },
    direccion: { type: Object },
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
        "Tipo de Operador",
        "Estado",
        "Jefe"
    ];
    // Extraer los datos filtrados y visibles de la tabla
    const filas = [];
    nextTick(() => {
        const table = $('#operadoresTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados
        data.each((row) => {
            filas.push([
                row.idOperador,
                row.apellidoP,
                row.apellidoM,
                row.nombre,
                props.tipoOperador.find(tipOp => tipOp.idTipoOperador === row.idTipoOperador)?.tipOperador || '',
                props.estado.find(estad => estad.idEstado === row.idEstado)?.estado || '',
                props.directivo.find(jefe => jefe.idDirectivo === row.idDirectivo)?.nombre_completo || ''
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
        const table = $('#operadoresTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados

        // Convertir los datos a formato JSON
        const jsonData = data.toArray().map(row => ({
            ID: row.idOperador,
            'Apellido Paterno': row.apellidoP,
            'Apellido Materno': row.apellidoM,
            Nombre: row.nombre,
            'Tipo de Operador': props.tipoOperador.find(tipOp => tipOp.idTipoOperador === row.idTipoOperador)?.tipOperador || '',
            Estado: props.estado.find(estad => estad.idEstado === row.idEstado)?.estado || '',
            Jefe: props.directivo.find(jefe => jefe.idDirectivo === row.idDirectivo)?.nombre_completo || ''
        }));

        // Crear la hoja de Excel
        const ws = XLSX.utils.json_to_sheet(jsonData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Operadores Registrados');

        // Guardar el archivo Excel
        XLSX.writeFile(wb, 'Operadores Registrados.xlsx');
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
        title: 'Operadores registrados',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        action: () => exportarExcel() // Usa la función de exportar a Excel
    },
    {
        title: 'Operadores registrados',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        orientation: 'landscape', // Configurar la orientación horizontal
        action: () => exportarPDF(props.title || 'Operadores registrados')
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
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    { data: 'apellidoP' },
    { data: 'apellidoM' },
    { data: 'nombre' },
    {
        data: 'idTipoOperador',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const tipOp = props.tipoOperador.find(tipOp => tipOp.idTipoOperador === data);
            return tipOp ? tipOp.tipOperador : '';
        }
    },
    {
        data: 'idEstado',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const estad = props.estado.find(estad => estad.idEstado === data);
            return estad ? estad.estado : '';
        }
    },
    {
        data: null,
        render: function (data, type, row, meta) {
            const operador = props.operador.find(op => op.idOperador === row.idOperador);
            if (operador && operador.ultimaIncapacidad && operador.idEstado === 3) {
                return operador.ultimaIncapacidad.numeroDias;
            }
            return ''; // Mostrar vacío si no cumple la condición
        }
    },
    {
    data: null,
    render: function (data, type, row, meta) {
        const operador = props.operador.find(op => op.idOperador === row.idOperador);
        if (operador && operador.ultimaIncapacidad && operador.idEstado === 3 && operador.ultimaIncapacidad.fechaInicio) {
            // Convertir la fecha explícitamente a UTC
            const fechaInicio = new Date(operador.ultimaIncapacidad.fechaInicio + 'T00:00:00Z');
            const dia = ('0' + fechaInicio.getUTCDate()).slice(-2);
            const mes = ('0' + (fechaInicio.getUTCMonth() + 1)).slice(-2);
            const anio = fechaInicio.getUTCFullYear();
            return `${dia}/${mes}/${anio}`;
        }
        return ''; // Mostrar vacío si no cumple la condición
    }
},
{
    data: null,
    render: function (data, type, row, meta) {
        const operador = props.operador.find(op => op.idOperador === row.idOperador);
        if (operador && operador.ultimaIncapacidad && operador.idEstado === 3 && operador.ultimaIncapacidad.fechaFin) {
            // Convertir la fecha explícitamente a UTC
            const fechaFin = new Date(operador.ultimaIncapacidad.fechaFin + 'T00:00:00Z');
            const dia = ('0' + fechaFin.getUTCDate()).slice(-2);
            const mes = ('0' + (fechaFin.getUTCMonth() + 1)).slice(-2);
            const anio = fechaFin.getUTCFullYear();
            return `${dia}/${mes}/${anio}`;
        }
        return ''; // Mostrar vacío si no cumple la condición
    }
},

    {
        data: 'fechaAlta',
        render: function (data, type, row, meta) {
            // Verifica si el dato es válido
            if (data) {
                // Asume que la fecha está en formato yyyy-mm-dd
                const parts = data.split('-'); // Divide la fecha en partes
                const year = parts[0];
                const month = parts[1];
                const day = parts[2];
                return `${day}-${month}-${year}`;
            }
            return '';
        }
    },
    {
        data: 'fechaBaja',
        render: function (data, type, row, meta) {
            // Verifica si el dato es válido
            if (data) {
                // Asume que la fecha está en formato yyyy-mm-dd
                const parts = data.split('-'); // Divide la fecha en partes
                const year = parts[0];
                const month = parts[1];
                const day = parts[2];
                return `${day}-${month}-${year}`;
            }
            return '';
        }
    },
    {
        data: 'numLicencia',
    },
    {
        data: 'idDirectivo',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const jefe = props.directivo.find(jefe => jefe.idDirectivo === data);
            return jefe ? jefe.nombre_completo : '';
        }
    },
    /* {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idOperador}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        }
    }, */
]
console.log(props.operador);

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;
const operadoresSeleccionados = ref([]);

const form = useForm({
    _method: 'DELETE',
});

var operadorE = ({});
const abrirE = ($operadoress) => {
    operadorE = $operadoress;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleOperadorSelection = (operador) => {
    if (operadoresSeleccionados.value.includes(operador)) {
        // Si el operador ya está seleccionado, la eliminamos del array
        operadoresSeleccionados.value = operadoresSeleccionados.value.filter((r) => r !== operador);
    } else {
        // Si el operador no está seleccionado, la agregamos al array
        operadoresSeleccionados.value.push(operador);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    if (operadoresSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

onMounted(() => {

    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('operadoresTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('operador-checkboxes')) {
            const operadorId = parseInt(checkbox.getAttribute('data-id'));
            if (props.operador) {
                const oper = props.operador.find(oper => oper.idOperador === operadorId);
                if (oper) {
                    toggleOperadorSelection(oper);
                } else {
                    console.log("No se tiene operador");
                }
            }
        }
    });

    // Manejar clic en el botón de editar
    $('#operadoresTablaId').on('click', '.editar-button', function () {
        const operadorId = $(this).data('id');
        const oper = props.operador.find(o => o.idOperador === operadorId);
        abrirE(oper);
    });
});

</script>

<template>

    <ServicioLayout title="Operadores" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Operadores</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

            <div>
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="operadoresTablaId" name="operadoresTablaId" :columns="columnas" :data="operador" :options="{
                        responsive: true, autoWidth: false, dom: 'Bftrip', language: {
                            search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
                            info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                            infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                            infoFiltered: '(filtrado de un total de _MAX_ registros)',
                            lengthMenu: 'Mostrar _MENU_ registros',
                            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
                        }, buttons: [botonesPersonalizados],
                        pageLength: 23
                    }">
                    <thead>
                        <tr class="text-sm leading-normal">
                            <th class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                colspan="6"></th>
                            <th class="py-2 px-4 bg-blue-300 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left text-center"
                                colspan="3" style="text-align: center; vertical-align: middle;">INCAPACIDAD</th>
                            <th class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                colspan="4"></th>
                        </tr>
                        <tr class="text-sm leading-normal">
                            <!-- <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                            </th> -->
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                ID
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Paterno
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Materno
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Nombre
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Tipo de operador
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Estado
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-300 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Días
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-300 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Inicio
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-300 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fin
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha Alta
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha Baja
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Núm. Licencia
                            </th>
                            <th
                                class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Jefe
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioOperadores :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir operador'" :modal="'modalCreate'" :operador="props.operador"
            :tipoOperador="props.tipoOperador" :estado="props.estado" :directivo="props.directivo">
        </FormularioOperadores>
        <FormularioActualizarOperadores :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalE" :title="'Editar operador'" :modal="'modalEdit'" :tipoOperador="props.tipoOperador"
            :estado="props.estado" :directivo="props.directivo" :operador="operadorE"></FormularioActualizarOperadores>
    </ServicioLayout>
</template>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>