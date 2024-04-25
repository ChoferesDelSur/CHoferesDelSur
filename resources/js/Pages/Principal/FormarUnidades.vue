<script setup>
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import pdfmake from 'pdfmake';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5.mjs';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import jsZip from 'jszip';

// Variables e inicializaciones necesarias para el datatable y el uso de generacion de 
// documentos
window.JSZip = jsZip;
DataTable.use(DataTablesLib);
DataTable.use(Select);
DataTable.use(pdfmake);
DataTable.use(ButtonsHtml5);

const props = defineProps({
  message: { String, default: '' },
  color: { String, default: '' },
  ruta: { type: Object },
  unidad: { type: Object },
  operador: { type: Object }, 
  formacionUnidades: {type: Object},
});

console.log("Unidades:");
console.log(props.unidad);
console.log("Rutas");
console.log(props.ruta)

const botones = [
    {
        title: 'Unidades Registrados',
        extend: 'excelHtml5',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
        },
    },
    {
        title: 'Unidades Registrados',
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
  /*   /* {
        title: 'Unidades registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir',
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
        },
        orientation: 'landscape',
    }, 
    {
        title: 'Unidades registrados',
        extend: 'copy',
        text: '<i class="fa-solid fa-copy"></i> Copiar Texto',
        className: 'bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
        }
    }, */
]

const columnas = [
    {
        /* data: null, */
        render: function (data, type, row, meta) {
            return "";
        }
    },
    {
        /* data: null, */
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="formacion-checkboxes" data-id="${row.idFormacionUnidades}" ">`;
        }
    },
    {
        /* data: null,  */
        render: function (data, type, row, meta) { 
          return meta.row + 1 }
    },
    {
        data: 'idUnidad',
        render: function (data, type, row, meta) {
            const carro = props.unidad.find(carro => carro.idUnidad === data);
            return carro ? carro.idRuta : '';
        }
    },
    {
        data: 'idUnidad',
        render: function (data, type, row, meta) {
            const carro = props.unidad.find(carro => carro.idUnidad === data);
            return carro ? carro.numeroUnidad : '';
        }
    },
]


console.log("Estoy en Formar Unidades");

</script>

<template>
  <PrincipalLayout title="FormarUnidades">
    <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
      <h2 class="font-bold text-center text-xl pt-5"> Formar Unidades</h2>
      <div class="my-1"></div> <!-- Espacio de separación -->
      <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

      <div class="overflow-x-auto"> <!-- el overflow-x-auto - es para poner la barra de dezplazamiento en horizontal automático -->
        <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
          id="formacionTablaId" name="formacionTablaId" :columns="columnas" :data="formacionUnidades" :options="{
            responsive: true, autoWidth: false, dom: 'Bftrip', language: {
              search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
              info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
              infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
              infoFiltered: '(filtrado de un total de _MAX_ registros)',
              lengthMenu: 'Mostrar _MENU_ registros',
              paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
            },buttons: [botones],
          }">
          <thead>
            <tr class="text-sm leading-normal border-b border-gray-300">
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th> <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" ></th> <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" ></th> <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th> <!-- Ruta -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th> <!-- Unidad -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th><!-- Socio/Prestador -->
              <th class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" colspan="2">ENTRADA</th>
              <th class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" colspan="3">CORTE</th> <!-- Columna combinada con título "Corte" -->
              <th class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" colspan="4">CASTIGO</th> <!-- Columna combinada con título "Corte" -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th> <!-- operador -->
            </tr>
            <tr class="text-sm leading-normal border-b border-gray-300" >
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                ID
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Ruta
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Unidad
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Socio / Prestador
              </th>
              <th
                class="py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora entrada
              </th>
              <th
                class="py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Tipo de entrada
              </th>
              <th
                class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de Corte
              </th>
              <th
                class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Causa
              </th>
              <th
                class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de regreso
              </th>
              <th
                class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de inicio
              </th>
              <th
                class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora fin
              </th>
              <th
                class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Castigo
              </th>
              <th
                class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Otras observaciones
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Operador
              </th>
            </tr>
          </thead>
        </DataTable>
      </div>

    </div>
  </PrincipalLayout>
</template>