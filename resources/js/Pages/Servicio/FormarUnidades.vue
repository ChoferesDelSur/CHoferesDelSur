<script setup>
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import { ref, onMounted, computed, nextTick } from 'vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import Mensaje from '../../Components/Mensaje.vue';
import { jsPDF } from 'jspdf';
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';
import ServicioLayout from '../../Layouts/ServicioLayout.vue';
import FormularioRegHoraEntrada from '../../Components/Servicio/FormularioRegHoraEntrada.vue';
import FormularioRegCorte from '../../Components/Servicio/FormularioRegCorte.vue';
import FormularioRegRegreso from '../../Components/Servicio/FormularioRegRegreso.vue';
import FormularioCastigo from '../../Components/Servicio/FormularioCastigo.vue';
import FormularioRegUC from '../../Components/Servicio/FormularioRegUC.vue';
import FormularioRegresoUC from '../../Components/Servicio/FormularioRegresoUC.vue';
import FormularioDomingo from '../../Components/Servicio/FormularioDomingo.vue';
import FormularioFinCastigo from '../../Components/Servicio/FormularioFinCastigo.vue';
import FormularioEditarRegistro from '../../Components/Servicio/FormularioEditarRegistro.vue';
import FormularioEliminarRegistro from '../../Components/Servicio/FormularioEliminarRegistro.vue';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
  message: { String, default: '' },
  color: { String, default: '' },
  ruta: { type: Object },
  unidad: { type: Object },
  unidadesConOperador: { type: Object },
  operador: { type: Object },
  directivo: { type: Object },
  rolServicio: { type: Object },
  castigo: { type: Object },
  entrada: { type: Object },
  corte: { type: Object },
  ultimaCorrida: { type: Object },
  tipoUltimaCorrida: { type: Object },
  usuario: { type: Object },
});

// Función para obtener el número de la semana
function obtenerNumeroSemana(fecha) {
  const fechaActual = new Date(fecha);
  const diaLunes = new Date(fechaActual.setDate(fechaActual.getDate() - fechaActual.getDay() + 1));
  const inicioAnio = new Date(fechaActual.getFullYear(), 0, 1);
  const diasTranscurridos = Math.floor((diaLunes - inicioAnio) / (24 * 60 * 60 * 1000));
  return Math.ceil((diasTranscurridos + inicioAnio.getDay() + 1) / 7);
}

// Obtener la fecha actual y el número de semana
const fechaActual = new Date().toLocaleDateString();
const diaSemana = obtenerDiaSemana(new Date().getDay());
const numeroSemanaActual = obtenerNumeroSemana(new Date());

// Función para obtener el día de la semana según el número
function obtenerDiaSemana(diaNumero) {
  const diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
  return diasSemana[diaNumero];
}

const exportarExcel = () => {
  nextTick(() => {
    const table = $('#formacionTablaId').DataTable();
    const data = table.rows({ search: 'applied', page: 'current' }).data(); // Obtiene solo los datos filtrados y en la página actual

    if (!data.length) {
      console.warn('No hay datos para exportar.');
      return;
    }

    // Obtener la fecha actual en formato YYYY-MM-DD
    const today = new Date().toISOString().split('T')[0];

    // Crear mapas para optimizar las búsquedas
    const rutasMap = Object.fromEntries(props.ruta.map(r => [r.idRuta, r.nombreRuta]));
    const rolServicioMap = Object.fromEntries(props.rolServicio.map(rol => [rol.idUnidad, rol.trabajaDomingo]));
    const unidadMap = Object.fromEntries(props.unidad.map(u => [u.idUnidad, u.numeroUnidad]));
    const directivoMap = Object.fromEntries(props.directivo.map(d => [d.idDirectivo, d.nombre_completo]));
    const entradaMap = Object.fromEntries(props.entrada.map(e => [e.idUnidad, { horaEntrada: e.horaEntrada, tipoEntrada: e.tipoEntrada, extremo: e.extremo, created_at: e.created_at }]));
    const corteMap = Object.fromEntries(props.corte.map(c => [c.idUnidad, []]));
    const ultimaCorridaMap = Object.fromEntries(props.ultimaCorrida.map(uc => [uc.idUnidad, { tipoCorrida: uc.idTipoUltimaCorrida, horaInicioUC: uc.horaInicioUC, horaFinUC: uc.horaFinUC, created_at: uc.created_at }]));
    const castigoMap = Object.fromEntries(props.castigo.map(c => [c.idUnidad, []])); // Cambiado a lista de castigos
    const operadorMap = Object.fromEntries(props.operador.map(o => [o.idOperador, o.nombre_completo]));
    const tipoUltimaCorridaMap = Object.fromEntries(props.tipoUltimaCorrida.map(tuc => [tuc.idTipoUltimaCorrida, tuc.tipoUltimaCorrida]));

    // Función para formatear hora a "HH:mm"
    const formatTime = (time) => {
      if (!time) return '';
      const [hours, minutes] = time.split(':');
      return `${hours}:${minutes}`;
    };

    // Agrupar los datos por unidad
    const groupedData = data.toArray().reduce((acc, row) => {
      const entrada = entradaMap[row.idUnidad] || {};
      const corte = props.corte.filter(c => c.idUnidad === row.idUnidad && c.created_at.split('T')[0] === today);
      const ultimaCorrida = ultimaCorridaMap[row.idUnidad];
      const castigos = props.castigo.filter(c => c.idUnidad === row.idUnidad && c.created_at.split('T')[0] === today);
      const rol = rolServicioMap[row.idUnidad] || 'Sin Asignar';

      const isEntradaValid = entrada.created_at?.split('T')[0] === today;
      const isUltimaCorridaValid = ultimaCorrida?.created_at?.split('T')[0] === today; // Verifica que la fecha sea hoy
      const isCastigoValid = castigos.length > 0;

      // Inicializa el objeto para la unidad si no existe
      if (!acc[row.idUnidad]) {
        acc[row.idUnidad] = {
          ID: row.idUnidad,
          'Ruta': rutasMap[row.idRuta] || 'N/A',
          'Trab. Domingo': rol || 'Sin Asignar',
          'Unidad': unidadMap[row.idUnidad] || '',
          'Socio/Prestador': directivoMap[row.idDirectivo] || '',
          'Hr. Entrada': isEntradaValid ? formatTime(entrada.horaEntrada) : '',
          'Tipo Entrada': isEntradaValid ? entrada.tipoEntrada : '',
          'Extremo': isEntradaValid ? entrada.extremo : '',
          'Hr. Corte': '',
          'Causa': '',
          'Hr. Regreso': '',
          'Tipo De Corrida': isUltimaCorridaValid ? (tipoUltimaCorridaMap[ultimaCorrida.tipoCorrida] || '') : '',
          'Hr. Inicio UC': isUltimaCorridaValid ? formatTime(ultimaCorrida.horaInicioUC) : '',
          'Hr. Regreso UC': isUltimaCorridaValid ? formatTime(ultimaCorrida.horaFinUC) : '',
          'Hr. Inicio': '',
          'Hr. Finaliza': '',
          'Motivo': '',
          'Otras Observaciones': '',
          'Operador': operadorMap[row.idOperador] || 'Sin Asignar'
        };
      }

      // Concatenar los registros de corte si existen múltiples para la misma unidad
      corte.forEach(c => {
        acc[row.idUnidad]['Hr. Corte'] += (acc[row.idUnidad]['Hr. Corte'] ? ', ' : '') + formatTime(c.horaCorte);
        acc[row.idUnidad]['Causa'] += (acc[row.idUnidad]['Causa'] ? ', ' : '') + c.causa;
        acc[row.idUnidad]['Hr. Regreso'] += (acc[row.idUnidad]['Hr. Regreso'] ? ', ' : '') + formatTime(c.horaRegreso);
      });

      // Concatenar los registros de castigo si existen múltiples para la misma unidad
      castigos.forEach(c => {
        acc[row.idUnidad]['Hr. Inicio'] += (acc[row.idUnidad]['Hr. Inicio'] ? ', ' : '') + formatTime(c.horaInicio);
        acc[row.idUnidad]['Hr. Finaliza'] += (acc[row.idUnidad]['Hr. Finaliza'] ? ', ' : '') + formatTime(c.horaFin);
        acc[row.idUnidad]['Motivo'] += (acc[row.idUnidad]['Motivo'] ? ', ' : '') + c.castigo;
        acc[row.idUnidad]['Otras Observaciones'] += (acc[row.idUnidad]['Otras Observaciones'] ? ', ' : '') + c.observaciones;
      });

      return acc;
    }, {});

    // Convertir el objeto agrupado en un array para la exportación
    const jsonData = Object.values(groupedData);

    // Crear y exportar el archivo Excel
    const ws = XLSX.utils.json_to_sheet(jsonData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Formación De Unidades');
    const fechaArchivo = new Date().toISOString().split('T')[0];
    XLSX.writeFile(wb, `Formación_De_Unidades_${fechaArchivo}.xlsx`);
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
    title: 'Formación De Unidades',
    text: '<i class="fa-solid fa-file-excel"></i> Excel',
    className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
    action: () => exportarExcel() // Usa la función de exportar a Excel
  },
  {
    title: 'Formación De Unidades',
    extend: 'print',
    text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
    className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18] // Índices de las columnas que deseas imprimir (por ejemplo, imprimir las columnas 0 y 2)
    }
  }
];

const columnas = [
  {
    data: null,
    render: function (data, type, row, meta) {
      return `<span class="bg-sky-200 text-black py-1 px-2 rounded">${meta.row + 1}</span>`
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'RUTA',
    render: function (data, type, row, meta) {
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const ruta = props.ruta.find(ruta => ruta.idRuta === unidad.idRuta);
        return `<div class="bg-sky-200 text-black py-1 px-2 rounded">${ruta.nombreRuta}</div>`;
      } else {
        return '';
      }
    },
    visible: true
  },
  {
    data: "idUnidad",
    title: 'DOM',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de rolServicio para esta idUnidad
        const registrosUnidad = props.rolServicio.filter(rol => rol.idUnidad === unidad.idUnidad);
        if (registrosUnidad.length > 0) {
          // Ordenar los registros por fecha de created_at (más reciente primero)
          registrosUnidad.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
          // Devolver el valor de trabajaDomingo del registro más reciente
          return `<div class="bg-sky-200 text-black py-1 px-2 rounded">${registrosUnidad[0].trabajaDomingo}</div>`;
        } else {
          // Si no hay registros para esta idUnidad, devolver un valor por defecto
          return '<div class="bg-sky-200 text-red-600 py-1 px-2 rounded"><span>Sin asignar</span></div>';
        }
      } else {
        // Si no se encuentra la unidad, devolver un mensaje de error o un valor por defecto
        return 'Unidad no encontrada';
      }
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'UNIDAD',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      const carro = props.unidad.find(carro => carro.idUnidad === data);
      return `<div class="bg-sky-200 text-black py-1 px-2 rounded">${carro ? carro.numeroUnidad : ''}</div>`;
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'SOCIO/PRESTADOR',
    render: function (data, type, row, meta) {
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const jefe = props.directivo.find(jefe => jefe.idDirectivo === unidad.idDirectivo);
        return `<div class="bg-sky-200 text-black py-1 px-2 rounded">${jefe ? jefe.nombre_completo : ''}</div>`;
      } else {
        return '';
      }
    },
    visible: true
  },
  {
    data: "idUnidad",
    title: 'HORA ENTRADA',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de entrada correspondientes a la unidad y a la fecha actual
        const entradasHoy = props.entrada.filter(entrada => {
          return entrada.idUnidad === unidad.idUnidad &&
            new Date(entrada.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de entrada para la unidad y la fecha actual
        if (entradasHoy.length > 0) {
          // Construir una cadena con todas las horas de entrada para la unidad y la fecha actual
          const horasEntrada = entradasHoy.map(entrada => entrada.horaEntrada.substring(0, 5)).join(', ');
          return `<div class="bg-green-200 text-black py-1 px-2 rounded">${horasEntrada}</div>`;
        }
      }
      // Si no se encuentra la unidad o no hay registros de entrada para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: "idUnidad",
    title: 'TIPO ENTRADA',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de entrada correspondientes a la unidad y a la fecha actual
        const entradasHoy = props.entrada.filter(entrada => {
          return entrada.idUnidad === unidad.idUnidad &&
            new Date(entrada.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de entrada para la unidad y la fecha actual
        if (entradasHoy.length > 0) {
          // Construir una cadena con todos los tipos de entrada para la unidad y la fecha actual
          const tiposEntrada = entradasHoy.map(entrada => entrada.tipoEntrada).join(', ');
          return `<div class="bg-green-200 text-black py-1 px-2 rounded">${tiposEntrada}</div>`;
        }
      }
      // Si no se encuentra la unidad o no hay registros de entrada para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: "idUnidad",
    title: 'EXTREMO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de entrada correspondientes a la unidad y a la fecha actual
        const entradasHoy = props.entrada.filter(entrada => {
          return entrada.idUnidad === unidad.idUnidad &&
            new Date(entrada.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de entrada para la unidad y la fecha actual
        if (entradasHoy.length > 0) {
          // Construir una cadena con todos los valores de esExtremo para la unidad y la fecha actual
          const extremos = entradasHoy.map(entrada => entrada.extremo).join(', ');
          //return `<div class="bg-green-200 text-black py-1 px-2 rounded">${extremos}</div>`;
          return `<div class="bg-green-200 text-black py-1 px-2 rounded" style="width: 100%; height: 100%;">${extremos}</div>`;
        }
      }
      // Si no se encuentra la unidad o no hay registros de entrada para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'HORA CORTE',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de corte correspondientes a la unidad y a la fecha actual
        const cortesHoy = props.corte.filter(corte => {
          return corte.idUnidad === unidad.idUnidad &&
            new Date(corte.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de corte para la unidad y la fecha actual
        if (cortesHoy.length > 0) {
          // Construir una cadena con todas las horas de corte para la unidad y la fecha actual
          const horasCorteRows = cortesHoy.map((corte, index) => {
            // Renderizar cada hora de corte en una fila diferente con un borde inferior si hay más de una hora de corte
            const borderStyle = cortesHoy.length > 1 && index !== cortesHoy.length - 1 ? 'border-bottom: 1px solid #b2b2b2;' : '';
            return `
            <div style="${borderStyle}">
              <div>${corte.horaCorte.substring(0, 5)}</div>
            </div>
          `;
          }).join(''); // Unir las filas en una sola cadena para mostrar en la celda

          return `<div style="background-color: #f5c6cb; padding: 0.5rem;">${horasCorteRows}</div>`;
        }
      }
      // Si no se encuentra la unidad o no hay registros de corte para la fecha actual, devolver un valor por defecto
      return '';//return `<div style="background-color: #f5c6cb; padding: 0.5rem;"></div>`;
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'CAUSA',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de corte correspondientes a la unidad y a la fecha actual
        const cortesHoy = props.corte.filter(corte => {
          return corte.idUnidad === unidad.idUnidad &&
            new Date(corte.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de corte para la unidad y la fecha actual
        if (cortesHoy.length > 0) {
          // Crear un array de causas de corte
          const causasCorteRows = cortesHoy.map((corte, index) => {
            // Renderizar cada causa de corte en una fila diferente con un borde inferior si hay más de una causa de corte
            const borderStyle = cortesHoy.length > 1 && index !== cortesHoy.length - 1 ? 'border-bottom: 1px solid #b2b2b2;' : '';
            return `
            <div style="${borderStyle}">
              <div>${corte.causa}</div>
            </div>
          `;
          }).join(''); // Unir las filas en una sola cadena para mostrar en la celda

          return `<div style="background-color: #f5c6cb; padding: 0.5rem;">${causasCorteRows}</div>`;

        }
      }
      // Si no se encuentra la unidad o no hay registros de corte para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'HORA REGRESO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de corte correspondientes a la unidad y a la fecha actual
        const cortesHoy = props.corte.filter(corte => {
          return corte.idUnidad === unidad.idUnidad &&
            new Date(corte.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de corte para la unidad y la fecha actual
        if (cortesHoy.length > 0) {
          // Crear un array de horas de regreso
          const horasRegresoRows = cortesHoy.map((corte, index) => {
            // Obtener la hora de regreso del corte actual
            const horaRegreso = corte.horaRegreso ? corte.horaRegreso.substring(0, 5) : '';

            // Renderizar cada hora de regreso en una fila diferente con un borde inferior si hay más de una hora de regreso
            const borderStyle = cortesHoy.length > 1 && index !== cortesHoy.length - 1 ? 'border-bottom: 1px solid #b2b2b2;' : '';
            return `
            <div style="${borderStyle}">
              <div>${horaRegreso}</div>
            </div>
          `;
          }).join(''); // Unir las filas en una sola cadena para mostrar en la celda

          return `<div style="background-color: #f5c6cb; padding: 0.5rem;">${horasRegresoRows}</div>`;
        }
      }
      // Si no se encuentra la unidad o no hay registros de corte para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: "idUnidad",
    title: 'TIPO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de última corrida correspondientes a la unidad y a la fecha actual
        const ultimasCorridasHoy = props.ultimaCorrida.filter(corrida => {
          return corrida.idUnidad === unidad.idUnidad &&
            new Date(corrida.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de última corrida para la unidad y la fecha actual
        if (ultimasCorridasHoy.length > 0) {
          // Obtener el objeto de tipoUltimaCorrida asociado al idTipoUltimaCorrida
          const tipoUltimaCorrida = props.tipoUltimaCorrida.find(tipo => tipo.idTipoUltimaCorrida === ultimasCorridasHoy[0].idTipoUltimaCorrida);
          // Devolver el dato correspondiente a tipoUltimaCorrida
          if (tipoUltimaCorrida) {
            return `<div class="bg-purple-200 text-black py-1 px-2 rounded">${tipoUltimaCorrida.tipoUltimaCorrida}</div>`;
          }
        }
      }
      // Si no se encuentra la unidad o no hay registros de última corrida para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: "idUnidad",
    title: 'HORA INICIO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de última corrida correspondientes a la unidad y a la fecha actual
        const ultimasCorridasHoy = props.ultimaCorrida.filter(corrida => {
          return corrida.idUnidad === unidad.idUnidad &&
            new Date(corrida.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de última corrida para la unidad y la fecha actual
        if (ultimasCorridasHoy.length > 0) {
          // Devolver la hora de inicio de la última corrida del primer registro encontrado, si no es null
          const horaInicioUC = ultimasCorridasHoy[0].horaInicioUC;
          return `<div class="bg-purple-200 text-black py-1 px-2 rounded">${horaInicioUC ? horaInicioUC.substring(0, 5) : ''}</div>`;
        }
      }
      // Si no se encuentra la unidad o no hay registros de última corrida para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: "idUnidad",
    title: 'HORA REGRESO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidad
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Filtrar los registros de última corrida correspondientes a la unidad y a la fecha actual
        const ultimasCorridasHoy = props.ultimaCorrida.filter(corrida => {
          return corrida.idUnidad === unidad.idUnidad &&
            new Date(corrida.created_at).toLocaleDateString() === fechaActual;
        });
        // Verificar si hay registros de última corrida para la unidad y la fecha actual
        if (ultimasCorridasHoy.length > 0) {
          // Obtener la hora de fin de la última corrida, si existe
          const horaFinUC = ultimasCorridasHoy[0].horaFinUC;
          // Verificar si horaFinUC no es null
          if (horaFinUC) {
            // Devolver la hora de fin de la última corrida truncada a los primeros 5 caracteres
            return `<div class="bg-purple-200 text-black py-1 px-2 rounded">${horaFinUC.substring(0, 5)}</div>`;
          }
        }
      }
      // Si no se encuentra la unidad o no hay registros de última corrida para la fecha actual, devolver un valor por defecto
      return '';
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'HORA INICIO CASTIGO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Filtrar los castigos de la unidad correspondiente para la fecha actual
      const castigosUnidadHoy = props.castigo.filter(castigo =>
        castigo.idUnidad === data && new Date(castigo.created_at).toLocaleDateString() === fechaActual
      );

      if (castigosUnidadHoy.length > 0) {
        // Crear un array de objetos con la estructura necesaria para renderizar en filas separadas
        const castigosRows = castigosUnidadHoy.map(castigo => {
          return {
            horaInicio: castigo.horaInicio ? castigo.horaInicio.substring(0, 5) : '', // Obtener hora de inicio si existe
          };
        });

        // Devolver un array de filas con los datos de castigos
        return castigosRows.map((castigoRow, index) => {
          // Renderizar cada castigo en una fila diferente con un borde inferior si hay más de un castigo
          const borderStyle = castigosRows.length > 1 && index !== castigosRows.length - 1 ? 'border-bottom: 1px solid #b2b2b2;' : '';
          return `
          <div style="background-color: #fff3b0 ${borderStyle}">
            <div>${castigoRow.horaInicio}</div>
          </div>
        `;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'HORA FIN CASTIGO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Filtrar los castigos de la unidad correspondiente para la fecha actual
      const castigosUnidadHoy = props.castigo.filter(castigo =>
        castigo.idUnidad === data && new Date(castigo.created_at).toLocaleDateString() === fechaActual
      );

      if (castigosUnidadHoy.length > 0) {
        // Crear un array de objetos con la estructura necesaria para renderizar en filas separadas
        const castigosRows = castigosUnidadHoy.map(castigo => {
          return {
            horaFin: castigo.horaFin ? castigo.horaFin.substring(0, 5) : '', // Obtener hora de fin si existe
          };
        });

        // Devolver un array de filas con los datos de castigos
        return castigosRows.map((castigoRow, index) => {
          // Renderizar cada castigo en una fila diferente con un borde inferior si hay más de un castigo
          const borderStyle = castigosRows.length > 1 && index !== castigosRows.length - 1 ? 'border-bottom: 1px solid #b2b2b2;' : '';
          return `
          <div style="background-color: #fff3b0 ${borderStyle}">
            <div>${castigoRow.horaFin}</div>
          </div>
        `;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'MOTIVO',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Filtrar los castigos de la unidad correspondiente para la fecha actual
      const castigosUnidadHoy = props.castigo.filter(castigo =>
        castigo.idUnidad === data && new Date(castigo.created_at).toLocaleDateString() === fechaActual
      );

      if (castigosUnidadHoy.length > 0) {
        // Crear un array de nombres de castigos
        const nombresCastigos = castigosUnidadHoy.map(castigo => {
          return castigo.castigo || ''; // Obtener el nombre del castigo si existe
        });

        // Devolver un array de filas con los nombres de castigos
        return nombresCastigos.map((nombreCastigo, index) => {
          // Renderizar cada nombre de castigo en una fila diferente con un borde inferior si hay más de un nombre de castigo
          const borderStyle = nombresCastigos.length > 1 && index !== nombresCastigos.length - 1 ? 'border-bottom: 1px solid #b2b2b2;' : '';
          return `<div style="background-color: #fff3b0 ${borderStyle}">${nombreCastigo}</div>`;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'OBSERVACIONES',
    className: "text-center", // Clase que asegura el centrado
    render: function (data, type, row, meta) {
      // Filtrar los castigos de la unidad correspondiente para la fecha actual
      const castigosUnidadHoy = props.castigo.filter(castigo =>
        castigo.idUnidad === data && new Date(castigo.created_at).toLocaleDateString() === fechaActual
      );

      if (castigosUnidadHoy.length > 0) {
        // Crear un array de observaciones de castigos
        const observacionesCastigos = castigosUnidadHoy.map(castigo => {
          return castigo.observaciones || ''; // Obtener la observación del castigo si existe
        });

        // Devolver un array de filas con las observaciones de castigos
        return observacionesCastigos.map((observacion, index) => {
          // Renderizar cada observación en una fila diferente con un borde inferior si hay más de una observación de castigo
          const borderStyle = observacionesCastigos.length > 1 && index !== observacionesCastigos.length - 1 ? 'border-bottom: 1px solid #b2b2b2;' : '';
          return `<div style="background-color: #fff3b0 ${borderStyle}">${observacion}</div>`;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    },
    visible: true
  },
  {
    data: 'idUnidad',
    title: 'OPERADOR',
    render: function (data, type, row, meta) {
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const chofer = props.operador.find(chofer => chofer.idOperador === unidad.idOperador);
        if (chofer) {
          return `<div class="bg-sky-200 text-black py-1 px-2 rounded">${chofer.nombre_completo}</div>`;
        } else {
          return '<div class="bg-sky-200 text-red-600 py-1 px-2 rounded"><span>Sin asignar</span></div>'; // Aplica color rojo si no hay operador asignado
        }
      } else {
        return '';
      }
    },
    visible: true
  },
]

const mostrarModal = ref(false);
const mostrarModalCorte = ref(false);
const mostrarModalCastigo = ref(false);
const mostrarModalFinCastigo = ref(false);
const mostrarModalRegreso = ref(false);
const mostrarModalRegUC = ref(false);
const mostrarModalRegresoUC = ref(false);
const mostrarModalDomingo = ref(false);
const mostrarModalE = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({});

var formacionE = ({});
const abrirE = ($formacioness) => {
  formacionE = $formacioness;
  mostrarModalE.value = true;
}

const cerrarModalEditar = () => {
  mostrarModalEditar.value = false;
}

const cerrarModalEliminar = () => {
  mostrarModalEliminar.value = false;
}

const cerrarModal = () => {
  mostrarModal.value = false;
};

const cerrarModalCorte = () => {
  mostrarModalCorte.value = false;
};

const cerrarModalCastigo = () => {
  mostrarModalCastigo.value = false;
};

const cerrarModalFinCastigo = () => {
  mostrarModalFinCastigo.value = false;
}

const cerrarModalRegreso = () => {
  mostrarModalRegreso.value = false;
};

const cerrarModalUC = () => {
  mostrarModalRegUC.value = false;
};

const cerrarModalRegresoUC = () => {
  mostrarModalRegresoUC.value = false;
};

const cerrarModalDomingo = () => {
  mostrarModalDomingo.value = false;
};

const cerrarModalE = () => {
  mostrarModalE.value = false;
};

const actualizarRolServicio = () => {
  Swal.fire({
    title: '¿Seguro de actualizar el Rol de Domingo?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, Actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Si el usuario confirma, ejecuta la acción de Inertia
      Inertia.post(route('servicio.cambiarRolServicio'), {}, {
        onError: (errors) => {
          // Si ocurre un error, muestra una alerta de error
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al actualizar el rol de servicio',
            confirmButtonText: 'OK'
          });
          console.error('Error al actualizar el rol de servicio:', errors);
        }
      });
    }
  });
};

const actualizarRolRuta = () => {
  Swal.fire({
    title: '¿Seguro de actualizar el Rol de Ruta?',
    text: 'Las unidades que están en la ruta "LIBRAMIENTO - PLAZA DEL VALLE" se cambiarán a la ruta "ESMERALDA - COL. JARDIN" y viceversa',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, Actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Si el usuario confirma, ejecuta la acción de Inertia con _method: 'PUT'
      Inertia.post(route('servicio.actualizarRolRuta'), {
        _method: 'PUT'  // Esto disfrazará la solicitud POST como PUT
      }, {
        onError: (errors) => {
          // Si ocurre un error, muestra una alerta de error
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al actualizar el rol de rutas',
            confirmButtonText: 'OK'
          });
          console.error('Error al actualizar el rol de rutas:', errors);
        }
      });
    }
  });
};

const contarRegistrosHrEnt = () => {
  return props.entrada.filter(entrada => {
    // Comprobar si la entrada tiene 'horaEntrada' y si la fecha coincide con 'fechaActual'
    return entrada.horaEntrada && new Date(entrada.created_at).toLocaleDateString() === fechaActual;
  }).length;
};

// Configuración de opciones para DataTable
const dataTableOptions = {
  responsive: false,
  autoWidth: false,
  dom: 'Bftrip',
  language: {
    search: 'Buscar',
    zeroRecords: 'No hay registros para mostrar',
    info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
    infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
    infoFiltered: '(filtrado de un total de _MAX_ registros)',
  },
  buttons: [botonesPersonalizados],
  paging: false, // Esto es para quitar la paginación
};

const col = ref([]);
// Computed para filtrar columnas visibles
const filteredColumnas = computed(() => columnas.filter(col => col.visible));

// Función para actualizar la tabla
const updateTable = () => {
  nextTick(() => {
    const table = $('#formacionTablaId').DataTable();

    // Actualiza la visibilidad de las columnas según el estado de `visible`
    filteredColumnas.value.forEach((col, index) => {
      table.column(index).visible(col.visible);
    });

    // Recarga la tabla con los datos actuales
    table.ajax.reload();
  });
};


</script>

<template>
  <ServicioLayout title="Formar Unidades" :usuario="props.usuario">
    <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6 ">
      <h3 class="font-bold text-center text-xl pt-0"> Formar Unidades</h3>
      <!-- Aquí agregamos la fecha y número de semana, fuera de la tabla -->
      <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-1.5"></div>

      <Mensaje />

      <div class="py-0 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0 mb-2">
        <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-check-circle" aria-hidden="true"></i> Registrar Entrada
        </button>
        <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalCorte = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-scissors" aria-hidden="true"></i> Registrar Corte
        </button>

        <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalRegreso = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-history" aria-hidden="true"></i> Registrar Regreso
        </button>

        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalCastigo = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-bullhorn" aria-hidden="true"></i> Registrar castigo
        </button>

        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalFinCastigo = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-bullhorn" aria-hidden="true"></i> Registrar Fin Castigo
        </button>

        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalRegUC = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Registrar UC
        </button>

        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalRegresoUC = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Regreso UC
        </button>
      </div>

      <div class="py-1 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0 mb-1">

        <button class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalDomingo = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-calendar" aria-hidden="true"></i> Rol Domingo
        </button>

        <button class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded"
          @click="actualizarRolServicio"> <i class="fa fa-calendar" aria-hidden="true"></i> Actualizar Rol Servicio
        </button>

        <button class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2 px-4 rounded"
          @click="actualizarRolRuta"> <i class="fa fa-map" aria-hidden="true"></i> Actualizar Rol Rutas
        </button>

        <button class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalEditar = true" data-bs-toggle="modal" data-bs-target="#modalCreate"> <i
            class="fa fa-pencil" aria-hidden="true"></i> Editar Registro
        </button>

        <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalEliminar = true" data-bs-toggle="modal" data-bs-target="#modalCreate"> <i
            class="fa fa-trash" aria-hidden="true"></i> Eliminar Registro
        </button>

      </div>

      <div class="overflow-x-auto">
        <!-- Texto de total de formaciones-->
        <h1 class="text-center">Formaciones registradas: <strong>{{ contarRegistrosHrEnt() }}</strong></h1>

        <!-- Aquí agregamos la fecha y número de semana, fuera de la tabla -->
        <div class="py-2 text-center text-sm font-semibold bg-gray-100 rounded mb-2">
          <span>FECHA: {{ diaSemana + ', ' + fechaActual }} -- SEMANA: {{ numeroSemanaActual }}</span>
        </div>

        <div class="overflow-x-auto py-2">
          <!-- Checkbox para controlar columnas -->
          <div class="checkbox-container">
            <label v-for="(col, index) in filteredColumnas" :key="index" class="checkbox-label">
              <input type="checkbox" v-model="col.visible" @change="updateTable" />
              {{ col.title }}
            </label>
          </div>
        </div>

        <!-- el overflow-x-auto - es para poner la barra de dezplazamiento en horizontal automático -->
        <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
          id="formacionTablaId" name="formacionTablaId" :columns="filteredColumnas" :data="unidad"
          :options="dataTableOptions">
          <thead>
            <!--Viene titulos de las columnas y datos-->
            <tr class="text-sm leading-normal border-b border-gray-300">
              <th v-for="col in filteredColumnas" :key="col.title" v-if="col.visible" :class="col.class"
                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                {{ col.title }}
                <!-- Input de filtro para cada columna -->
                <div>
                  <input type="text" v-model="filters[columna.key]" placeholder="Filtrar"
                    class="mt-1 p-1 text-xs w-full border rounded" />
                </div>
              </th>
            </tr>
          </thead>
        </DataTable>
      </div>
    </div>
    <FormularioRegHoraEntrada :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
      :title="'Registrar hora de entrada'" :op="'1'" :modal="'modalCreate'" :entrada="props.entrada"
      :unidad="props.unidad" :unidadesConOperador="props.unidadesConOperador" :ruta="props.ruta"
      :operador="props.operador">
    </FormularioRegHoraEntrada>
    <FormularioRegCorte :show="mostrarModalCorte" :max-width="maxWidth" :closeable="closeable" @close="cerrarModalCorte"
      :title="'Registrar hora de corte'" :op="'1'" :modal="'modalCreate'" :corte="props.corte" :unidad="props.unidad"
      :unidadesConOperador="props.unidadesConOperador">
    </FormularioRegCorte>
    <FormularioRegRegreso :show="mostrarModalRegreso" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalRegreso" :title="'Registrar hora de regreso de corte'" :op="'1'" :modal="'modalCreate'"
      :unidad="props.unidad" :unidadesConOperador="props.unidadesConOperador" :corte="props.corte">
    </FormularioRegRegreso>
    <FormularioCastigo :show="mostrarModalCastigo" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalCastigo" :title="'Registrar un castigo'" :op="'1'" :modal="'modalCreate'"
      :unidad="props.unidad" :unidadesConOperador="props.unidadesConOperador" :castigo="props.castigo">
    </FormularioCastigo>
    <FormularioFinCastigo :show="mostrarModalFinCastigo" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalFinCastigo" :title="'Registrar Fin De Castigo'" :op="'1'" :modal="'modalCreate'"
      :unidad="props.unidad" :unidadesConOperador="props.unidadesConOperador" :castigo="props.castigo">
    </FormularioFinCastigo>
    <FormularioRegUC :show="mostrarModalRegUC" :max-width="maxWidth" :closeable="closeable" @close="cerrarModalUC"
      :title="'Registrar última corrida'" :op="'1'" :modal="'modalCreate'" :ultimaCorrida="props.ultimaCorrida"
      :tipoUltimaCorrida="props.tipoUltimaCorrida" :unidad="props.unidad">
    </FormularioRegUC>
    <FormularioRegresoUC :show="mostrarModalRegresoUC" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalRegresoUC" :title="'Registrar hora de regreso de última corrida'" :op="'1'"
      :modal="'modalCreate'" :ultimaCorrida="props.ultimaCorrida" :unidad="props.unidad">
    </FormularioRegresoUC>
    <FormularioDomingo :show="mostrarModalDomingo" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalDomingo" :title="'Registrar las unidades que trabajaran domingo'" :op="'1'"
      :modal="'modalCreate'" :rolServicio="props.rolServicio" :unidad="props.unidad">
    </FormularioDomingo>
    <FormularioEditarRegistro :show="mostrarModalEditar" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalEditar" :title="'Editar Registro'" :op="'1'" :modal="'modalCreate'"
      :unidadesConOperador="props.unidadesConOperador" :entrada="props.entrada" :corte="props.corte"
      :castigo="props.castigo" :ultimaCorrida="props.ultimaCorrida" :tipoUltimaCorrida="props.tipoUltimaCorrida">
    </FormularioEditarRegistro>
    <FormularioEliminarRegistro :show="mostrarModalEliminar" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalEliminar" :title="'Eliminar Registro'" :op="'1'" :modal="'modalCreate'"
      :unidadesConOperador="props.unidadesConOperador" :entrada="props.entrada" :corte="props.corte"
      :castigo="props.castigo" :ultimaCorrida="props.ultimaCorrida" :tipoUltimaCorrida="props.tipoUltimaCorrida">
    </FormularioEliminarRegistro>

  </ServicioLayout>
</template>

<style>
/* Estilo personalizado para centrar el texto en las celdas de la tabla */
#formacionTablaId th {
  text-align: center !important;
}

/* Asegúrate de que las celdas de la tabla tengan el fondo cubriendo toda la celda */
#formacionTablaId td {
  padding: 0 !important;
  /* Elimina cualquier padding extra que podría estar limitando el fondo */
  border: 1px solid #e5e7eb;
  /* Mantiene los bordes visibles */
  vertical-align: middle;
  /* Alinea el contenido verticalmente */
}

#formacionTablaId td.bg-green-200 {
  background-color: #c6f6d5 !important;
  /* Aplica el color verde */
  padding: 4px 8px !important;
  /* Asegúrate de que haya suficiente padding */
}

/* Aplica padding directamente al div de contenido para evitar conflictos con el padding de la celda */
#formacionTablaId td div {
  padding: 4px 8px;
  /* Controla el padding del contenido */
  width: 100%;
  /* Asegúrate de que el contenido ocupe todo el espacio */
  height: 100%;
  /* Asegúrate de que el contenido se extienda hasta el fondo de la celda */
  box-sizing: border-box;
  /* Asegura que el padding no afecte el tamaño del contenido */
}

/* Si quieres que el texto en las celdas se centre */
#formacionTablaId td.text-center {
  text-align: center;
  vertical-align: middle;
  /* Alinea el contenido verticalmente */
}

/* Estilo adicional para la columna Hora Corte, asegurando que las celdas con varias horas de corte se vean bien */
#formacionTablaId td .hora-corte {
  background-color: #f5c6cb;
  padding: 0.5rem;
  display: block;
  /* Asegura que cada hora de corte se muestre en una línea separada */
}

/* Si la celda tiene más de un corte, asegurarse de que las horas de corte se muestren correctamente */
#formacionTablaId td .hora-corte div {
  border-bottom: 1px solid #b2b2b2;
  padding: 4px 0;
  /* Asegura separación entre las horas de corte */
}

/* Añadir un poco de espacio entre las filas de la tabla */
#formacionTablaId td {
  padding: 10px;
}

/* Estilo para celdas de texto centrado */
.dataTables_wrapper td.text-center {
  text-align: center;
  width: 100px;
  /* Ajusta este valor según sea necesario */
}

/* Fila de elementos expirados */
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

.checkbox-container {
  display: flex;
  flex-wrap: wrap;
  /* Permite que los checkboxes se ajusten en filas si hay muchos */
  gap: 2px;
  /* Espacio entre cada checkbox */
}

.checkbox-label {
  margin-right: 5px;
  /* Espacio adicional entre cada checkbox */
}
</style>