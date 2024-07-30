<script setup>
import $ from 'jquery';
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import Mensaje from '../../Components/Mensaje.vue';
import RHLayout from '../../Layouts/RHLayout.vue';
import FormularioOperadores from '../../Components/RH/FormularioOperadores.vue';
import FormularioActualizarOperadores from '../../Components/RH/FormularioActualizarOperadores.vue';

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
    /* direccionDireccion: { type: Object },
    operadorDireccion: { type: Object }, */
});

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
        extend: 'excelHtml5',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8]
        }
    },
    {
        title: 'Operadores registrados',
        extend: 'pdfHtml5',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        orientation: 'landscape', // Configurar la orientación horizontal
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8]
        }
    },
    {
        title: 'Operadores registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [2, 3, 4, 5, 6, 7, 8] // Índices de las columnas que deseas imprimir 
        }
    }
];

const columnas = [
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
        data: 'fechaNacimiento',
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
        data: 'edad',
        render: function (data, type, row, meta) {
            return data + ' años';
        }
    },
    { data: 'CURP' },
    { data: 'RFC' },
    { data: 'numTelefono' },
    { data: 'NSS' },
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
        data: 'idEmpresa',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const emp = props.empresa.find(emp => emp.idEmpresa === data);
            return emp ? emp.empresa : '';
        }
    },
    {
        data: 'idConvenioPago',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const convenio = props.convenioPago.find(convenio => convenio.idConvenioPago === data);
            return convenio ? convenio.convenioPago : '';
        }
    },
    {
        data: 'antiguedad',
        render: function (data, type, row, meta) {
            return data + ' años';
        }
    },
    {
        data: 'idDireccion',
        render: function (data, type, row, meta) {
            // Encuentra la dirección correspondiente en la lista de direcciones
            const dir = props.direccion.find(dir => dir.idDireccion === data);
            if (dir) {
                // Accede al asentamiento y municipio relacionados
                const asentamiento = dir.asentamiento || {};
                const municipio = asentamiento.municipio || {};
                const entidad = municipio.estados || {};
            
                return `${dir.calle} ${dir.numero}, ${asentamiento.tipo ? `${asentamiento.tipo} ` : ''}${asentamiento.asentamiento || ''}, ${asentamiento.municipio.municipio || ''}, ${entidad.entidad || ''}, C.P. ${asentamiento.codigo_postal.codigoPostal}`;
            }
            return '';
        }
    },
    { data: 'numLicencia' },
    {
        data: 'vigenciaLicencia',
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
        }
    },
    { data: 'numINE' },
    {
        data: 'vigenciaINE',
        render: function (data, type, row, meta) {
            if (data) {
                const anioActual = new Date().getFullYear();
                const estaVencido = data < anioActual;

                // Aplica el color de fondo en el div para no afectar el tamaño de la celda
                return `<div style="background-color: ${estaVencido ? '#f56565' : 'transparent'}; color: ${estaVencido ? '#ffffff' : 'inherit'}; width: 100%; height: 100%; box-sizing: border-box;">${data}</div>`;
            }
            return '';
        }
    },
    {
        data: 'constanciaSF',
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        }
    },
    {
        data: 'cursoSemovi',
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        }
    },
    {
        data: 'constanciaSemovi',
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        }
    },
    {
        data: 'cursoPsicologico',
        render: function (data, type, row, meta) {
            return data ? 'SI' : 'NO';
        }
    },
    {
        data: 'ultimoContrato',
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
        data: 'idDirectivo',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const jefe = props.directivo.find(jefe => jefe.idDirectivo === data);
            return jefe ? jefe.nombre_completo : '';
        }
    },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idOperador}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        }
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
        html: `Operadores seleccionados: ${nombresOperadoores}`,
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
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="operadoresTablaId" name="operadoresTablaId" :columns="columnas" :data="operador" :options="{
                        responsive: false, autoWidth: false, dom: 'Bftrip', language: {
                            search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
                            info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                            infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                            infoFiltered: '(filtrado de un total de _MAX_ registros)',
                            lengthMenu: 'Mostrar _MENU_ registros',
                            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
                        }, buttons: [botonesPersonalizados],
                        /* pageLength: -1 */ // Esto elimina el límite de registros por página
                    }">
                    <thead>
                        <tr class="text-sm leading-normal border-b border-gray-300">
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                            </th>
                            <th class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                colspan="9">INFORMACIÓN PERSONAL</th>
                            <th class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                colspan="7">INFORMACIÓN LABORAL</th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                                INFORMACIÓN DE DOMICILIO
                            </th>
                            <th class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                colspan="8">INFORMACIÓN DE DOCUMENTACIÓN</th>
                            <th class="py-2 px-4 bg-orange-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                colspan="2">INFORMACIÓN ADICIONAL</th>
                        </tr>
                        <tr class="text-sm leading-normal">
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                ID
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Paterno
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Apellido Materno
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Nombre
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha de Nacimiento
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Edad
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                CURP
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                RFC
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Teléfono
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                NSS
                            </th>
                            <th
                                class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Tipo de operador
                            </th>
                            <th
                                class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Estado
                            </th>
                            <th
                                class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha Alta
                            </th>
                            <th
                                class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha Baja
                            </th>
                            <th
                                class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Empresa
                            </th>
                            <th
                                class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Convenio de Pago
                            </th>
                            <th
                                class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Antiguedad
                            </th>
                            <th
                                class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Dirección
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Número de Licencia
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Vigencia de Licencia
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Número de INE
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Vigencia de INE
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Constancia de Situación Fiscal
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Curso SEMOVI
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Constancia SEMOVI
                            </th>
                            <th
                                class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Curso Psicologico
                            </th>
                            <th
                                class="py-2 px-4 bg-orange-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Fecha de Último Contrato
                            </th>
                            <th
                                class="py-2 px-4 bg-orange-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Jefe
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
            :convenioPago="props.convenioPago"></FormularioActualizarOperadores>
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
</style>