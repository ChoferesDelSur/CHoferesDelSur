<script setup>
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
import FormularioMovimiento from '../../Components/RH/FormularioMovimiento.vue';

DataTable.use(DataTablesLib);
DataTable.use(Select);

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    operador: { type: Object },
    operadoresAlta: { type: Object },
    operadoresBaja: { type: Object },
    estado: { type: Object },
    directivo: { type: Object },
    movimiento: { type: Object },
    usuario: { type: Object },
    tipoMovimiento: { type: Object },
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
            return `<input type="checkbox" class="movimiento-checkboxes" data-id="${row.idMovimiento}" ">`;
        }
    },
    {
        data: null, render: function (data, type, row, meta) { return meta.row + 1 }
    },
    { data: 'fechaMovimiento' },
    {
        data: 'idEstado',
        render: function (data, type, row, meta) {
            const mov = props.estado.find(mov => mov.idEstado === data);
            return mov ? mov.estado : '';
        }
    },
    {
        data: 'idOperador',
        render: function (data, type, row, meta) {
            const chofer = props.operador.find(chofer => chofer.idOperador === data);
            return chofer ? chofer.nombre_completo : '';
        }
    },
    {
        data: 'idDirectivo',
        render: function (data, type, row, meta) {
            const jefe = props.directivo.find(jefe => jefe.idDirectivo === data);
            return jefe ? jefe.nombre_completo : '';
        }
    },
    {
        data: 'idTipoMovimiento',
        render: function (data, type, row, meta) {
            const tipoMov = props.tipoMovimiento.find(tipoMov => tipoMov.idTipoMovimiento === data);
            return tipoMov ? tipoMov.tipoMovimiento : '';
        }
    },
]

const mostrarModal = ref(false);
const mostrarModalE = ref(false);
const maxWidth = 'xl';
const closeable = true;
const movimientosSeleccionados = ref([]);

const form = useForm({
    _method: 'DELETE',
});

var movimientoE = ({});
const abrirE = ($movimientoss) => {
    movimientoE = $movimientoss;
    mostrarModalE.value = true;
}

const cerrarModal = () => {
    mostrarModal.value = false;
};

const cerrarModalE = () => {
    mostrarModalE.value = false;
};

const toggleMovimientoSelection = (movimiento) => {
    if (movimientosSeleccionados.value.includes(movimiento)) {
        // Si el operador ya está seleccionado, la eliminamos del array
        movimientosSeleccionados.value = movimientosSeleccionados.value.filter((m) => m !== movimiento);
    } else {
        // Si el operador no está seleccionado, la agregamos al array
        movimientosSeleccionados.value.push(movimiento);
    }
    // Llamado del botón de eliminar para cambiar su estado deshabilitado
    const botonEliminar = document.getElementById("eliminarABtn");
    if (movimientosSeleccionados.value.length > 0) {
        botonEliminar.removeAttribute("disabled");
    } else {
        botonEliminar.setAttribute("disabled", "");
    }
};

onMounted(() => {
    // Agrega un escuchador de eventos fuera de la lógica de Vue
    document.getElementById('movimientosTablaId').addEventListener('click', (event) => {
        const checkbox = event.target;
        if (checkbox.classList.contains('movimiento-checkboxes')) {
            const movimientoId = parseInt(checkbox.getAttribute('data-id'));
            if (props.movimiento) {
                const mov = props.movimiento.find(mov => mov.idMovimiento === movimientoId);
                if (mov) {
                    toggleMovimientoSelection(mov);
                } else {
                    console.log("No se tiene movimiento");
                }
            }
        }
    });
});

const eliminarMovimientos = () => {
    const swal = Swal.mixin({
        buttonsStyling: true
    })
    // Construir el mensaje con los datos del operador y el estado
    const mensaje = movimientosSeleccionados.value.map((movimiento) => {
        const operador = props.operador.find(oper => oper.idOperador === movimiento.idOperador);
        const estado = props.estado.find(est => est.idEstado === movimiento.idEstado);

        return `${operador ? operador.nombre_completo : 'Desconocido'} ~ ${estado ? estado.estado : 'Desconocido'}`;
    }).join('<br>');

    swal.fire({
        title: '¿Estas seguro que deseas eliminar el movimiento seleccionado?',
        html: `Movimiento seleccionado:<br>${mensaje}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const movimientoE = movimientosSeleccionados.value.map((movimiento) => movimiento.idMovimiento);
                const $movimientoIds = movimientoE.join(',');
                await form.post(route('rh.eliminarMovimiento', $movimientoIds));
                movimientosSeleccionados.value = [];
                const botonEliminar = document.getElementById("eliminarABtn");
                if (movimientosSeleccionados.value.length > 0) {
                    botonEliminar.removeAttribute("disabled");
                } else {
                    botonEliminar.setAttribute("disabled", "");
                }
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Movimiento(s) eliminado(s) correctamente',
                    icon: 'success'
                });

                // Almacenar el mensaje en la sesión flash de Laravel
                window.flash = { message: 'Movimiento(s) eliminado(s) correctamente', color: 'green' };

            } catch (error) {
                console.log("Error al eliminar varios movimientos: " + error);
                // Mostrar mensaje de error si es necesario
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al eliminar el movimiento. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        }
    });
};

</script>

<template>

    <RHLayout title="Movimientos" :usuario="props.usuario">
        <div class="mt-0 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Movimientos</h2>
            <div class="my-1"></div> <!-- Espacio de separación -->
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"></div>

            <Mensaje />

            <div class="py-3 flex flex-col md:flex-row md:items-start md:space-x-3 space-y-3 md:space-y-0">
                <button class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                    @click="mostrarModal = true" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="fa fa-plus mr-2"></i>Realizar Movimiento
                </button>
                <button id="eliminarABtn" disabled
                    class="bg-red-500 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded"
                    @click="eliminarMovimientos">
                    <i class="fa fa-trash mr-2"></i> Eliminar Movimiento
                </button>
            </div>

            <div class="overflow-x-auto">
                <DataTable class="w-full table-auto text-sm display nowrap stripe compact cell-border order-column"
                    id="movimientosTablaId" name="movimientosTablaId" :columns="columnas" :data="movimiento" :options="{
                        responsive: false, autoWidth: false, dom: 'Bftrip', language: {
                            search: 'Buscar', zeroRecords: 'No hay registros para mostrar',
                            info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                            infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                            infoFiltered: '(filtrado de un total de _MAX_ registros)',
                        }, buttons: [botonesPersonalizados],
                        paging: false,// Esto es para quitar la paginacion
                    }">
                    <thead>
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
                                Fecha de Movimiento
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Movimiento
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Operador
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Socio / Prestador
                            </th>
                            <th
                                class="py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-light border-b border-grey-light">
                                Tipo de Movimiento
                            </th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
        <FormularioMovimiento :show="mostrarModal" :max-width="maxWidth" :closeable="closeable" @close="cerrarModal"
            :title="'Añadir Movimento'" :modal="'modalCreate'" :operador="props.operador" :estado="props.estado"
            :directivo="props.directivo" :movimiento="props.movimiento" :tipoMovimiento="props.tipoMovimiento"
            :operadoresAlta="props.operadoresAlta" :operadoresBaja="props.operadoresBaja">
        </FormularioMovimiento>
    </RHLayout>
</template>

<style>
/* Estilo personalizado para centrar el texto en las celdas de la tabla */
#movimientosTablaId th {
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