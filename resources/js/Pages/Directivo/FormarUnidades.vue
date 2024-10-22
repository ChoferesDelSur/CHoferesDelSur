<script setup>
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import { ref, onMounted, computed, nextTick } from 'vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import { jsPDF } from 'jspdf';
import * as XLSX from 'xlsx';
import 'jspdf-autotable';
import DirectivoLayout from '../../Layouts/DirectivoLayout.vue';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
  message: { String, default: '' },
  color: { String, default: '' },
  ruta: { type: Object },
  unidades: { type: Object },
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
const nombreCompleto = `${props.usuario.apellidoP} ${props.usuario.apellidoM} ${props.usuario.nombre}`;
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
const numeroSemanaActual = obtenerNumeroSemana(new Date()) + 1;

// Función para obtener el día de la semana según el número
function obtenerDiaSemana(diaNumero) {
  const diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
  return diasSemana[diaNumero];
}

const columnas = [
  /* {
    data: null,
    render: function (data, type, row, meta) {
      return meta.row + 1
    }
  }, */
  {
    data: 'idUnidad',
    render: (data, type, row, meta) => {
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data); // Busca la unidad
      if (unidad) {
        // Verifica si el directivo de la unidad tiene el mismo nombre que el usuario
        const directivo = unidad.directivo;

        if (directivo && directivo.nombre_completo === nombreCompleto) {
          return unidad.ruta ? unidad.ruta.nombreRuta : ''; // Retorna el nombre de la ruta
        } else {
          return ''; // Si no coincide, devuelve un string vacío
        }
      } else {
        return ''; // Si no existe la unidad, devuelve un string vacío
      }
    }
  },
  {
    data: "idUnidad",
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidades
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Verifica si el directivo de la unidad tiene el mismo nombre que el usuario
        const directivo = unidad.directivo;

        if (directivo && directivo.nombre_completo === nombreCompleto) {
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
          return ''; // Si no coincide el nombre del directivo, devuelve un string vacío
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
      // Buscar la unidad correspondiente en props.unidades
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Verifica si el directivo de la unidad tiene el mismo nombre que el usuario
        const directivo = unidad.directivo;

        if (directivo && directivo.nombre_completo === nombreCompleto) {
          return unidad.numeroUnidad; // Retorna el número de la unidad si coincide
        } else {
          return ''; // Si no coincide, devuelve un string vacío
        }
      } else {
        return ''; // Si no se encuentra la unidad, devuelve un string vacío
      }
    }
  },
  {
    data: 'idUnidad',
    render: function (data, type, row, meta) {
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        const jefe = props.directivo.find(jefe => jefe.idDirectivo === unidad.idDirectivo);
        if (jefe) {
          return jefe.nombre_completo === nombreCompleto ? jefe.nombre_completo : '';
        }
        return jefe ? jefe.nombre_completo : '';
      } else {
        return '';
      }
    }
  },
  {
    data: "idUnidad",
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidades
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Verifica si el directivo de la unidad tiene el mismo nombre que el usuario
        const directivo = unidad.directivo;

        if (directivo && directivo.nombre_completo === nombreCompleto) {
          // Filtrar los registros de entrada correspondientes a la unidad y a la fecha actual
          const entradasHoy = props.entrada.filter(entrada => {
            return entrada.idUnidad === unidad.idUnidad &&
              new Date(entrada.created_at).toLocaleDateString() === fechaActual;
          });

          // Verificar si hay registros de entrada para la unidad y la fecha actual
          if (entradasHoy.length > 0) {
            // Construir una cadena con todas las horas de entrada para la unidad y la fecha actual
            const horasEntrada = entradasHoy.map(entrada => entrada.horaEntrada.substring(0, 5)).join(', ');
            return horasEntrada; // Retorna las horas de entrada
          }
        }
      }
      // Si no se encuentra la unidad, no hay registros de entrada para la fecha actual o el directivo no coincide, devolver un valor por defecto
      return '';
    }
  },
  {
    data: "idUnidad",
    render: function (data, type, row, meta) {
      // Buscar la unidad correspondiente en props.unidades
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);

      if (unidad) {
        // Verifica si el directivo de la unidad tiene el mismo nombre que el usuario
        const directivo = unidad.directivo;

        if (directivo && directivo.nombre_completo === nombreCompleto) {
          // Filtrar los registros de entrada correspondientes a la unidad y a la fecha actual
          const entradasHoy = props.entrada.filter(entrada => {
            return entrada.idUnidad === unidad.idUnidad &&
              new Date(entrada.created_at).toLocaleDateString() === fechaActual;
          });

          // Verificar si hay registros de entrada para la unidad y la fecha actual
          if (entradasHoy.length > 0) {
            // Construir una cadena con todos los tipos de entrada para la unidad y la fecha actual
            const tiposEntrada = entradasHoy.map(entrada => entrada.tipoEntrada).join(', ');
            return tiposEntrada; // Retorna los tipos de entrada
          }
        }
      }
      // Si no se encuentra la unidad o no hay registros de entrada para la fecha actual, devolver un valor por defecto
      return '';
    }
  },
  {
  data: "idUnidad",
  className: "text-center", // Clase que asegura el centrado
  render: function (data, type, row, meta) {
    // Buscar la unidad correspondiente en props.unidades
    const unidad = props.unidades.find(unidad => unidad.idUnidad === data);

    if (unidad) {
      // Verifica si el directivo de la unidad tiene el mismo nombre que el usuario
      const directivo = unidad.directivo;

      if (directivo && directivo.nombre_completo === nombreCompleto) {
        // Filtrar los registros de entrada correspondientes a la unidad y a la fecha actual
        const entradasHoy = props.entrada.filter(entrada => {
          return entrada.idUnidad === unidad.idUnidad &&
            new Date(entrada.created_at).toLocaleDateString() === fechaActual;
        });

        // Verificar si hay registros de entrada para la unidad y la fecha actual
        if (entradasHoy.length > 0) {
          // Construir una cadena con todos los valores de esExtremo para la unidad y la fecha actual
          const extremos = entradasHoy.map(entrada => entrada.extremo).join(', ');
          return extremos; // Retorna los extremos
        }
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
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
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
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
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
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
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
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
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
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
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
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
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
      // Buscar la unidad correspondiente en props.unidades
      const unidad = props.unidades.find(unidad => unidad.idUnidad === data);
      if (unidad) {
        // Verifica si el directivo de la unidad tiene el mismo nombre que el usuario
        const directivo = unidad.directivo;

        if (directivo && directivo.nombre_completo === nombreCompleto) {
          const chofer = props.operador.find(chofer => chofer.idOperador === unidad.idOperador);
          if (chofer) {
            return chofer.nombre_completo; // Retorna el nombre completo del chofer si hay uno asignado
          } else {
            return '<span style="color: red;">Sin asignar</span>'; // Aplica color rojo si no hay operador asignado
          }
        } else {
          return ''; // Si el directivo no coincide, devuelve un string vacío
        }
      } else {
        return ''; // Si no se encuentra la unidad, devuelve un string vacío
      }
    }
  }
]

const unidadesFiltradas = computed(() => {
  const filtradas = props.unidades.filter(unidad => unidad.directivo.nombre_completo === nombreCompleto);
  return filtradas;
});

</script>

<template>
  <DirectivoLayout title="Formar Unidades" :usuario="props.usuario">
    <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6 ">
      <h2 class="font-bold text-center text-xl pt-0"> Formación de Unidades</h2>
      <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-1.5"></div>

      <div class="overflow-x-auto relative">
        <!-- el overflow-x-auto - es para poner la barra de dezplazamiento en horizontal automático -->
        <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
          id="formacionTablaId" name="formacionTablaId" :columns="columnas" :data="unidadesFiltradas" :options="{
            responsive: false, autoWidth: false, language: {
              search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
              info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
              infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
              infoFiltered: '(filtrado de un total de _MAX_ registros)',
              /* lengthMenu: 'Mostrar _MENU_ registros',
              paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' }, */
            },

            paging: false,// Esto es para quitar la paginacion
            lengthMenu: [] // Este es donde se pone sin limite de filas
          }">
          <thead>
            <tr class="text-sm leading-normal border-b border-gray-300">
              <!-- <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
              </th> -->
              <!-- Celda vacía para la primera columna -->
              <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"
                colspan="2">FECHA: {{ diaSemana + ', ' + fechaActual }} -- SEMANA: {{ numeroSemanaActual }}</th>
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
              <!-- <th class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                ID
              </th> -->
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
  </DirectivoLayout>
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