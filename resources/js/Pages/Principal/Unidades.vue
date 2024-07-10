<script setup>
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import { DataTable } from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { useForm } from '@inertiajs/inertia-vue3';
import Select from 'datatables.net-select-dt';
import 'datatables.net-responsive-dt';
import jsZip from 'jszip';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
const pdfMake = require('pdfmake/build/pdfmake');
const pdfFonts = require('pdfmake/build/vfs_fonts');
import FormularioUnidades from '../../Components/Principal/FormularioUnidades.vue';
import FormularioAsignarOperador from '../../Components/Principal/FormularioAsignarOperador.vue';
import Mensaje from '../../Components/Mensaje.vue';
import FormularioQuitarOperador from '../../Components/Principal/FormularioQuitarOperador.vue';
import FormularioActualizarUnidades from '../../Components/Principal/FormularioActualizarUnidades.vue';

// Variables e inicializaciones necesarias para el datatable y el uso de generacion de 
// documentos
window.JSZip = jsZip;

// Cargar fuentes personalizadas
pdfMake.vfs = pdfFonts.pdfMake.vfs;

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    unidad: { type: Object },
    ruta: { type: Object },
    operador: { type: Object },
    directivo: { type: Object },
    operadoresDisp: { type: Object },
    unidadesDisp: { type: Object },
    unidadesConOperador: { type: Object },
    usuario: { type: Object },
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
        title: 'Unidades registradas',
        extend: 'excelHtml5',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        className: 'bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon',
        exportOptions: {
            columns: [2, 3, 4, 5, 6]
        }
    },
    {
        title: 'Unidades registradas',
        extend: 'pdfHtml5',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF', // Texto del botón
        className: 'bg-red-500 hover:bg-red-600 text-white py-1/2 px-3 rounded mb-2 jump-icon', // Clase de estilo
        orientation: 'landscape', // Configurar la orientación horizontal
        exportOptions: {
            columns: [2, 3, 4, 5, 6]
        }
    },
    {
        title: 'Unidades registradas',
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
            return "";
        }
    },
    {
        data: null,
        render: function (data, type, row, meta) {
            return `<input type="checkbox" class="unidades-checkboxes" data-id="${row.idUnidad}" ">`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    { data: 'numeroUnidad' },
    {
        data: 'idOperador',
        render: function (data, type, row, meta) {
            if (!data) { // Verifica si idOperador está vacío
                return '<span style="color: red;">Sin asignar</span>'; // Aplica color rojo si está vacío
            } else {
                // Modificación para mostrar la descripción del ciclo
                const oper = props.operador.find(oper => oper.idOperador === data);
                return oper ? oper.nombre_completo : '';
            }

        }
    },
    {
        data: 'idRuta',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const rut = props.ruta.find(rut => rut.idRuta === data);
            return rut ? rut.nombreRuta : '';
        }
    },
    {
        data: 'idDirectivo',
        render: function (data, type, row, meta) {
            // Modificación para mostrar la descripción del ciclo
            const dir = props.directivo.find(dir => dir.idDirectivo === data);
            return dir ? dir.nombre_completo : '';
        }
    },
    {
        data: null, render: function (data, type, row, meta) {
            return `<button class="editar-button" data-id="${row.idUnidad}" style="display: flex; justify-content: center;"><i class="fa fa-pencil"></i></button>`;
        }
    },
]

const mostrarModal = ref(false);
const mostrarModalAsigOper = ref(false);
const mostrarModalQuitarOper = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;

const form = useForm({
    _method: 'DELETE',
});
const unidadesSeleccionados = ref([]);

var unidadE = ({});
const abrirE = ($unidadess) => {
    unidadE = $unidadess;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalAsigOper = () => {
    mostrarModalAsigOper.value = false;
};

const cerrarModalQuitargOper = () => {
    mostrarModalQuitarOper.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleUnidadSelection = (unidad) => {
    if (unidadesSeleccionados.value.includes(unidad)) {
        // Si el alumno ya está seleccionado, la eliminamos del array
        unidadesSeleccionados.value = unidadesSeleccionados.value.filter((u) => u !== unidad);
    } else {
        // Si el alumno no está seleccionado, la agregamos al array
        unidadesSeleccionados.value.push(unidad);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    // Cambio de estado del botón eliminar dependiendo de las materias seleccionadas
    if (unidadesSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

onMounted(() => {
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('unidadesTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('unidades-checkboxes')) {
            const unidadId = parseInt(checkbox.getAttribute('data-id'));
            if (props.unidad) {
                const uni = props.unidad.find(uni => uni.idUnidad === unidadId);
                if (uni) {
                    toggleUnidadSelection(uni);
                } else {
                    console.log("No se tiene unidad");
                }
            }
        }
    });

    // Manejar clic en el botón de editar
    $('#unidadesTablaId').on('click', '.editar-button', function () {
        const unidadId = $(this).data('id');
        const uni = props.unidad.find(u => u.idUnidad === unidadId);
        abrirE(uni);
    });
});

const eliminarUnidades = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    // Obtener los nombres de las rutas seleccionadas
    const numerosUnidades = unidadesSeleccionados.value.map((unidad) => unidad.numeroUnidad).join(', ');

    swal.fire({
        title: '¿Estas seguro que deseas eliminar la(s) unidad(es) seleccionada(s)?',
        html: `Unidades seleccionadas: ${numerosUnidades}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const unidadE = unidadesSeleccionados.value.map((unidad) => unidad.idUnidad);
                const $unidadesIds = unidadE.join(',');
                await form.post(route('principal.eliminarUnidad', $unidadesIds));
                unidadesSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (unidadesSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Unidad(es) eliminado(s) correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Unidad(es) eliminado(s) correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varias unidades: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar las unidades. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
};

</script>

<template>
    <PrincipalLayout title="Formar Unidades" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Unidades</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Agregar Unidad
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarUnidades">
                    <i class="fa fa-trash mr-2"></i>Borrar Unidad
                </button>
                <button class="bg-cyan-500 hover:bg-cyan-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModalAsigOper = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-male" aria-hidden="true"></i> Asignar Operador
                </button>
                <button class="bg-orange-500 hover:bg-orange-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModalQuitarOper = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-male" aria-hidden="true"></i> Quitar Operador
                </button>
            </div>

            <div>
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="unidadesTablaId" name="unidadesTablaId" :columns="columnas" :data="unidad" :options="{
                        responsive: true, autoWidth: false, dom: 'Bftrip', language: {
                            search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
                            info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                            infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                            infoFiltered: '(filtrado de un total de _MAX_ registros)',
                            lengthMenu: 'Mostrar _MENU_ registros',
                            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Ultimo' },
                        }, buttons: [botonesPersonalizados],
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
                                Número de Unidad
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Operador
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Ruta
                            </th>
                            <th
                                class="py-2 px-4 bg-grey-lightest font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Dueño de la unidad
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <formulario-unidades :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir unidad'" :op="'1'" :modal="'modalCreate'" :unidad="props.unidad" :ruta="props.ruta"
            :operadoresDisp="props.operadoresDisp" :directivo="props.directivo"></formulario-unidades>
        <FormularioActualizarUnidades :show="mostrarModalE" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalE" :title="'Editar unidad'" :op="'2'" :modal="'modalEdit'" :unidad="unidadE"
            :ruta="props.ruta" :operador="props.operador" :operadoresDisp="props.operadoresDisp" :directivo="props.directivo">
        </FormularioActualizarUnidades>
        <FormularioAsignarOperador :show="mostrarModalAsigOper" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalAsigOper" :title="'Asignar operador'" :op="'1'" :modal="'modalCreate'"
            :unidad="props.unidad" :unidadesDisp="props.unidadesDisp" :operadoresDisp="props.operadoresDisp">
        </FormularioAsignarOperador>
        <FormularioQuitarOperador :show="mostrarModalQuitarOper" :max-width="maxWidth" :closeable="closeable"
            @close="cerrarModalQuitargOper" :title="'Quitar operador'" :op="'1'" :modal="'modalCreate'"
            :unidad="props.unidad" :unidadesConOperador="props.unidadesConOperador"
            :operadoresDisp="props.operadoresDisp">
        </FormularioQuitarOperador>
    </PrincipalLayout>
</template>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>