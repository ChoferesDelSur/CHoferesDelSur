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
import FormularioSP from '../../Components/Principal/FormularioSP.vue';

window.JSZip = jsZip;

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
    directivo: { type: Object },
    tipDirectivo: { type: Object },
});

const botones = [
    {
        title: 'Directivos Registrados',
        extend: 'excelHtml5',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
        },
    },
    {
        title: 'Directivos Registrados',
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
        title: 'Directivos Registrados',
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
    /*     {
            data: 'idOperador',
            render: function (data, type, row, meta) {
                // Modificación para mostrar la descripción del ciclo
                const chofer = props.operador.find(chofer => chofer.idOperador === data);
                return chofer ? chofer.nombre : '';
            }
        }, */
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

</script>

<template>
    <PrincipalLayout title="Directivos">
        <div class="mt-8 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-5">Directivos</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Socio/Prestador
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarAlumnos">
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
                                Tipo Directivo
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioSP :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir directivo'" :op="'1'" :modal="'modalCreate'" :tipDirectivo="props.tipDirectivo"
            :directivo="props.directivo"></FormularioSP>
        <FormularioSP :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable" @close="cerrarModalE"
            :title="'Editar directivo'" :op="'2'" :modal="'modalEdit'" :tipDirectivo="props.tipDirectivo"
            :directivo="props.directivo"></FormularioSP>
    </PrincipalLayout>
</template>