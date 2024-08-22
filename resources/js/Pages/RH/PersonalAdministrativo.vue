<script setup>
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import { ref, onMounted, nextTick } from 'vue';
import Swal from 'sweetalert2';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import Mensaje from '../../Components/Mensaje.vue';
import RHLayout from '../../Layouts/RHLayout.vue';
import FormularioPersonal from '../../Components/RH/FormularioPersonal.vue';

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
    escolaridad: { type: Object },
    personal: { type: Object },
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
        title: 'Directivos registrados',
        extend: 'excelHtml5',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        exportOptions: {
            columns: [2, 3, 4, 5, 6]
        }
    },
    {
        title: 'Directivos registrados',
        extend: 'pdfHtml5',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [2, 3, 4, 5, 6]
        }
    },
    {
        title: 'Directivos registrados',
        extend: 'print',
        text: '<i class="fa-solid fa-print"></i> Imprimir', // Texto del botón
        className: 'bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        exportOptions: {
            columns: [2, 3, 4, 5, 6] // Índices de las columnas que deseas imprimir (por ejemplo, imprimir las columnas 0 y 2)
        }
    }
];

const columnas = [
    {
        data: null,
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="personal-checkboxes" data-id="${row.idPersonal}" ">`;
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
    { data: 'telEmergencia' },
    { data: 'NSS' },
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
        data: 'idEscolaridad',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const nivel = props.escolaridad.find(nivel => nivel.idEscolaridad === data);
            return nivel ? nivel.escolaridad : '';
        }
    },
    {
        data: 'antiguedad',
        render: function (data, type, row, meta) {
            return data + ' años';
        }
    },
    {
        data: 'domicilio', // Cambiar 'direccion' a 'domicilio'
        render: function (data, type, row, meta) {
            return data ? data : '';
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
    { data: 'totalDiasVac' },
    { data: 'diasVacRestantes' },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idPersonal}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        }
    },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({
    _method: 'DELETE',
});

const personalSeleccionados = ref([]);

var personalE = ({});
const abrirE = ($personall) => {
    personalE = $personall;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const togglePersonalSelection = (personal) => {
    if (personalSeleccionados.value.includes(personal)) {
        // Si el alumno ya está seleccionado, la eliminamos del array
        personalSeleccionados.value = personalSeleccionados.value.filter((p) => p !== personal);
    } else {
        // Si el alumno no está seleccionado, la agregamos al array
        personalSeleccionados.value.push(personal);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    // Cambio de estado del botón eliminar dependiendo de las materias seleccionadas
    if (personalSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

onMounted(() => {
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('personalTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('personal-checkboxes')) {
            const personalId = parseInt(checkbox.getAttribute('data-id'));
            if (props.personal) {
                const per = props.personal.find(per => per.idPersonal === personalId);
                if (per) {
                    togglePersonalSelection(per);
                } else {
                    console.log("No se tiene personal");
                }
            }
        }
    });

    // Manejar clic en el botón de editar
    $('#personalTablaId').on('click', '.editar-button', function () {
        const personalId = $(this).data('id');
        const per = props.personal.find(p => p.idPersonal === personalId);
        abrirE(per);
    });
});

const eliminarPersonal = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    // Obtener los nombres de las rutas seleccionadas
    const nombrePersonal = personalSeleccionados.value.map((personal) => personal.nombre_completo).join(', ');

    swal.fire({
        title: '¿Estas seguro que deseas eliminar al directivo seleccionado?',
        html: `Directivo seleccionado: ${nombrePersonal}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const personalE = personalSeleccionados.value.map((personal) => personal.idPersonal);
                const $personalIds = personalE.join(',');
                await form.post(route('rh.eliminarPersonal', $personalIds));
                personalSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (personalSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Personal eliminado correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Personal eliminado correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varias personales: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar al personal. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
};

</script>

<template>
    <RHLayout title="Personal" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Personal Administrativo</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-6"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Personal
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarPersonal">
                    <i class="fa fa-trash mr-2"></i>Borrar Personal
                </button>
            </div>
            <div>
                <div class="overflow-x-auto">
                    <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                        id="personalTablaId" name="personalTablaId" :columns="columnas" :data="personal" :options="{
                            responsive: false, autoWidth: false, dom: 'Bftrip', language: {
                                search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
                                info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                                infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                                infoFiltered: '(filtrado de un total de _MAX_ registros)',
                                lengthMenu: 'Mostrar _MENU_ registros',
                                paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
                            }, buttons: [botonesPersonalizados],
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
                                    colspan="11">INFORMACIÓN PERSONAL</th>
                                <th class="py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                    colspan="4">INFORMACIÓN LABORAL</th>
                                <th
                                    class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                                    INFORMACIÓN DE DOMICILIO
                                </th>
                                <th class="py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                    colspan="3">INFORMACIÓN DE DOCUMENTACIÓN</th>
                                <th class="py-2 px-4 bg-orange-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left"
                                    colspan="2">INFORMACIÓN VACACIONAL</th>
                                <th
                                    class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300">
                                </th>
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
                                    Teléfono de Emergencia
                                </th>
                                <th
                                    class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                    NSS
                                </th>
                                <th
                                    class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                    Escolaridad
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
                                    Antiguedad
                                </th>
                                <th
                                    class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                    Dirección
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
                                    class="py-2 px-4 bg-orange-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                    Total de Días
                                </th>
                                <th
                                    class="py-2 px-4 bg-orange-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                    Días Restantes
                                </th>
                                <th
                                    class="py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">

                                </th>
                            </tr>
                        </thead>
                    </DataTable>
                </div>
            </div>
        </div>
        <FormularioPersonal :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir Personal'" :op="'1'" :modal="'modalCreate'" :personal="props.personal"
            :escolaridad="props.escolaridad" :empresa="props.empresa"></FormularioPersonal>
    </RHLayout>
</template>

<style>
/* Estilo personalizado para centrar el texto en las celdas de la tabla */
#personalTablaId th {
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