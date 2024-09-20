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
// Dentro del bloque <script setup>
const fechaActual = new Date().toLocaleDateString(); // Obtiene la fecha actual en formato de cadena
const diaSemana = obtenerDiaSemana(new Date().getDay()); // Obtiene el día de la semana actual
// Función para obtener el día de la semana según el número
function obtenerDiaSemana(diaNumero) {
  const diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
  return diasSemana[diaNumero];
}

const registrosFiltrados = computed(() => {
  return props.entrada.filter(entrada => {
    // Obtener la fecha del registro y convertirla al formato de fecha actual
    const fechaRegistro = new Date(entrada.fechaRegistro).toLocaleDateString();
    // Comparar la fecha del registro con la fecha actual
    return fechaRegistro === fechaActual;
  });
});

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
    // Mapa para los tipos de última corrida
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
      const ultimaCorrida = ultimaCorridaMap[row.idUnidad] || {};
      const castigos = props.castigo.filter(c => c.idUnidad === row.idUnidad && c.created_at.split('T')[0] === today);
      const rol = rolServicioMap[row.idUnidad] || {};

      const isEntradaValid = entrada.created_at?.split('T')[0] === today;
      const isUltimaCorridaValid = ultimaCorrida.created_at?.split('T')[0] === today;
      const isCastigoValid = castigos.length > 0;

      if (!acc[row.idUnidad]) {
        acc[row.idUnidad] = {
          ID: row.idUnidad,
          'Ruta': rutasMap[row.idRuta] || 'N/A',
          'Trab. Domingo': rolServicioMap[row.idUnidad] || 'Sin Asignar',
          'Unidad': unidadMap[row.idUnidad] || '',
          'Socio/Prestador': directivoMap[row.idDirectivo] || '',
          'Hr. Entrada': isEntradaValid ? formatTime(entrada.horaEntrada) || '' : '',
          'Tipo Entrada': isEntradaValid ? entrada.tipoEntrada || '' : '',
          'Extremo': isEntradaValid ? entrada.extremo || '' : '',
          'Hr. Corte': '',
          'Causa': '',
          'Hr. Regreso': '',
          'Tipo De Corrida': isUltimaCorridaValid ? (tipoUltimaCorridaMap[ultimaCorrida.tipoCorrida] || '') : '',
          'Hr. Inicio UC': isUltimaCorridaValid ? formatTime(ultimaCorrida.horaInicioUC) || '' : '',
          'Hr. Regreso UC': isUltimaCorridaValid ? formatTime(ultimaCorrida.horaFinUC) || '' : '',
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
    data: "idUnidad",
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
          return registrosUnidad[0].trabajaDomingo;
        } else {
          // Si no hay registros para esta idUnidad, devolver un valor por defecto
          return '<span style="color: red;">Sin asignar</span>';
        }
      } else {
        // Si no se encuentra la unidad, devolver un mensaje de error o un valor por defecto
        return 'Unidad no encontrada';
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
    data: "idUnidad",
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
          return horasEntrada;
        }
      }
      // Si no se encuentra la unidad o no hay registros de entrada para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: "idUnidad",
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
          return tiposEntrada;
        }
      }
      // Si no se encuentra la unidad o no hay registros de entrada para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: "idUnidad",
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
          return extremos;
        }
      }
      // Si no se encuentra la unidad o no hay registros de entrada para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: 'idUnidad',
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

          return horasCorteRows;
        }
      }
      // Si no se encuentra la unidad o no hay registros de corte para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: 'idUnidad',
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

          return causasCorteRows;
        }
      }
      // Si no se encuentra la unidad o no hay registros de corte para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: 'idUnidad',
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

          return horasRegresoRows;
        }
      }
      // Si no se encuentra la unidad o no hay registros de corte para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: "idUnidad",
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
            return tipoUltimaCorrida.tipoUltimaCorrida;
          }
        }
      }
      // Si no se encuentra la unidad o no hay registros de última corrida para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: "idUnidad",
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
          return horaInicioUC ? horaInicioUC.substring(0, 5) : '';
        }
      }
      // Si no se encuentra la unidad o no hay registros de última corrida para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: "idUnidad",
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
            return horaFinUC.substring(0, 5);
          }
        }
      }
      // Si no se encuentra la unidad o no hay registros de última corrida para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
    data: 'idUnidad',
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
          <div style="${borderStyle}">
            <div>${castigoRow.horaInicio}</div>
          </div>
        `;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    }
  },
  {
    data: 'idUnidad',
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
          <div style="${borderStyle}">
            <div>${castigoRow.horaFin}</div>
          </div>
        `;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    }
  },
  {
    data: 'idUnidad',
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
          return `<div style="${borderStyle}">${nombreCastigo}</div>`;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    }
  },
  {
    data: 'idUnidad',
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
          return `<div style="${borderStyle}">${observacion}</div>`;
        }).join(''); // Unir las filas en una sola cadena para mostrar en la celda
      }

      // Si no se encontraron castigos para la unidad actual, devolver cadena vacía
      return '';
    }
  },
  {
    data: 'idUnidad',
    render: function (data, type, row, meta) {
      const unidad = props.unidad.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const chofer = props.operador.find(chofer => chofer.idOperador === unidad.idOperador);
        if (chofer) {
          return chofer.nombre_completo;
        } else {
          return '<span style="color: red;">Sin asignar</span>'; // Aplica color rojo si no hay operador asignado
        }
      } else {
        return '';
      }
    }
  },
]

const mostrarModal = ref(false);
const mostrarModalCorte = ref(false);
const mostrarModalCastigo = ref(false);
const mostrarModalRegreso = ref(false);
const mostrarModalRegUC = ref(false);
const mostrarModalRegresoUC = ref(false);
const mostrarModalDomingo = ref(false);
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

const cerrarModalCorte = () => {
  mostrarModalCorte.value = false;
};

const cerrarModalCastigo = () => {
  mostrarModalCastigo.value = false;
};

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

</script>

<template>
  <ServicioLayout title="Formar Unidades" :usuario="props.usuario">
    <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6 ">
      <h2 class="font-bold text-center text-xl pt-0"> Formar Unidades</h2>
      <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-1.5"></div>

      <Mensaje />

      <div class="py-0 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0 mb-2">
        <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-check-circle" aria-hidden="true"></i> Registrar Entrada
        </button>
        <button class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalCorte = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-scissors" aria-hidden="true"></i> Registrar Corte
        </button>

        <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalRegreso = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-history" aria-hidden="true"></i> Registrar Regreso
        </button>

        <button class="bg-yellow-500 hover:bg-yellow-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalCastigo = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-bullhorn" aria-hidden="true"></i> Registrar castigo
        </button>

        <button class="bg-teal-500 hover:bg-teal-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalRegUC = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Registrar UC
        </button>
      </div>

      <div class="py-1 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0 mb-1">

        <button class="bg-teal-500 hover:bg-teal-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalRegresoUC = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Regreso UC
        </button>

        <button class="bg-teal-500 hover:bg-teal-500 text-white font-semibold py-2 px-4 rounded"
          @click="mostrarModalDomingo = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="fa fa-calendar" aria-hidden="true"></i> Rol Domingo
        </button>

        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded"
          @click="actualizarRolServicio"> <i class="fa fa-calendar" aria-hidden="true"></i> Actualizar Rol Servicio
        </button>

      </div>

      <div class="overflow-x-auto">
        <!-- el overflow-x-auto - es para poner la barra de dezplazamiento en horizontal automático -->
        <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
          id="formacionTablaId" name="formacionTablaId" :columns="columnas" :data="unidad" :options="{
            responsive: false, autoWidth: false, dom: 'Bftrip', language: {
              search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
              info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
              infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
              infoFiltered: '(filtrado de un total de _MAX_ registros)',
              /* lengthMenu: 'Mostrar _MENU_ registros',
              paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' }, */
            }, buttons: [botonesPersonalizados],

            paging: false,// Esto es para quitar la paginacion
            lengthMenu: [] // Este es donde se pone sin limite de filas
          }">
          <thead>
            <tr class="text-sm leading-normal border-b border-gray-300">
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th>
              <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="2">FECHA: {{ diaSemana + ', ' + fechaActual }}</th>
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th>
              <!-- Unidad -->
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th>
              <!-- Socio/Prestador -->
              <th
                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                colspan="3">ENTRADA</th>
              <th class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="3">CORTE</th> <!-- Columna combinada con título "Corte" -->
              <th class="py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="3">ÚLTIMAS CORRIDAS</th> <!-- Columna combinada con título "Corte" -->
              <th class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="4">CASTIGO</th> <!-- Columna combinada con título "Corte" -->
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th>
              <!-- operador -->
            </tr>
            <tr class="text-sm leading-normal border-b border-gray-300">
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                ID
              </th>
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Ruta
              </th>
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Trab. DOMINGO
              </th>
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Unidad
              </th>
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Socio / Prestador
              </th>
              <th class="py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hr. entrada
              </th>
              <th class="py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Tipo entrada
              </th>
              <th class="py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Extremo
              </th>
              <th class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hr. Corte
              </th>
              <th class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Causa
              </th>
              <th class="py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hr. regreso
              </th>
              <th class="py-2 px-4 bg-blue-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Tipo de Corrida
              </th>
              <th class="py-2 px-4 bg-blue-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hr. inicio
              </th>
              <th class="py-2 px-4 bg-blue-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hr. regreso
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hr. inicio
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Hr. finaliza
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Motivo
              </th>
              <th class="py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Otras observaciones
              </th>
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                Operador
              </th>
            </tr>
          </thead>
        </DataTable>
      </div>
    </div>
    <FormularioRegHoraEntrada :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
      :title="'Registrar hora de entrada'" :op="'1'" :modal="'modalCreate'" :entrada="props.entrada"
      :unidad="props.unidad" :unidadesConOperador="props.unidadesConOperador">
    </FormularioRegHoraEntrada>
    <FormularioRegCorte :show="mostrarModalCorte" :max-width="maxWidth" :closeable="closeable" @close="cerrarModalCorte"
      :title="'Registrar hora de corte'" :op="'1'" :modal="'modalCreate'" :corte="props.corte" :unidad="props.unidad"
      :unidadesConOperador="props.unidadesConOperador">
    </FormularioRegCorte>
    <FormularioRegRegreso :show="mostrarModalRegreso" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalRegreso" :title="'Registrar hora de regreso de corte'" :op="'1'" :modal="'modalCreate'"
      :unidad="props.unidad">
    </FormularioRegRegreso>
    <FormularioCastigo :show="mostrarModalCastigo" :max-width="maxWidth" :closeable="closeable"
      @close="cerrarModalCastigo" :title="'Registrar un castigo'" :op="'1'" :modal="'modalCreate'"
      :unidad="props.unidad" :castigo="props.castigo">
    </FormularioCastigo>
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


  </ServicioLayout>
</template>
<style>
/* Estilo personalizado para centrar el texto en las celdas de la tabla */
#formacionTablaId th {
  text-align: center !important;
}

.jump-icon:hover i {
  transition: transform 0.2s ease-in-out;
  transform: translateY(-3px);
}
</style>