<script setup>
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import pdfmake from 'pdfmake';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5.mjs';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import jsZip from 'jszip';
import { ref, onMounted } from 'vue';
import FormularioRegHoraEntrada from '../../Components/Principal/FormularioRegHoraEntrada.vue';

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
  directivo: { type: Object },
  formacionUnidades: { type: Object },
});

console.log("Unidades:");
console.log(props.unidad);
console.log("Formacion Unidades");
console.log(props.formacionUnidades)

// Dentro del bloque <script setup>
const fechaActual = new Date().toLocaleDateString(); // Obtiene la fecha actual en formato de cadena
const diaSemana = obtenerDiaSemana(new Date().getDay()); // Obtiene el día de la semana actual
// Función para obtener el día de la semana según el número
function obtenerDiaSemana(diaNumero) {
  const diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
  return diasSemana[diaNumero];
}

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
    data: null,
    render: function (data, type, row, meta) {
      return `<input type="checkbox" class="formacion-checkboxes" data-id="${row.idFormacionUnidades}" ">`;
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return meta.row + 1
    }
  },
  {
    data: 'idUnidad',
    render: function (data, type, row, meta) {
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const ruta = props.ruta.find(ruta => ruta.idRuta === unidad.idRuta);
        return ruta ? ruta.nombreRuta : '';
      } else {
        return '';
      }
    }
  },
  {
    data: 'idUnidad',
    render: function (data, type, row, meta) {
      const carro = props.unidad.find(carro => carro.idUnidad === data);
      return carro ? carro.numeroUnidad : '';
    }
  },
  {
    data: 'idUnidad',
    render: function (data, type, row, meta) {
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const jefe = props.directivo.find(jefe => jefe.idDirectivo === unidad.idDirectivo);
        return jefe ? jefe.nombre_completo : '';
      } else {
        return '';
      }
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      const hEntrada = props.formacionUnidades.find(hEntrada => hEntrada === data);
      if (hEntrada && hEntrada.horaEntrada) { // Verificar si hEntrada.horaEntrada no es nulo
        return hEntrada.horaEntrada.substring(0, 5); // Obtener solo la hora y los minutos
      } else {
        return ''; // Devolver una cadena vacía si el valor es nulo
      }
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      const hEntrada = props.formacionUnidades.find(hEntrada => hEntrada === data);
        if (hEntrada && hEntrada.horaEntrada) {
            // Obtener el día de la semana
            var fecha = new Date(hEntrada.fecha);
            var diaSemana = fecha.getDay();

            // Obtener solo la hora y los minutos
            var horaEntrada = hEntrada.horaEntrada.substring(0, 5);

            // Convertir la hora de entrada a un objeto de tipo Date
            var horaEntradaDate = new Date("01/01/2000 " + horaEntrada);

            // Definir los límites de tiempo según el día de la semana
            var limiteNormal, limiteMulta;

            if (diaSemana >= 1 && diaSemana <= 5) { // Lunes a viernes
                limiteNormal = new Date("01/01/2000 06:15");
                limiteMulta = new Date("01/01/2000 06:30");
            } else if (diaSemana === 6) { // Sábado
                limiteNormal = new Date("01/01/2000 06:30");
                limiteMulta = new Date("01/01/2000 07:00");
            } else if (diaSemana === 0) { // Domingo
                limiteNormal = new Date("01/01/2000 07:30");
                limiteMulta = new Date("01/01/2000 07:45");
            }

            // Verificar el tipo de entrada
            if (horaEntradaDate < limiteNormal) {
                return 'Normal';
            } else if (horaEntradaDate >= limiteNormal && horaEntradaDate <= limiteMulta) {
                return 'Multa';
            } else {
                return '';
            }
        } else {
            return '';
        }
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  },
  {
    data: null,
    render: function (data, type, row, meta) {
      return "";
    }
  },
  {
    data: 'idUnidad',
    render: function (data, type, row, meta) {
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const chofer = props.operador.find(chofer => chofer.idOperador === unidad.idOperador);
        return chofer ? chofer.nombre_completo : '';
      } else {
        return '';
      }
    }
  },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({});

var formacionE = ({});
const abrirE = ($formacioness) => {
  formacionE = $formacioness;
  mostrarModalE.value = true;
}

const cerrarModal = () => {
  mostrarModal.value = false;
};

const cerrarModalE = () => {
  mostrarModalE.value = false;
};


console.log("Estoy en Formar Unidades");

</script>

<template>
  <PrincipalLayout title="Formar Unidades">
    <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
      <h2 class="font-bold text-center text-xl pt-5"> Formar Unidades</h2>
      <div class="my-1"></div> <!-- Espacio de separación -->
      <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

      <div class="py-1 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
        <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-check-circle" aria-hidden="true"></i> Registrar entrada
        </button>
        <button id="eliminarABtn" disabled
          class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded" @click="eliminarOperadores">
          <i class="fa fa-scissors" aria-hidden="true"></i> Registrar Corte
        </button>
        <button id="editarABtn" disabled
          class="bg-yellow-500 hover:bg-yellow-500 text-white font-semibold py-2 px-4 rounded" @click="eliminarAlumnos">
          <i class="fa fa-bullhorn" aria-hidden="true"></i> Registrar Castigo
        </button>
      </div>

      <div class="overflow-x-auto">
        <!-- el overflow-x-auto - es para poner la barra de dezplazamiento en horizontal automático -->
        <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
          id="formacionTablaId" name="formacionTablaId" :columns="columnas" :data="formacionUnidades" :options="{
            responsive: false, autoWidth: false, dom: 'Bftrip', language: {
              search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
              info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
              infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
              infoFiltered: '(filtrado de un total de _MAX_ registros)',
              lengthMenu: 'Mostrar _MENU_ registros',
              paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
            }, buttons: [botones],
            /* editable: true, // Habilitar la edición
            editField: 'edit', // Campo que indica si una celda está en modo de edición */
          }">
          <thead>
            <tr class="text-sm leading-normal border-b border-gray-300">

              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th>
              <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="2">FECHA: {{ diaSemana + ', ' + fechaActual }}</th>
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th>
              <!-- Unidad -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th>
              <!-- Socio/Prestador -->
              <th class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="2">ENTRADA</th>
              <th class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="3">CORTE</th> <!-- Columna combinada con título "Corte" -->
              <th class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="4">CASTIGO</th> <!-- Columna combinada con título "Corte" -->
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"></th>
              <!-- operador -->
            </tr>
            <tr class="text-sm leading-normal border-b border-gray-300">

              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th>
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                ID
              </th>
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Ruta
              </th>
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Unidad
              </th>
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Socio / Prestador
              </th>
              <th class="py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora entrada
              </th>
              <th class="py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Tipo de entrada
              </th>
              <th class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de Corte
              </th>
              <th class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Causa
              </th>
              <th class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de regreso
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora de inicio
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hora fin
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Castigo
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Otras observaciones
              </th>
              <th class="py-2 px-4 bg-grey-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Operador
              </th>
            </tr>
          </thead>
        </DataTable>
      </div>
    </div>
    <FormularioRegHoraEntrada :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
      :title="'Registrar hora de entrada'" :op="'1'" :modal="'modalCreate'" :formacionUnidades="props.formacionUnidades"
      :unidad="props.unidad">
    </FormularioRegHoraEntrada>
  </PrincipalLayout>
</template>