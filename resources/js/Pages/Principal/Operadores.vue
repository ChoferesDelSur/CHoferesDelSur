<script setup>
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import pdfmake from 'pdfmake';
import { useForm } from '@inertiajs/inertia-vue3';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5.mjs';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import jsZip from 'jszip';
import { ref, watch } from 'vue';
import FormularioOperadores from '../../Components/Principal/FormularioOperadores.vue';

// Variables e inicializaciones necesarias para el datatable y el uso de generacion de 
// documentos
window.JSZip = jsZip;
//pdfmake.vfs = pdfFonts.pdfMake.vfs;
pdfmake.fonts = {
    Roboto: {
        normal:
            "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Regular.ttf",
        bold: "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Medium.ttf",
        italics:
            "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Italic.ttf",
        bolditalics:
            "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-MediumItalic.ttf",
    },
};

DataTable.use(DataTablesLib);
DataTable.use(Select);
DataTable.use(pdfmake);
DataTable.use(ButtonsHtml5);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    operador: { type: Object },
    tipoOperador: { type: Object },
    estado: { type: Object },
    directivo: { type: Object },
});

console.log("Operadores");
console.log(props.operador);

const botones = [
    {
        title: 'Operadores Registrados',
        extend: 'excelHtml5',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
        },
    },
    {
        title: 'Operadores Registrados',
        extend: 'pdfHtml5',
        customize: function (doc) {
            doc.content.splice(0, 0, {
                margin: [0, 0, 0, 0],
                alignment: 'center',

            });
        },
        text: '<i class="fa-solid fa-file-pdf"></i> PDF',
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
        },
        orientation: 'landscape',
        pageSize: 'TABLOID',
    },
    /* {
        title: 'Unidades Registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir',
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
        },
        orientation: 'landscape',
    }, */
]

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
            return `<input type="checkbox" class="operador-checkboxes" data-id="${row.idOperador}" ">`;
        }
    },
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
        data: 'idDirectivo',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const jefe = props.directivo.find(jefe => jefe.idDirectivo === data);
            return jefe ? jefe.nombre_completo : '';
        }
    },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({});

const abrirE = ($clasee) => {
    claseE = $clasee;
    mostrarModalE.value = true;
    console.log($clasee);
    console.log(claseE);
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

console.log("Estoy en Operadores");


</script>

<template>

    <PrincipalLayout title="Operadores">
        <div class="mt-8 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-5">Operadores</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

            <!-- Para mostrar el mensaje de que se ha borrado o agregado correctamente al operador    -->
            <!-- <div v-if="$page.props.flash.message" class="p-4 mb-4 text-sm rounded-lg" role="alert"
                :class="`text-${$page.props.flash.color}-700 bg-${$page.props.flash.color}-100 dark:bg-${$page.props.flash.color}-200 dark:text-${$page.props.flash.color}-800`">
                <span class="font-medium">
                    {{ $page.props.flash.message }}
                </span>
            </div> -->

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Operador
                </button>
                <button id="editarABtn" disabled
                    class="bg-yellow-500 hover:bg-yellow-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarAlumnos">
                    <i class="fa fa-pencil" aria-hidden="true"></i> Editar Operador
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarAlumnos">
                    <i class="fa fa-trash mr-2"></i> Eliminar Operador
                </button>
            </div>

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
                        }, buttons: [botones],
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
                                Tipo de operador
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Estado
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Jefe
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <formulario-operadores :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir operador'" :op="'1'" :modal="'modalCreate'" :operador="props.operador"
            :tipoOperador="props.tipoOperador" :estado="props.estado" :directivo="props.directivo"></formulario-operadores>
        <formulario-operadores :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable" @close="cerrarModalE"
            :title="'Editar operador'" :op="'2'" :modal="'modalEdit'" :operador="props.operador"
            :tipoOperador="props.tipoOperador" :estado="props.estado" :directivo="props.directivo"></formulario-operadores>
    </PrincipalLayout>
</template>