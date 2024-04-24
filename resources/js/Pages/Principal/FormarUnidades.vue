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

const props = defineProps({
  message: { String, default: '' },
  color: { String, default: '' },
});

const columnas = [
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  }
]


console.log("Estoy en Formar Unidades");

</script>

<template>
  <PrincipalLayout title="FormarUnidades">
    <div class="mt-8 bg-white p-4 shadow rounded-lg h-5/6">
      <h2 class="font-bold text-center text-xl pt-5"> Formar Unidades</h2>
      <div class="my-1"></div> <!-- Espacio de separación -->
      <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

      <!-- <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
        <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-plus mr-2"></i>Agregar Operador
        </button>
        <button id="editarABtn" disabled
          class="bg-yellow-500 hover:bg-yellow-500 text-white font-semibold py-2 px-4 rounded" @click="eliminarAlumnos">
          <i class="fa fa-pencil" aria-hidden="true"></i> Editar Operador
        </button>
        <button id="eliminarABtn" disabled
          class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded" @click="eliminarAlumnos">
          <i class="fa fa-trash mr-2"></i> Eliminar Operador
        </button>
      </div> -->

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
            <tr class="text-sm leading-normal border-b border-gray-300">
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th> <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" ></th> <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th> <!-- Ruta -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th> <!-- Unidad -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th><!-- Socio/Prestador -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" colspan="3">ENTRADA</th>
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300" colspan="3">CORTE</th> <!-- Columna combinada con título "Corte" -->
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
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora entrada
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Tipo de entrada
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de Corte
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Causa
              </th>
              <th
                class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de regreso
              </th>
            </tr>
          </thead>
        </DataTable>
      </div>

    </div>
  </PrincipalLayout>
</template>