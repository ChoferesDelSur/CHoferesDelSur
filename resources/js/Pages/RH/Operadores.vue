<script setup>
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import Swal from 'sweetalert2';
import { watch, ref, computed, onMounted, nextTick } from 'vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import Mensaje from '../../Components/Mensaje.vue';
import RHLayout from '../../Layouts/RHLayout.vue';
import FormularioOperadores from '../../Components/RH/FormularioOperadores.vue';
import FormularioActualizarOperadores from '../../Components/RH/FormularioActualizarOperadores.vue';
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

const exportarExcel = () => {
    nextTick(() => {
        // Obtener la instancia de DataTable
        const table = $('#operadoresTablaId').DataTable();
        const data = table.rows({ search: 'applied' }).data(); // Obtiene solo los datos filtrados

        // Mapear IDs a valores
        const empresaMap = new Map(props.empresa.map(emp => [emp.idEmpresa, emp.empresa]));
        const convenioPagoMap = new Map(props.convenioPago.map(con => [con.idConvenioPago, con.convenioPago]));
        const direccionMap = new Map(props.operador.map(direc => [direc.idOperador, direc.domicilio]));
        const constanciaSFMap = new Map([[1, 'SI'], [0, 'NO']]);
        const cursoSemoviMap = new Map([[1, 'SI'], [0, 'NO']]);
        const constanciaSemoviMap = new Map([[1, 'SI'], [0, 'NO']]);
        const cursoPsicologicoMap = new Map([[1, 'SI'], [0, 'NO']]);

        // Convertir los datos a formato JSON
        const jsonData = data.toArray().map(row => ({
            ID: row.idOperador,
            'Apellido Paterno': row.apellidoP,
            'Apellido Materno': row.apellidoM,
            Nombre: row.nombre,
            'Fecha Nacimiento': row.fechaNacimiento,
            Edad: `${row.edad} años`,
            'CURP': row.CURP,
            'RFC': row.RFC,
            'Teléfono': row.numTelefono,
            'NSS': row.NSS,
            'Tipo de Operador': props.tipoOperador.find(tipOp => tipOp.idTipoOperador === row.idTipoOperador)?.tipOperador || '',
            Estado: props.estado.find(estad => estad.idEstado === row.idEstado)?.estado || '',
            'Fecha Alta': row.fechaAlta,
            'Fecha Baja': row.fechaBaja,
            Empresa: empresaMap.get(row.idEmpresa) || '',
            'Convenio de Pago': convenioPagoMap.get(row.idConvenioPago) || '',
            Antiguedad: `${row.antiguedad} años`,
            Direccion: direccionMap.get(row.idDireccion) || '',
            'Num. Licencia': row.numLicencia,
            'Vigencia Licencia': row.vigenciaLicencia,
            'Número de INE': row.numINE,
            'Vigencia INE': row.vigenciaINE,
            'Constancia de Situación Fiscal': constanciaSFMap.get(row.constanciaSF) || '',
            'Curso SEMOVI': cursoSemoviMap.get(row.cursoSemovi) || '',
            'Constancia SEMOVI': constanciaSemoviMap.get(row.constanciaSemovi) || '',
            'Curso Psicologico': cursoPsicologicoMap.get(row.cursoPsicologico) || '',
            'Fecha Último Contrato': row.ultimoContrato,
            Jefe: props.directivo.find(jefe => jefe.idDirectivo === row.idDirectivo)?.nombre_completo || '',
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
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28] // Índices de las columnas que deseas imprimir 
        }
    }
];

const columnas = [
    {
        data: 'checkbox',
        
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="operador-checkboxes" data-id="${row.idOperador}" ">`;
        },
        visible: true,
    },
    {
        data: 'index', render: function (data, type, row, meta) { return meta.row + 1 },
        //title: 'No.',
        visible: true,
    },
    {
        data: 'apellidoP',
        title: 'APELLIDO PATERNO',
        visible: true,
    },
    {
        data: 'apellidoM',
        title: 'APELLIDO MATERNO',
        visible: true,
    },
    {
        data: 'nombre',
        title: 'NOMBRE',
        visible: true,
    },
    {
        data: 'fechaNacimiento',
        title: 'FECHA DE NACIMIENTO',
        visible: true,
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
        },
    },
    {
        data: 'edad',
        title: 'EDAD',
        visible: true,
        render: function (data, type, row, meta) {
            return data + ' años';
        },
    },
    {
        data: 'CURP',
        title: 'CURP',
        visible: true,
    },
    {
        data: 'RFC',
        title: 'RFC',
        visible: true,
    },
    {
        data: 'numTelefono',
        title: 'TELÉFONO',
        visible: true,
    },
    {
        data: 'NSS',
        title: 'NSS',
        visible: true,
    },
    {
        data: 'idTipoOperador',
        title: 'TIPO OPERADOR',
        visible: true,
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const tipOp = props.tipoOperador.find(tipOp => tipOp.idTipoOperador === data);
            return tipOp ? tipOp.tipOperador : '';
        },
    },
    {
        data: 'idEstado',
        title: 'ESTADO',
        visible: true,
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const estad = props.estado.find(estad => estad.idEstado === data);
            return estad ? estad.estado : '';
        },
    },
    {
        data: 'fechaAlta',
        title: 'FECHA ALTA',
        visible: true,
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
        },
    },
    {
        data: 'fechaBaja',
        title: 'FECHA BAJA',
        visible: true,
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
        },
    },
    {
        data: 'idEmpresa',
        title: 'EMPRESA',
        visible: true,
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const emp = props.empresa.find(emp => emp.idEmpresa === data);
            return emp ? emp.empresa : '';
        },
    },
    {
        data: 'idConvenioPago',
        title: 'CONVENIO DE PAGO',
        visible: true,
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const convenio = props.convenioPago.find(convenio => convenio.idConvenioPago === data);
            return convenio ? convenio.convenioPago : '';
        },
    },
    {
        data: 'antiguedad',
        title: 'ANTIGUEDAD',
        visible: true,
        render: function (data, type, row, meta) {
            return data + ' años';
        },
    },
    {
        data: 'domicilio', // Cambiar 'direccion' a 'domicilio'
        title: 'DOMICILIO',
        visible: true,
        render: function (data, type, row, meta) {
            return data ? data : '';
        },
    },
    {
        data: 'numLicencia',
        title: 'NÚM. LICENCIA',
        visible: true,
    },
    {
        data: 'vigenciaLicencia',
        title: 'VIGENCIA DE LICENCIA',
        visible: true,
        render: function (data, type, row, meta) {
            if (data) {
                const parts = data.split('-');
                const year = parts[0];
                const month = parts[1];
                const day = parts[2];
                const formattedDate = `${day}-${month}-${year}`;

                const fechaActual = new Date();
                const fechaVencimiento = new Date(year, month - 1, day);
                const diasRestantes = Math.ceil((fechaVencimiento - fechaActual) / (1000 * 60 * 60 * 24));
                let backgroundColor = 'transparent';

                if (fechaVencimiento < fechaActual) {
                    backgroundColor = '#f56565'; // Rojo para vencido
                } else if (diasRestantes <= 5) {
                    backgroundColor = '#ff9800'; // Naranja para advertencia
                }

                return `<div style="background-color: ${backgroundColor}; color: ${backgroundColor === '#f56565' ? '#ffffff' : 'inherit'}; width: 100%; height: 100%; box-sizing: border-box;">${formattedDate}</div>`;
            }
            return '';
        },
    },
    {
        data: 'numINE',
        title: 'NÚM. DE INE',
        visible: true,
    },
    {
        data: 'vigenciaINE',
        title: 'VIGENCIA DE INE',
        visible: true,
        render: function (data, type, row, meta) {
            if (data) {
                const anioActual = new Date().getFullYear();
                const estaVencido = data < anioActual;

                // Aplica el color de fondo en el div para no afectar el tamaño de la celda
                return `<div style="background-color: ${estaVencido ? '#f56565' : 'transparent'}; color: ${estaVencido ? '#ffffff' : 'inherit'}; width: 100%; height: 100%; box-sizing: border-box;">${data}</div>`;
            }
            return '';
        },
    },
    {
        data: 'constanciaSF',
        title: 'CONSTANCIA DE SITUACIÓN FISCAL',
        visible: true,
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        },
    },
    {
        data: 'cursoSemovi',
        title: 'CURSO SEMOVI',
        visible: true,
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        },
    },
    {
        data: 'constanciaSemovi',
        title: 'CONSTANCIA SEMOVI',
        visible: true,
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        },
    },
    {
        data: 'cursoPsicologico',
        title: 'CURSO PSICOLÓGICO',
        visible: true,
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        },
    },
    {
        data: 'ultimoContrato',
        title: 'ÚLTIMO CONTRATO',
        visible: true,
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
        },
    },
    {
        data: 'idDirectivo',
        title: 'JEFE',
        visible: true,
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const jefe = props.directivo.find(jefe => jefe.idDirectivo === data);
            return jefe ? jefe.nombre_completo : '';
        },
    },
    {
        title: 'EDITAR',
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idOperador}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        },
        visible: true,
    },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;
const operadoresSeleccionados = ref([]);

const form = useForm({
    _method: 'DELETE',
});

const col = ref([]);

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


const eliminarOperadores = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    // Obtener los nombres de las rutas seleccionadas
    const nombresOperadoores = operadoresSeleccionados.value.map((operador) => operador.nombre_completo).join(', ');

    swal.fire({
        title: '¿Estas seguro que deseas eliminar al operador seleccionado?',
        html: `Operador seleccionado: ${nombresOperadoores}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const operadorE = operadoresSeleccionados.value.map((operador) => operador.idOperador);
                const $operadoresIds = operadorE.join(',');
                await form.post(route('rh.eliminarOperador', $operadoresIds));
                operadoresSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (operadoresSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Operador(es) eliminado(s) correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Operador(es) eliminado(s) correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varias operadores: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar al operador. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
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

// Computed para filtrar columnas visibles
const filteredColumnas = computed(() => columnas.filter(col => col.visible));

// Función para actualizar la tabla
const updateTable = () => {
    nextTick(() => {
        const table = $('#operadoresTablaId').DataTable();

        // Actualiza la visibilidad de las columnas según el estado de `visible`
        filteredColumnas.value.forEach((col, index) => {
            table.column(index).visible(col.visible);
        });

        // Recarga la tabla con los datos actuales
        table.ajax.reload();
    });
};

const searchQuery = ref('');
/* const filters = ref({}); */

// Datos reactivos
const filters = ref({}); // Filtros por columna
//const filteredData = ref(props.data); // Datos de la tabla filtrados

// Método para aplicar los filtros
const applyFilters = () => {
    filteredData.value = props.operador.filter(row => {
        return Object.keys(filters.value).every(colKey => {
            // Si no hay filtro para la columna, no se aplica filtro
            if (!filters.value[colKey]) return true;

            // Compara el valor de la celda con el filtro
            const filterValue = filters.value[colKey].toLowerCase();
            return row[colKey] && row[colKey].toString().toLowerCase().includes(filterValue);
        });
    });
};

// Observador para aplicar los filtros cuando cambian
watch(filters, () => {
    applyFilters();
}, { deep: true });

const filteredData = computed(() => {
    return props.operador.filter(item => {
        // Filtro global
        const matchesGlobalSearch = Object.values(item).some(value =>
            String(value).toLowerCase().includes(searchQuery.value.toLowerCase())
        );

        // Filtros por columna
        const matchesColumnFilters = Object.keys(filters.value).every((key) => {
            if (filters.value[key]) {
                return String(item[key]).toLowerCase().includes(filters.value[key].toLowerCase());
            }
            return true;
        });

        return matchesGlobalSearch && matchesColumnFilters;
    });
});

</script>

<template>
    <RHLayout title="Operadores" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Operadores</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Operador
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarOperadores">
                    <i class="fa fa-trash mr-2"></i> Eliminar Operador
                </button>
            </div>

            <div class="overflow-x-auto">
                <!-- Checkbox para controlar columnas -->
                <div class="checkbox-container">
                    <label v-for="(col, index) in filteredColumnas" :key="index" class="checkbox-label">
                        <input type="checkbox" v-model="col.visible" @change="updateTable" />
                        {{ col.title }}
                    </label>
                </div>
            </div>
            
            <!-- Filtro por columna -->
            <div class="py-2 flex flex-wrap space-x-2">
                <div v-for="(columna, index) in filteredColumnas" :key="index">
                    <!-- Solo mostrar filtros para columnas a partir de la tercera (índice 2) y excluir la última columna -->
                    <input v-if="columna.visible && index >= 2 && index < filteredColumnas.length - 1"
                        v-model="filters[columna.data || 'sinData']"
                        :placeholder="'Filtrar por ' + (columna.title || 'sin título')"
                        class="mt-1 p-1 text-xs border rounded" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <!-- DataTable con visibilidad de columnas controlada por v-if -->
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="operadoresTablaId" name="operadoresTablaId" :columns="filteredColumnas" :data="filteredData"
                    :options="dataTableOptions">
                    <thead>
                        <tr class="text-sm leading-normal border-b border-gray-300">
                            <th v-for="col in filteredColumnas" :key="col.title" v-if="col.visible" :class="col.class"
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                                {{ col.title }}
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioOperadores :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir Operador'" :modal="'modalCreate'" :operador="props.operador"
            :tipoOperador="props.tipoOperador" :estado="props.estado" :incapacidad="props.incapacidad"
            :directivo="props.directivo" :empresa="props.empresa" :convenioPago="props.convenioPago">
        </FormularioOperadores>
        <FormularioActualizarOperadores :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalE" :title="'Editar Operador'" :modal="'modalEdit'" :tipoOperador="props.tipoOperador"
            :estado="props.estado" :directivo="props.directivo" :operador="operadorE" :empresa="props.empresa"
            :convenioPago="props.convenioPago" :direccion="props.direccion"></FormularioActualizarOperadores>
    </RHLayout>
</template>

<style>
/* Estilo personalizado para centrar el texto en las celdas de la tabla */
#operadoresTablaId th {
    text-align: center !important;
}

.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}

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